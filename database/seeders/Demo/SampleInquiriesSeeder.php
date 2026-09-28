<?php

namespace Database\Seeders\Demo;

use App\Models\Contact;
use App\Models\Inquiry;
use Illuminate\Database\Seeder;

class SampleInquiriesSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $contact = Contact::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => '+44 7700 900'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'company' => null,
                'notes' => 'Sample contact for developer demonstration.',
                'status' => 'active',
            ]);

            Inquiry::create([
                'contact_id' => $contact->id,
                'name' => "{$contact->first_name} {$contact->last_name}",
                'email' => $contact->email,
                'phone' => $contact->phone,
                'subject' => 'Sample trial inquiry',
                'message' => 'Interested in joining the youth development program.',
                'status' => 'new',
            ]);
        }
    }
}
