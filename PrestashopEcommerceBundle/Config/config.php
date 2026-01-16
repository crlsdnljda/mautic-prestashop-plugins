<?php

return [
    'name'        => 'PrestashopEcommerce',
    'description' => 'Prestashop Ecommerce integration for Mautic 6',
    'version'     => '1.0.0',
    'author'      => 'Mautic Community',

    'services' => [
        'integrations' => [
            'mautic.integration.prestashopecommerce' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Integration\PrestashopEcommerceIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
        'helpers' => [
            'mautic.prestashopecommerce.config' => [
                'class'     => \MauticPlugin\PrestashopEcommerceBundle\Integration\Support\ConfigSupport::class,
                'arguments' => [
                    'mautic.helper.integration',
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
