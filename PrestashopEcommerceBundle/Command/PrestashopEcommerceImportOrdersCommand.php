<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Command;

use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\EcommerceBundle\Model\CartModel;
use MauticPlugin\EcommerceBundle\Model\OrderModel;
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
    name: 'mautic:prestashopecommerce:importorders',
    description: 'Import orders from PrestaShop API'
)]
class PrestashopEcommerceImportOrdersCommand extends Command
{
    private CartModel $cartModel;
    private OrderModel $orderModel;
    private ProductModel $productModel;
    private LeadModel $leadModel;
    private PrestashopEcommerceIntegration $prestashopEcommerceIntegration;
    private IntegrationHelper $integrationHelper;

    public function __construct(
        CartModel $cartModel,
        OrderModel $orderModel,
        ProductModel $productModel,
        LeadModel $leadModel,
        PrestashopEcommerceIntegration $prestashopEcommerceIntegration,
        IntegrationHelper $integrationHelper
    ) {
        parent::__construct();
        $this->cartModel = $cartModel;
        $this->orderModel = $orderModel;
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
        $output->writeln('Importing Orders');

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
                $ordersCount = ['Created' => 0, 'Updated' => 0];

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
                    'resource' => 'orders',
                    'display' => 'full',
                    'filter[id_shop]' => $shopId
                ]);
                $orders = $xml->orders;

                foreach ($orders->children() as $order) {
                    $existingOrder = $this->orderModel->getOrderById((int) $order->id, (int) $order->id_shop);

                    if (empty($existingOrder)) {
                        $entity = $this->orderModel->getEntity();
                        $countType = 'Created';
                    } else {
                        $entity = $this->orderModel->getEntity((int) $existingOrder['id']);
                        $countType = 'Updated';
                    }

                    $fullImport = $input->getOption('full');
                    $needsUpdate = $fullImport || ($entity->getDateModified() === null) ||
                                   ($entity->getDateModified() < new \DateTime((string) $order->date_upd));

                    if ($needsUpdate) {
                        $entity->setShopId((int) $order->id_shop);
                        $entity->setOrderId((int) $order->id);
                        $entity->setCartId((int) $order->id_cart);
                        $entity->setCustomerId((int) $order->id_customer);
                        $entity->setLanguage($languages[(int) $order->id_lang] ?? 'en');
                        $entity->setReference((string) $order->reference);
                        $entity->setPayment((string) $order->payment);
                        $entity->setTotalPaid((float) $order->total_paid);
                        $entity->setTotalPaidWithTax((float) $order->total_paid_tax_incl);
                        $entity->setTotalProducts((float) $order->total_products);
                        $entity->setTotalProductsWithTax((float) $order->total_products_wt);
                        $entity->setTotalShipping((float) $order->total_shipping);
                        $entity->setTotalShippingWithTax((float) $order->total_shipping_tax_incl);
                        $entity->setTotalDiscounts((float) $order->total_discounts);
                        $entity->setTotalDiscountsWithTax((float) $order->total_discounts_tax_incl);
                        $entity->setCurrentState((int) $order->current_state);
                        $entity->setOrderDate(new \DateTime((string) $order->date_add));
                        $entity->setDateUpdPrestashop(new \DateTime((string) $order->date_upd));

                        $this->orderModel->saveEntity($entity);
                        $ordersCount[$countType]++;

                        // Import order rows
                        $this->orderModel->deleteOrderRowsByOrder($entity);

                        if (isset($order->associations->order_rows)) {
                            foreach ($order->associations->order_rows->children() as $order_row) {
                                $orderRow = $this->orderModel->createOrderRow();
                                $orderRow->setOrder($entity);
                                $orderRow->setProductId((int) $order_row->product_id);
                                $orderRow->setProductAttributeId((int) $order_row->product_attribute_id);
                                $orderRow->setProductName((string) $order_row->product_name);
                                $orderRow->setQuantity((int) $order_row->product_quantity);
                                $orderRow->setUnitPrice((float) $order_row->unit_price_tax_excl);
                                $orderRow->setUnitPriceWithTax((float) $order_row->unit_price_tax_incl);
                                $orderRow->setTotalPrice((float) $order_row->total_price_tax_excl);
                                $orderRow->setTotalPriceWithTax((float) $order_row->total_price_tax_incl);
                                $this->orderModel->saveOrderRow($orderRow);
                            }
                        }

                        $output->writeln('  - Order #' . $order->reference);
                    }
                }

                $output->writeln("  Created: {$ordersCount['Created']}, Updated: {$ordersCount['Updated']}");
            }

            $output->writeln('<info>Import completed successfully</info>');
            return Command::SUCCESS;

        } catch (PrestaShopWebserviceException $ex) {
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
