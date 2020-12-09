<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactRelationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactRelation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'contact_id' => function() {
                $contact = Contact::query()->inRandomOrder()->first();
                if($contact instanceof Contact) {
                    return $contact->id;
                } else {
                    return Contact::factory()->create()->id;
                }
            },
            'related_contact_id' => function() {
                $contact = Contact::query()->inRandomOrder()->first();
                if($contact instanceof Contact) {
                    return $contact->id;
                } else {
                    return Contact::factory()->create()->id;
                }
            },
            'label' => $this->faker->word,
            'remarks' => 'Gegenereerd door een factory.',
        ];
    }
}
