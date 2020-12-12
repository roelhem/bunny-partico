<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Group;
use App\Models\GroupCategory;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GroupCategory::factory(5)->create();
        Group::factory(20)->create()->each(function (Group $group) {
            $contactIds = Contact::query()->inRandomOrder()->limit(rand(0, 20))->pluck('id');
            $group->contacts()->attach($contactIds);
        });
    }
}
