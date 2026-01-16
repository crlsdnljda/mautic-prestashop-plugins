<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Command;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\EcommerceBundle\Model\ProductModel;
use MauticPlugin\PrestashopEcommerceBundle\Integration\PrestashopEcommerceIntegration;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebserviceException;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebservice;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'mautic:prestashopecommerce:importproducts',
    description: 'Import products from PrestaShop API'
)]
class PrestashopEcommerceImportProductsCommand extends Command
{
    private ProductModel $productModel;
    private PrestashopEcommerceIntegration $prestashopEcommerceIntegration;
    private IntegrationHelper $integrationHelper;

    public function __construct(
        ProductModel $productModel,
        PrestashopEcommerceIntegration $prestashopEcommerceIntegration,
        IntegrationHelper $integrationHelper
    ) {
        parent::__construct();
        $this->productModel = $productModel;
        $this->prestashopEcommerceIntegration = $prestashopEcommerceIntegration;
        $this->integrationHelper = $integrationHelper;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('Importing Products');

        try {
            $integrationObject = $this->integrationHelper->getIntegrationObject('PrestashopEcommerce');
            if (!$integrationObject || !$integrationObject->getIntegrationSettings()->getIsPublished()) {
                $output->writeln('<error>PrestashopEcommerce integration is not enabled</error>');
                return Command::FAILURE;
            }

            $api = $this->prestashopEcommerceIntegration->decryptApiKeys(
                $integrationObject->getIntegrationSettings()->getApiKeys()
            );

            if (empty($api['apiUrl']) || empty($api['apiKey'])) {
                $output->writeln('<error>API URL or API Key not configured</error>');
                return Command::FAILURE;
            }

            $webService = new PrestaShopWebservice($api['apiUrl'], $api['apiKey'], false);

            $xml = $webService->get([
                'resource' => 'shops',
                'display' => '[id,name]',
                'filter[active]' => '1'
            ]);
            $shops = $xml->shops;

            foreach ($shops->children() as $shop) {
                $taxProduct = [];
                $shopId = (int) $shop->id;

                $xml = $webService->get([
                    'resource' => 'shop_urls',
                    'display' => '[domain,physical_uri,virtual_uri]',
                    'filter[active]' => '1',
                    'filter[main]' => '1',
                    'filter[id_shop]' => $shopId
                ]);
                $shop_url = $xml->shop_urls->shop_url;
                $shop_url = 'http://' . $shop_url->domain . $shop_url->physical_uri . $shop_url->virtual_uri;

                $output->writeln('Processing shop: ' . $shop->name);
                $webServiceShop = new PrestaShopWebservice($shop_url, $api['apiKey'], false);

                $xml = $webServiceShop->get([
                    'resource' => 'languages',
                    'display' => '[id,iso_code,active]'
                ]);
                $languages = $xml->languages;

                foreach ($languages->children() as $language) {
                    $xml = $webServiceShop->get([
                        'resource' => 'products',
                        'display' => '[id,active,reference,price,date_upd,name,description,description_short,id_tax_rules_group]',
                        'language' => (int) $language->id
                    ]);
                    $products = $xml->products;

                    $productCount = 0;
                    foreach ($products->children() as $product) {
                        if ((int) $product->active == 1) {
                            // Get tax rate
                            if (!isset($taxProduct[(int) $product->id_tax_rules_group])) {
                                try {
                                    $xml = $webServiceShop->get([
                                        'resource' => 'tax_rules',
                                        'display' => '[id,id_tax]',
                                        'filter[id_tax_rules_group]' => (int) $product->id_tax_rules_group
                                    ]);
                                    $taxRules = $xml->tax_rules;
                                    if (count($taxRules->children()) > 0) {
                                        $taxRule = $taxRules->children()[0];
                                        $xml = $webServiceShop->get([
                                            'resource' => 'taxes',
                                            'display' => '[id,rate]',
                                            'filter[id]' => (int) $taxRule->id_tax
                                        ]);
                                        $taxes = $xml->taxes;
                                        if (count($taxes->children()) > 0) {
                                            $tax = $taxes->children()[0];
                                            $taxProduct[(int) $product->id_tax_rules_group] = (float) $tax->rate;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    $taxProduct[(int) $product->id_tax_rules_group] = 0;
                                }
                            }

                            $existingProduct = $this->productModel->getProductById(
                                (int) $product->id,
                                (int) $shop->id,
                                0,
                                (string) $language->iso_code
                            );

                            if (empty($existingProduct)) {
                                $entity = $this->productModel->getEntity();
                            } else {
                                $entity = $this->productModel->getEntity((int) $existingProduct[0]['id']);
                            }

                            $productName = isset($product->name->language) ? (string) $product->name->language : (string) $product->name;
                            $shortDesc = isset($product->description_short->language) ? (string) $product->description_short->language : (string) $product->description_short;
                            $longDesc = isset($product->description->language) ? (string) $product->description->language : (string) $product->description;

                            $entity->setName($productName);
                            $entity->setShortDescription($shortDesc);
                            $entity->setLongDescription($longDesc);
                            $entity->setProductId((int) $product->id);
                            $entity->setShopId((int) $shop->id);
                            $entity->setProductAttributeId(0);
                            $entity->setPrice((float) $product->price);
                            $entity->setLanguage((string) $language->iso_code);
                            $entity->setReference((string) $product->reference);
                            $entity->setTaxPercent($taxProduct[(int) $product->id_tax_rules_group] ?? 0);
                            $entity->setUrl($shop_url . 'index.php?controller=product&id_product=' . $product->id);

                            $this->productModel->saveEntity($entity);
                            $productCount++;

                            $output->writeln('  - ' . $productName);
                        }
                    }
                    $output->writeln("Imported $productCount products for language " . $language->iso_code);
                }
            }

            $output->writeln('<info>Import completed successfully</info>');
            return Command::SUCCESS;

        } catch (PrestaShopWebserviceException $ex) {
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
