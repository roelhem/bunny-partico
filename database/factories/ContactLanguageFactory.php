<?php

namespace Database\Factories;

use App\Models\ContactLanguage;
use App\Models\Contact;
use CommerceGuys\Intl\Language\LanguageRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactLanguageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactLanguage::class;

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
            'label' => $this->faker->word,
            'language_code' => function() {
                $repository = resolve(LanguageRepositoryInterface::class);
                $keys = array_keys($repository->getList());
                return $this->faker->randomElement($keys);
            },
            'remarks' => 'Gegenereerd door een factory.'
        ];
    }
}
