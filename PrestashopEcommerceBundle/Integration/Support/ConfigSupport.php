<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Integration\Support;

use Mautic\PluginBundle\Helper\IntegrationHelper;

class ConfigSupport
{
    private IntegrationHelper $integrationHelper;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function getApiUrl(): ?string
    {
        return $this->getConfigValue('apiUrl');
    }

    public function getApiKey(): ?string
    {
        return $this->getConfigValue('apiKey');
    }

    public function getShopId(): int
    {
        return 1;
    }

    public function getLanguage(): string
    {
        return 'es';
    }

    public function isConfigured(): bool
    {
        return !empty($this->getApiUrl()) && !empty($this->getApiKey());
    }

    public function isPublished(): bool
    {
        try {
            $integration = $this->integrationHelper->getIntegrationObject('PrestashopEcommerce');
            if (!$integration) {
                return false;
            }
            return $integration->getIntegrationSettings()->getIsPublished();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getConfigValue(string $key): ?string
    {
        try {
            $integration = $this->integrationHelper->getIntegrationObject('PrestashopEcommerce');
            if (!$integration) {
                return null;
            }
            $keys = $integration->getDecryptedApiKeys();
            return $keys[$key] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
