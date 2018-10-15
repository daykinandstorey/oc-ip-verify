<?php return [
    'plugin' => [
        'name' => 'IP Verification',
        'description' => "Authorise login of backend user's by their IP address",
        'menu_description' => "Manage the IP verification settings",
    ],

    'errors' => [
        'verification' => 'Unable to verify IP address with the provided token',
        'token' => 'The token is invalid or has expired',
        'not_verified' => 'Your IP address has not been verified. Please check your email for further instruction.'
    ],

    'flash' => [
        'verified' => 'Thank you, the IP has been verified and you can now login.',
    ]
];