<?php

return [
    'application_fee' => env('STUDENT_APPLICATION_FEE', 0),

    'gst_percent' => 18,

    /*
    | Fixed registration fee plans (base amount + GST). Non-refundable.
    | Total = base * (1 + gst_percent/100)
    */
    'registration_fee_plans' => [
        'plan_a' => [
            'label' => 'Plan A',
            'base' => 10000,
            'refundable' => false,
        ],
        'plan_b' => [
            'label' => 'Plan B',
            'base' => 20000,
            'refundable' => false,
        ],
        'plan_c' => [
            'label' => 'Plan C',
            'base' => 30000,
            'refundable' => false,
        ],
        'mbbs' => [
            'label' => 'MBBS Fee',
            'base' => 50000,
            'refundable' => false,
        ],
    ],

    'document_types' => [
        'photo' => 'Passport Photo',
        'aadhar' => 'Aadhar Card',
        'marksheet' => 'Marksheet / Certificate',
    ],

    'required_documents' => ['photo', 'aadhar', 'marksheet'],

    'upload' => [
        'max_size_kb' => 5120,
        'allowed_mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
    ],

    'payment' => [
        'gateway' => env('STUDENT_PAYMENT_GATEWAY', 'cashfree'),
        'test_mode' => env('STUDENT_PAYMENT_TEST_MODE', true),
        'cashfree_key' => env('CASHFREE_KEY', ''),
        'cashfree_secret' => env('CASHFREE_SECRET', ''),
    ],

    'default_ledger_account_id' => env('STUDENT_DEFAULT_LEDGER_ACCOUNT_ID'),
];
