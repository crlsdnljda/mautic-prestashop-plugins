<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class PrestashopEcommerceIntegration extends AbstractIntegration
{
    public function getName(): string
    {
        return 'PrestashopEcommerce';
    }

    public function getDisplayName(): string
    {
        return 'PrestaShop Ecommerce';
    }

    public function getAuthenticationType(): string
    {
        return 'keys';
    }

    public function getRequiredKeyFields(): array
    {
        return [
            'apiUrl' => 'mautic.plugin.prestashop.api_url',
            'apiKey' => 'mautic.plugin.prestashop.api_key',
        ];
    }

    public function getSecretKeys(): array
    {
        return ['apiKey'];
    }

    public function getIcon(): string
    {
        return 'plugins/PrestashopEcommerceBundle/Assets/img/prestashop.png';
    }
}
