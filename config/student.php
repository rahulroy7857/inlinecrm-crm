<?php

return [
    'application_fee' => env('STUDENT_APPLICATION_FEE', 500),

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
        'gateway' => env('STUDENT_PAYMENT_GATEWAY', 'razorpay'),
        'test_mode' => env('STUDENT_PAYMENT_TEST_MODE', true),
        'razorpay_key' => env('RAZORPAY_KEY', ''),
        'razorpay_secret' => env('RAZORPAY_SECRET', ''),
    ],
];
