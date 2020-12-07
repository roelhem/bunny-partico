<?php

namespace Database\Factories;

use App\Models\EmailAddress;
use App\Models\Individual;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'individual_id' => function() {
                $individual = Individual::query()->inRandomOrder()->first();
                if($individual instanceof Individual) {
                    return $individual->id;
                } else {
                    return Individual::factory()->create()->id;
                }
            },
            'label' => $this->faker->word,
            'email_address' => $this->faker->safeEmail,
            'remarks' => 'Gegenereerd door een factory.'
        ];
    }
}
