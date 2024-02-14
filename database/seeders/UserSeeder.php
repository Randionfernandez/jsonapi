<?php

namespace Database\Seeders;


use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $admin = User::factory()
            ->create([
//                'doi' => fake()->unique()->dni(),  // doi es nullable
                'name' => 'Rafael',
//                'apellidos' => 'Andión',
//                'fechalta' => "2010-05-01",
                'email' => 'randion@cifpfbmoll.eu',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

        Article::factory()
//            ->hasAttached($admin)
            ->create();

        $invitado = User::factory()
            ->has(Article::factory(2))
            ->create([
                'email' => 'randionfernandez@gmail.com',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

        User::factory(4)
            ->has(Article::factory())
            ->create();

        Article::factory(4)
            ->create();
    }
}
