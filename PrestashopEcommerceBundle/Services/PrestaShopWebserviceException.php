<?php

namespace MauticPlugin\PrestashopEcommerceBundle\Services;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(false)]
class PrestaShopWebserviceException extends \Exception
{
}
