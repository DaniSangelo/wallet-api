<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arr = ['credit', 'debit', 'withdraw'];

        return [
            'wallet_id' => Wallet::factory(),
            'amount' => fake()->randomFloat(2, 0, 5000),
            'type' => $arr[rand(0, 2)],
            'user_id_to' => User::factory(),
        ];
    }
}
