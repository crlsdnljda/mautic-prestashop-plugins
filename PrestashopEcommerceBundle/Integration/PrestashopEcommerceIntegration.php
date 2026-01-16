<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\PrestashopEcommerceBundle\Form\Type\ConfigType;

class PrestashopEcommerceIntegration extends BasicIntegration implements BasicInterface, ConfigFormInterface
{
    use ConfigurationTrait;

    public const NAME = 'PrestashopEcommerce';
    public const DISPLAY_NAME = 'PrestaShop Ecommerce';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/PrestashopEcommerceBundle/Assets/img/prestashop.png';
    }

    public function getConfigFormName(): ?string
    {
        return ConfigType::class;
    }

    public function getConfigFormContentTemplate(): ?string
    {
        return '@PrestashopEcommerce/Integration/form.html.twig';
    }
}
