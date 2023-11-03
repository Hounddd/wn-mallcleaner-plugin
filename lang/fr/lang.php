<?php

return [
    'plugin' => [
        'name' => 'Mall Néttoyage',
        'description' => 'Nettoyer les données du plugin OFFLINE.Mall.',
    ],
    'permissions' => [
        'some_permission' => 'Some permission',
    ],

    'actions' => [
        'empty_carts' => [
            'label' => 'Paniers vides',
            'comment' => 'Supprimer les paniers sans produits',
        ],
        'unpaid_orders' => [
            'label' => 'Commandes non réglées',
            'comment' => 'Supprimer les commandes non réglées',
        ],
    ],
];
