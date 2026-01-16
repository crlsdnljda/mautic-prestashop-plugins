<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Command;

use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\PrestashopEcommerceBundle\Integration\PrestashopEcommerceIntegration;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebserviceException;
use MauticPlugin\PrestashopEcommerceBundle\Services\PrestaShopWebservice;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'mautic:prestashopecommerce:importcustomers',
    description: 'Import customers from PrestaShop API'
)]
class PrestashopEcommerceImportCustomersCommand extends Command
{
    private LeadModel $leadModel;
    private PrestashopEcommerceIntegration $prestashopEcommerceIntegration;
    private IntegrationHelper $integrationHelper;

    public function __construct(
        LeadModel $leadModel,
        PrestashopEcommerceIntegration $prestashopEcommerceIntegration,
        IntegrationHelper $integrationHelper
    ) {
        parent::__construct();
        $this->leadModel = $leadModel;
        $this->prestashopEcommerceIntegration = $prestashopEcommerceIntegration;
        $this->integrationHelper = $integrationHelper;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('Importing Customers');

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

                $output->writeln('Processing shop: ' . $shop_url);
                $webServiceShop = new PrestaShopWebservice($shop_url, $api['apiKey'], false);

                // Import registered customers
                $xml = $webServiceShop->get([
                    'resource' => 'customers',
                    'display' => 'full'
                ]);
                $customers = $xml->customers;

                $customerCount = 0;
                foreach ($customers->children() as $customer) {
                    $lead = $this->leadModel->getRepository()->getLeadsByFieldValue(
                        'customerid',
                        (int) $customer->id
                    );

                    if ($lead) {
                        $lead = array_values($lead)[0];
                    } else {
                        $lead = $this->leadModel->getEntity();
                        $lead->addUpdatedField('customerid', (int) $customer->id);
                        $lead->setEmail((string) $customer->email);
                    }
                    $lead->setFirstname((string) $customer->firstname);
                    $lead->setLastname((string) $customer->lastname);

                    $this->leadModel->saveEntity($lead);
                    $customerCount++;
                }
                $output->writeln("Imported $customerCount customers");

                // Import guests
                $xml = $webServiceShop->get([
                    'resource' => 'guests',
                    'display' => 'full'
                ]);
                $guests = $xml->guests;

                $guestCount = 0;
                foreach ($guests->children() as $guest) {
                    $lead = $this->leadModel->getRepository()->getLeadsByFieldValue(
                        'guestid',
                        (int) $guest->id
                    );

                    if ($lead) {
                        $lead = array_values($lead)[0];
                    } else {
                        $lead = $this->leadModel->getEntity();
                        $lead->addUpdatedField('guestid', (int) $guest->id);
                    }
                    $lead->setFirstname('Guest');
                    $this->leadModel->saveEntity($lead);
                    $guestCount++;
                }
                $output->writeln("Imported $guestCount guests");
            }

            $output->writeln('<info>Import completed successfully</info>');
            return Command::SUCCESS;

        } catch (PrestaShopWebserviceException $ex) {
            $output->writeln('<error>' . $ex->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
