<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Database\Factories\WalletFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()
            ->count(10)
            ->create();

        foreach($users as $user) {
            $wallet = Wallet::factory()->create(['user_id' => $user->id]);
            $userIds = $users->pluck('id');
            $othersUserIds = array_filter($users->pluck('id')->toArray(), function ($item) use ($user) {
                return $item !== $user->id;
            });

            Transaction::factory()
                ->count(rand(1,200))
                ->create([
                    'wallet_id' => $wallet->id,
                    'user_id_to' => $userIds->random() != $user->id ? $userIds->random() : $othersUserIds[0],
                ]);
        }
    }
}
