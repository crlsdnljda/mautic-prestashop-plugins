<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Services;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class PrestaShopWebserviceException extends \Exception
{
}
