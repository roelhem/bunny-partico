<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->text,
            'remarks' => "Created by a factory",
            'category_id' => function() {
                $category = GroupCategory::query()->inRandomOrder()->first();
                if($category instanceof GroupCategory) {
                    return $category->id;
                } else {
                    return GroupCategory::factory()->create()->id;
                }
            }
        ];
    }
}
