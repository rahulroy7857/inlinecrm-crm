<?php

return [
    'application_fee' => env('STUDENT_APPLICATION_FEE', 500),

    'payment' => [
        'gateway' => env('STUDENT_PAYMENT_GATEWAY', 'razorpay'),
        'test_mode' => env('STUDENT_PAYMENT_TEST_MODE', true),
        'razorpay_key' => env('RAZORPAY_KEY', ''),
        'razorpay_secret' => env('RAZORPAY_SECRET', ''),
    ],
];
