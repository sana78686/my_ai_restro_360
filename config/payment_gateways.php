<?php
// config/payment_gateways.php
return [

    'default' => env('DEFAULT_PAYMENT_GATEWAY', 'stripe'),

    'available_gateways' => ['stripe', 'paypal', 
    // 'razorpay',
    // 'applepay', 'klarna', 'googlepay', 'bank_transfer', 'cash_on_delivery'
],
    'gateways' => [
        'stripe' => [
            'display_name' => 'Stripe',
            'category' => 'payment_processor',
            'credentials_schema' => [
                'public_key' => [
                    'type' => 'text',
                    'label' => 'Publishable Key',
                    'required' => true,
                    'encrypted' => false,
                ],
                'secret_key' => [
                    'type' => 'password', 
                    'label' => 'Secret Key',
                    'required' => true,
                    'encrypted' => true,
                ],
                'webhook_secret' => [
                    'type' => 'password',
                    'label' => 'Webhook Secret',
                    'required' => false,
                    'encrypted' => true,
                ],
            ],
            'config_schema' => [
                'statement_descriptor' => [
                    'type' => 'text',
                    'label' => 'Statement Descriptor',
                    'default' => null,
                ],
                'capture_method' => [
                    'type' => 'select',
                    'label' => 'Capture Method',
                    'options' => ['automatic', 'manual'],
                    'default' => 'automatic',
                ],
            ],
            'supported_features' => [
                'recurring_payments',
                'refunds', 
                'international_payments',
                '3d_secure',
                'save_payment_methods',
            ],
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
            'webhook_events' => [
                'payment_succeeded',
                'payment_failed',
                'subscription_created',
                'subscription_updated',
                'subscription_cancelled',
            ],
        ],
        
        'paypal' => [
            'display_name' => 'PayPal',
            'category' => 'payment_processor',
            'credentials_schema' => [
                'client_id' => [
                    'type' => 'text',
                    'label' => 'Client ID',
                    'required' => true,
                    'encrypted' => false,
                ],
                'client_secret' => [
                    'type' => 'password',
                    'label' => 'Client Secret', 
                    'required' => true,
                    'encrypted' => true,
                ],
                'webhook_id' => [
                    'type' => 'text',
                    'label' => 'Webhook ID',
                    'required' => false,
                    'encrypted' => false,
                ],
            ],
            'config_schema' => [
                'mode' => [
                    'type' => 'select',
                    'label' => 'Environment',
                    'options' => ['sandbox', 'live'],
                    'default' => 'sandbox',
                ],
                'intent' => [
                    'type' => 'select',
                    'label' => 'Payment Intent',
                    'options' => ['CAPTURE', 'AUTHORIZE'],
                    'default' => 'CAPTURE',
                ],
            ],
            'supported_features' => [
                'recurring_payments',
                'refunds',
                'international_payments',
            ],
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        ],
        
        // 'razorpay' => [
        //     'display_name' => 'Razorpay',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'key_id' => [
        //             'type' => 'text',
        //             'label' => 'Key ID',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //         'key_secret' => [
        //             'type' => 'password',
        //             'label' => 'Key Secret',
        //             'required' => true,
        //             'encrypted' => true,
        //         ],
        //         'webhook_secret' => [
        //             'type' => 'password',
        //             'label' => 'Webhook Secret',
        //             'required' => false,
        //             'encrypted' => true,
        //         ],
        //     ],
        //     // ... similar structure
        // ],
        
        // 'applepay' => [
        //     'display_name' => 'Apple Pay',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'merchant_id' => [
        //             'type' => 'text',
        //             'label' => 'Merchant ID',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //         'merchant_domain' => [
        //             'type' => 'text',
        //             'label' => 'Merchant Domain',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //         'merchant_certificate' => [
        //             'type' => 'textarea',
        //             'label' => 'Merchant Certificate (PEM format)',
        //             'required' => true,
        //             'encrypted' => true,
        //         ],
        //     ],
        //     'supported_features' => [
        //         'one_time_payments',
        //         'recurring_payments',
        //     ],
        //     'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        // ],

        // 'googlepay' => [
        //     'display_name' => 'Google Pay',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'merchant_id' => [
        //             'type' => 'text',
        //             'label' => 'Merchant ID',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //         'merchant_name' => [
        //             'type' => 'text',
        //             'label' => 'Merchant Name',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //     ],
        //     'supported_features' => [
        //         'one_time_payments',
        //         'recurring_payments',
        //     ],
        //     'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        // ],

        // 'klarna' => [
        //     'display_name' => 'Klarna',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'merchant_id' => [
        //             'type' => 'text',
        //             'label' => 'Merchant ID',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //         'shared_secret' => [
        //             'type' => 'password',
        //             'label' => 'Shared Secret',
        //             'required' => true,
        //             'encrypted' => true,
        //         ],
        //     ],
        //     'supported_features' => [
        //         'one_time_payments',
        //         'recurring_payments',
        //     ],
        //     'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD', 'AUD'],
        // ],  
        // 'mollie' => [
        //     'display_name' => 'Mollie',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'api_key' => [
        //             'type' => 'password',
        //             'label' => 'API Key',
        //             'required' => true,
        //             'encrypted' => true,
        //         ],
        //     ],
        //     // ... similar structure
        // ],
        
        // 'paystack' => [
        //     'display_name' => 'Paystack',
        //     'category' => 'payment_processor', 
        //     'credentials_schema' => [
        //         'public_key' => [
        //             'type' => 'text',
        //             'label' => 'Public Key',
        //             'required' => true,
        //             'encrypted' => false,
        //         ],
        //         'secret_key' => [
        //             'type' => 'password',
        //             'label' => 'Secret Key',
        //             'required' => true,
        //             'encrypted' => true,
        //         ],
        //         'webhook_secret' => [
        //             'type' => 'password',
        //             'label' => 'Webhook Secret',
        //             'required' => false,
        //             'encrypted' => true,
        //         ],
        //     ],
        //     // ... similar structure
        // ],
        
        // ADD NEW GATEWAYS HERE - NO DATABASE CHANGES NEEDED!
        // 'flutterwave' => [
        //     'display_name' => 'Flutterwave',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'public_key' => ['type' => 'text', 'required' => true],
        //         'secret_key' => ['type' => 'password', 'required' => true],
        //         'encryption_key' => ['type' => 'password', 'required' => false],
        //     ],
        // ],
        
        // 'instamojo' => [
        //     'display_name' => 'Instamojo',
        //     'category' => 'payment_processor',
        //     'credentials_schema' => [
        //         'client_id' => ['type' => 'text', 'required' => true],
        //         'client_secret' => ['type' => 'password', 'required' => true],
        //     ],
        // ],
        
        // 'ccavenue' => [
        //     'display_name' => 'CCAvenue',
        //     'category' => 'payment_processor', 
        //     'credentials_schema' => [
        //         'merchant_id' => ['type' => 'text', 'required' => true],
        //         'access_code' => ['type' => 'password', 'required' => true],
        //         'working_key' => ['type' => 'password', 'required' => true],
        //     ],
        // ],
        
        // 'bank_transfer' => [
        //     'display_name' => 'Bank Transfer',
        //     'category' => 'bank_transfer',
        //     'credentials_schema' => [
        //         'bank_name' => ['type' => 'text', 'required' => true],
        //         'account_number' => ['type' => 'text', 'required' => true],
        //         'account_holder' => ['type' => 'text', 'required' => true],
        //         'ifsc_code' => ['type' => 'text', 'required' => true],
        //         'branch' => ['type' => 'text', 'required' => false],
        //     ],
        //     'supported_features' => ['manual_verification'],
        // ],
        
        // 'cash_on_delivery' => [
        //     'display_name' => 'Cash on Delivery',
        //     'category' => 'offline_payment',
        //     'credentials_schema' => [], // No credentials needed
        //     'supported_features' => ['manual_verification'],
        // ],
    ],
];