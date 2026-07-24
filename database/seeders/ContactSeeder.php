<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Contact::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->email(),
                'phone' => fake()->phoneNumber(),
                'company' => fake()->company(),
                'notes' => fake()->paragraph(),
                'status' => 'active',
            ]);
        }
    }
}
