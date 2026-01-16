<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Command;

use MauticPlugin\EcommerceBundle\Model\ProductCategoryModel;
use MauticPlugin\EcommerceBundle\Model\ProductModel;
use MauticPlugin\PrestashopEcommerceBundle\Integration\Support\ConfigSupport;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebserviceException;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebservice;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'mautic:prestashopecommerce:importproductcategories',
    description: 'Import product categories from PrestaShop API'
)]
class PrestashopEcommerceImportProductCategoriesCommand extends Command
{
    private ProductCategoryModel $productCategoryModel;
    private ProductModel $productModel;
    private ConfigSupport $configSupport;

    public function __construct(
        ProductCategoryModel $productCategoryModel,
        ProductModel $productModel,
        ConfigSupport $configSupport
    ) {
        parent::__construct();
        $this->productCategoryModel = $productCategoryModel;
        $this->productModel = $productModel;
        $this->configSupport = $configSupport;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('Importing Product Categories');

        try {
            if (!$this->configSupport->isPublished()) {
                $output->writeln('<error>PrestashopEcommerce integration is not enabled</error>');
                return Command::FAILURE;
            }

            if (!$this->configSupport->isConfigured()) {
                $output->writeln('<error>API URL or API Key not configured</error>');
                return Command::FAILURE;
            }

            $webService = new PrestaShopWebservice(
                $this->configSupport->getApiUrl(),
                $this->configSupport->getApiKey(),
                false
            );

            $xml = $webService->get([
                'resource' => 'shops',
                'display' => '[id,name]',
                'filter[active]' => '1'
            ]);
            $shops = $xml->shops;

            foreach ($shops->children() as $shop) {
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
                $webServiceShop = new PrestaShopWebservice($shop_url, $this->configSupport->getApiKey(), false);

                $xml = $webServiceShop->get([
                    'resource' => 'languages',
                    'display' => '[id,iso_code,active]'
                ]);
                $languages = $xml->languages;

                foreach ($languages->children() as $language) {
                    $xml = $webServiceShop->get([
                        'resource' => 'categories',
                        'display' => 'full',
                        'language' => (int) $language->id
                    ]);
                    $categories = $xml->categories->children();

                    $categoryCount = 0;
                    foreach ($categories as $category) {
                        $existingCategory = $this->productCategoryModel->getCategoryById(
                            (int) $category->id,
                            $shopId,
                            (string) $language->iso_code
                        );

                        if (empty($existingCategory)) {
                            $entity = $this->productCategoryModel->getEntity();
                        } else {
                            $entity = $this->productCategoryModel->getEntity((int) $existingCategory['id']);
                        }

                        $categoryName = isset($category->name->language) ? (string) $category->name->language : (string) $category->name;

                        $entity->setName($categoryName);
                        $entity->setCategoryId((int) $category->id);
                        $entity->setShopId($shopId);
                        $entity->setLanguage((string) $language->iso_code);
                        $entity->setParentId((int) $category->id_parent);
                        $entity->setLevelDepth((int) $category->level_depth);
                        $entity->setIsRoot((int) $category->level_depth === 0);

                        $this->productCategoryModel->saveEntity($entity);
                        $categoryCount++;

                        $output->writeln('  - ' . $categoryName);
                    }
                    $output->writeln("Imported $categoryCount categories for language " . $language->iso_code);
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
