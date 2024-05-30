<?php

return [
    'google' => [
        'apiKey' => env('GOOGLE_BOOKS_API_KEY'),
    ],
    'amazon' => [
        'associateId' => env('AMAZON_ASSOCIATE_ID'),
        'secretKey' => env('AMAZON_SECRET_KEY'),
        'accessKey' => env('AMAZON_ACCESS_KEY'),
    ],
];
