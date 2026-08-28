<?php

return [
    'driver' => env('PAYMENTS_DRIVER', 'sandbox'),
    'mode' => env('PAYMENTS_MODE', 'sandbox'),
    'webhook_secret' => env('PAYMENTS_WEBHOOK_SECRET'),
];
