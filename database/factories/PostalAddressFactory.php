<?php

namespace Database\Factories;

use App\Models\Individual;
use App\Models\PostalAddress;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostalAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PostalAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $locale = $this->faker->locale;
        $list = resolve(CountryRepositoryInterface::class)->getList('NL');
        $country_code = substr($locale, -2, 2);
        if (!array_key_exists($country_code, $list)) {
            $country_code = 'NL';
        }

        $localeFaker = \Faker\Factory::create($locale);
        if(!($localeFaker instanceof Generator)) {
            $localeFaker = $this->faker;
        }

        $addressFormat = resolve(AddressFormatRepositoryInterface::class)->get($country_code);


        $res = [
            'individual_id' => function() {
                $person = Individual::query()->inRandomOrder()->first();
                if($person instanceof Individual) {
                    return $person->id;
                } else {
                    return Individual::factory()->create()->id;
                }
            },
            'label' => $this->faker->word,
            'locale' => $locale,
            'country_code' => $country_code,
            'remarks' => 'Dit adres is automatisch gegenereerd met willekeurige gegevens.',
        ];

        $attributes = [
            AddressField::ADMINISTRATIVE_AREA => 'administrative_area',
            AddressField::LOCALITY => 'locality',
            AddressField::DEPENDENT_LOCALITY => 'dependent_locality',
            AddressField::POSTAL_CODE => 'postal_code',
            AddressField::SORTING_CODE => 'sorting_code',
            AddressField::ADDRESS_LINE1 => 'address_line_1',
            AddressField::ADDRESS_LINE2 => 'address_line_2',
            AddressField::ORGANIZATION => 'organisation'
        ];

        $formats = [
            AddressField::ADMINISTRATIVE_AREA => [
                'state' => [],
                'region' => [],
                'district' => []
            ],
            AddressField::LOCALITY => [
                'town' => [],
                'village' => [],
                'city' => [],
            ],
            AddressField::DEPENDENT_LOCALITY => [
                'departmentName' => [],
                'metropolitanCity' => [],
                'township' => [],
                'city' => [],
            ],
            AddressField::POSTAL_CODE => [
                'postcode' => [],
            ],
            AddressField::SORTING_CODE => [
                'numerify' => ['#####']
            ],
            AddressField::ADDRESS_LINE1 => [
                'streetAddress' => []
            ],
            AddressField::ADDRESS_LINE2 => [
                'secondaryAddress' => [],
                'building' => []
            ],
            AddressField::ORGANIZATION => [
                'company' => []
            ]
        ];

        foreach ($addressFormat->getUsedFields() as $usedField) {
            if(\Arr::has($attributes, $usedField)) {

                $attribute = \Arr::get($attributes, $usedField);
                $fakerOptions = \Arr::get($formats, $usedField);

                foreach ($fakerOptions as $format => $params) {
                    try {
                        $generatedValue = $localeFaker->format($format, $params);
                        $res[$attribute] = $generatedValue;
                        break;
                    } catch (\Throwable $exception) {

                    }
                }

            }
        }

        return $res;
    }
}
