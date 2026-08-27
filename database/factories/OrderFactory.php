<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'number' => 'TS-'.fake()->unique()->numerify('######'),
            'status' => OrderStatus::PENDING_PAYMENT,
            'subtotal' => 100,
            'shipping_amount' => 0,
            'total_amount' => 100,
            'shipping_address' => [
                'recipient_name' => fake()->name(), 'phone' => '11999999999',
                'zip' => '01001-000', 'street' => 'Rua Teste', 'number' => '1',
                'complement' => null, 'district' => 'Centro', 'city' => 'São Paulo', 'state' => 'SP',
            ],
        ];
    }
}
