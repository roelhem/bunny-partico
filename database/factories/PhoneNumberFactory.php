<?php

namespace Database\Factories;

use App\Models\Individual;
use App\Models\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PhoneNumber::class;

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
            'country_code' => $this->faker->randomElement(['NL','GB','FR','BE','DE','IT','SE','NO','DK','ES','PT','CH','AT']),
            'phone_number' => function($self) {
                $phoneNumberUtil = PhoneNumberUtil::getInstance();

                $country_code = \Arr::get($self, 'country_code','NL');

                if(\Arr::get($self, 'is_mobile', false)) {
                    $res = $phoneNumberUtil->getExampleNumberForType($country_code,PhoneNumberType::MOBILE);
                } else {
                    $res = $phoneNumberUtil->getExampleNumber($country_code);
                }

                if($res instanceof PhoneNumber) {
                    return $phoneNumberUtil->format($res, PhoneNumberFormat::E164);
                } else {
                    return $res;
                }
            },
            'remarks' => 'Gegenereerd door een Factory.'
        ];
    }
}
