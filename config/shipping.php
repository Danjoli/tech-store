<?php

return [
    'driver' => env('SHIPPING_DRIVER', 'sandbox'),
    'sandbox_flat_rate' => (float) env('SHIPPING_SANDBOX_FLAT_RATE', 0),
];
