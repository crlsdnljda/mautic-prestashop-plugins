<?php

return [
    'name'        => 'PrestashopEcommerce',
    'description' => 'Prestashop Ecommerce integration for Mautic 6',
    'version'     => '1.0.0',
    'author'      => 'Mautic Community',

    'services' => [
        'integrations' => [
            'mautic.integration.prestashopecommerce' => [
                'class' => \MauticPlugin\PrestashopEcommerceBundle\Integration\PrestashopEcommerceIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                    'mautic.config_integration',
                ],
            ],
        ],
        'forms' => [
            'mautic.form.type.prestashopecommerce_config' => [
                'class' => \MauticPlugin\PrestashopEcommerceBundle\Form\Type\ConfigType::class,
            ],
        ],
        'helpers' => [
            'mautic.prestashopecommerce.config' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Integration\Support\ConfigSupport::class,
                'arguments' => [
                    'mautic.integrations.helper',
                ],
            ],
        ],
        'other' => [
            'mautic.prestashopecommerce.command.importproducts' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Command\PrestashopEcommerceImportProductsCommand::class,
                'arguments' => [
                    'mautic.product.model.product',
                    'mautic.prestashopecommerce.config',
                ],
                'tag' => 'console.command',
            ],
            'mautic.prestashopecommerce.command.importcarts' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Command\PrestashopEcommerceImportCartsCommand::class,
                'arguments' => [
                    'mautic.cart.model.cart',
                    'mautic.product.model.product',
                    'mautic.lead.model.lead',
                    'mautic.prestashopecommerce.config',
                ],
                'tag' => 'console.command',
            ],
            'mautic.prestashopecommerce.command.importorders' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Command\PrestashopEcommerceImportOrdersCommand::class,
                'arguments' => [
                    'mautic.cart.model.cart',
                    'mautic.order.model.order',
                    'mautic.product.model.product',
                    'mautic.lead.model.lead',
                    'mautic.prestashopecommerce.config',
                ],
                'tag' => 'console.command',
            ],
            'mautic.prestashopecommerce.command.importcustomers' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Command\PrestashopEcommerceImportCustomersCommand::class,
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.prestashopecommerce.config',
                ],
                'tag' => 'console.command',
            ],
            'mautic.prestashopecommerce.command.importproductcategories' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Command\PrestashopEcommerceImportProductCategoriesCommand::class,
                'arguments' => [
                    'mautic.productcategory.model.productcategory',
                    'mautic.product.model.product',
                    'mautic.prestashopecommerce.config',
                ],
                'tag' => 'console.command',
            ],
        ],
    ],
];
