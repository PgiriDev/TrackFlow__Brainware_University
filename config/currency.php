<?php

return [
    'default' => 'INR',

    'currencies' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'locale' => 'en-US',
        ],
        'INR' => [
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'code' => 'INR',
            'locale' => 'en-IN',
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'locale' => 'de-DE',
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'code' => 'GBP',
            'locale' => 'en-GB',
        ],
        'JPY' => [
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'code' => 'JPY',
            'locale' => 'ja-JP',
        ],
    ],

    // Exchange rates relative to INR (INR = 1.00)
    'rates' => [
        'INR' => 1.00,
        'USD' => 0.011,
        'EUR' => 0.0093,
        'GBP' => 0.0081,
        'JPY' => 1.69,
    ],
];
