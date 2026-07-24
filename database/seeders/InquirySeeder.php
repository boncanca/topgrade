<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Inquiry;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Inquiry::create([
                'contact_id' => Contact::inRandomOrder()->first()?->id,
                'name' => fake()->name(),
                'email' => fake()->email(),
                'phone' => fake()->phoneNumber(),
                'subject' => fake()->sentence(),
                'message' => fake()->paragraphs(2, true),
                'status' => fake()->randomElement(['new', 'open', 'in_progress', 'resolved', 'closed', 'archived']),
            ]);
        }
    }
}
