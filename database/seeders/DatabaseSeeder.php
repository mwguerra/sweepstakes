<?php

namespace Database\Seeders;

use App\Models\Sweepstakes;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UsersTableSeeder::class);

        User::factory()->create([
            'name' => 'Marcelo',
            'email' => 'mwguerra@gmail.com',
            'password' => bcrypt('12341234'),
            'email_verified_at' => now(),
        ]);

        Sweepstakes::create([
            'title' => Str::random(100),
            'slug' => Str::slug(Str::random(30)),
            'description' => Str::random(100),
            'draw_time' => now()->addMinutes(100000000),
            'timezone' => 'America/Sao_Paulo',
            'is_over' => false,
            'is_winner_notified' => false,
            'winner_email_message' => Str::random(100),
        ]);
    }
}
