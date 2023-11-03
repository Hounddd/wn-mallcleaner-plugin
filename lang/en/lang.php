<?php

return [
    'plugin' => [
        'name' => 'Mall Cleaner',
        'description' => 'Clean OFFLINE.Mall plugin data.',
    ],
    'permissions' => [
        'some_permission' => 'Some permission',
    ],

    'actions' => [
        'empty_carts' => [
            'label' => 'Empty carts',
            'comment' => 'Delete carts with no products',
        ],
        'unpaid_orders' => [
            'label' => 'Unpaid orders',
            'comment' => 'Delete unpaid orders',
        ],
    ],
];
