<?php

return [
    'name'        => 'Ecommerce',
    'description' => 'Ecommerce entities for Mautic (Products, Carts, Orders)',
    'version'     => '1.0.0',
    'author'      => 'Mautic Community',

    'services' => [
        'models' => [
            'mautic.product.model.product' => [
                'class'     => \MauticPlugin\EcommerceBundle\Model\ProductModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'mautic.cart.model.cart' => [
                'class'     => \MauticPlugin\EcommerceBundle\Model\CartModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'mautic.order.model.order' => [
                'class'     => \MauticPlugin\EcommerceBundle\Model\OrderModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'mautic.productcategory.model.productcategory' => [
                'class'     => \MauticPlugin\EcommerceBundle\Model\ProductCategoryModel::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
    ],
];
