<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Contact::factory(40)->create();
        \App\Models\PostalAddress::factory(40)->create();
        \App\Models\PhoneNumber::factory(40)->create();
        \App\Models\EmailAddress::factory(40)->create();
        \App\Models\ContactLanguage::factory(40)->create();
        \App\Models\ContactRelation::factory(40)->create();
    }
}
