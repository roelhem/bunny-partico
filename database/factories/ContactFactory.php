<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $created_at = $this->faker->dateTime();

        $name = $this->faker->boolean(90) ? [
            'name_first' => $this->faker->firstName,
            'name_middle' => function() {
                $names = [];
                while ($this->faker->boolean(33)) {
                    $names[] = $this->faker->firstName();
                }

                if (count($names) > 0) {
                    return implode(' ', $names);
                }

                return null;
            },
            'name_initials' => function($self) {
                $letters = [];
                $name_first = trim(\Arr::get($self, 'name_first',''));
                $name_middle = trim(\Arr::get($self, 'name_middle',''));
                if(strlen($name_first) >= 1) {
                    $letters[] = mb_strtoupper(substr($name_first,0,1));
                }

                foreach (explode(' ', $name_middle) as $name) {
                    if(strlen($name) >= 1) {
                        $letters[] = mb_strtoupper(substr($name, 0,1));
                    }
                }


                return implode($letters);
            },
            'name_last' => $this->faker->boolean(90) ? $this->faker->lastName : null,
        ] : [
            'name_full' => $this->faker->name,
        ];

        return array_merge([
            'created_at' => $created_at,
            'updated_at' => $this->faker->dateTimeBetween($created_at),
            'nickname' => function($self)  {
                $name_first = \Arr::get($self, 'name_first');

                $length = $this->faker->numberBetween(4,12);

                if(strlen($name_first) > $length) {
                    return mb_substr($name_first, 0, $length);
                } else {
                    return null;
                }
            },
            'title' => $this->faker->boolean(30) ?$this->faker->title : null,
            'birth_date' => $this->faker->dateTimeBetween('-30 years', '-15 years'),
            'remarks' => 'Deze Person is met een factory gegenereerd op basis van willekeurige data. (Alleen voor test-toepassingen)',
        ], $name);
    }
}
