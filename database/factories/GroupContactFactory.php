<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Group;
use App\Models\GroupContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GroupContact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group_id' => function() {
                $group = Group::query()->inRandomOrder()->first();
                if($group instanceof Group) {
                    return $group->id;
                } else {
                    return Group::factory()->create()->id;
                }
            },
            'contact_id' => function() {
                $contact = Contact::query()->inRandomOrder()->first();
                if($contact instanceof Contact) {
                    return $contact->id;
                } else {
                    return Contact::factory()->create()->id;
                }
            }
        ];
    }
}
