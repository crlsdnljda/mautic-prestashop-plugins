<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Command;

use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\EcommerceBundle\Model\CartModel;
use MauticPlugin\EcommerceBundle\Model\ProductModel;
use MauticPlugin\PrestashopEcommerceBundle\Integration\PrestashopEcommerceIntegration;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebserviceException;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebservice;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'mautic:prestashopecommerce:importcarts',
    description: 'Import carts from PrestaShop API'
)]
class PrestashopEcommerceImportCartsCommand extends Command
{
    private CartModel $cartModel;
    private ProductModel $productModel;
    private LeadModel $leadModel;
    private PrestashopEcommerceIntegration $prestashopEcommerceIntegration;
    private IntegrationHelper $integrationHelper;

    public function __construct(
        CartModel $cartModel,
        ProductModel $productModel,
        LeadModel $leadModel,
        PrestashopEcommerceIntegration $prestashopEcommerceIntegration,
        IntegrationHelper $integrationHelper
    ) {
        parent::__construct();
        $this->cartModel = $cartModel;
        $this->productModel = $productModel;
        $this->leadModel = $leadModel;
        $this->prestashopEcommerceIntegration = $prestashopEcommerceIntegration;
        $this->integrationHelper = $integrationHelper;
    }

    protected function configure(): void
    {
        $this->addOption('full', null, InputOption::VALUE_NONE, 'Full Import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('Importing Carts');

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
                $cartsCount = ['Created' => 0, 'Updated' => 0];

                $xml = $webService->get([
                    'resource' => 'languages',
                    'display' => '[id,iso_code]'
                ]);
                $languages = [];
                foreach ($xml->languages->children() as $language) {
                    $languages[(int) $language->id] = (string) $language->iso_code;
                }

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
                    'resource' => 'carts',
                    'display' => 'full',
                    'filter[id_shop]' => $shopId
                ]);
                $carts = $xml->carts;

                foreach ($carts->children() as $cart) {
                    $existingCart = $this->cartModel->getCartById((int) $cart->id, (int) $cart->id_shop);

                    if (empty($existingCart)) {
                        $entity = $this->cartModel->getEntity();
                        $countType = 'Created';
                    } else {
                        $entity = $this->cartModel->getEntity((int) $existingCart['id']);
                        $countType = 'Updated';
                    }

                    $fullImport = $input->getOption('full');
                    $needsUpdate = $fullImport || ($entity->getDateModified() === null) ||
                                   ($entity->getDateModified() < new \DateTime((string) $cart->date_upd));

                    if ($needsUpdate) {
                        $entity->setShopId((int) $cart->id_shop);
                        $entity->setCartId((int) $cart->id);
                        $entity->setCustomerId((int) $cart->id_customer > 0 ? (int) $cart->id_customer : null);
                        $entity->setGuestId((int) $cart->id_guest > 0 ? (int) $cart->id_guest : null);
                        $entity->setLanguage($languages[(int) $cart->id_lang] ?? 'en');
                        $entity->setCartDate(new \DateTime((string) $cart->date_add));
                        $entity->setDateUpdPrestashop(new \DateTime((string) $cart->date_upd));

                        $this->cartModel->saveEntity($entity);
                        $cartsCount[$countType]++;

                        // Import cart lines
                        $this->cartModel->deleteCartLinesByCart($entity);

                        if (isset($cart->associations->cart_rows)) {
                            foreach ($cart->associations->cart_rows->children() as $cart_row) {
                                $cartLine = $this->cartModel->createCartLine();
                                $cartLine->setCart($entity);
                                $cartLine->setProductId((int) $cart_row->id_product);
                                $cartLine->setProductAttributeId((int) $cart_row->id_product_attribute);
                                $cartLine->setQuantity((int) $cart_row->quantity);
                                $this->cartModel->saveCartLine($cartLine);
                            }
                        }
                    }
                }

                $output->writeln("  Created: {$cartsCount['Created']}, Updated: {$cartsCount['Updated']}");
            }

            $output->writeln('<info>Import completed successfully</info>');
            return Command::SUCCESS;

        } catch (PrestaShopWebserviceException $ex) {
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
