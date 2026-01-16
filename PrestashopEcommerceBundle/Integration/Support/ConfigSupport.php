<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Integration\Support;

use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use MauticPlugin\PrestashopEcommerceBundle\Integration\PrestashopEcommerceIntegration;

class ConfigSupport
{
    private IntegrationsHelper $integrationsHelper;

    public function __construct(IntegrationsHelper $integrationsHelper)
    {
        $this->integrationsHelper = $integrationsHelper;
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
        return (int) ($this->getConfigValue('shopId') ?? 1);
    }

    public function getLanguage(): string
    {
        return $this->getConfigValue('language') ?? 'es';
    }

    public function isConfigured(): bool
    {
        return !empty($this->getApiUrl()) && !empty($this->getApiKey());
    }

    public function isPublished(): bool
    {
        try {
            $integration = $this->integrationsHelper->getIntegration(PrestashopEcommerceIntegration::NAME);
            $config = $integration->getIntegrationConfiguration();
            return $config->getIsPublished();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getConfigValue(string $key): ?string
    {
        try {
            $integration = $this->integrationsHelper->getIntegration(PrestashopEcommerceIntegration::NAME);
            $config = $integration->getIntegrationConfiguration();
            $apiKeys = $config->getApiKeys();
            return $apiKeys[$key] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
