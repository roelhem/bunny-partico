<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IndividualsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Individual::factory(40)->create();
        \App\Models\PostalAddress::factory(40)->create();
        \App\Models\PhoneNumber::factory(40)->create();
        \App\Models\EmailAddress::factory(40)->create();
    }
}
