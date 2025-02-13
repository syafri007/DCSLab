<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;

use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('id_ID');
        $branch_name = $faker->city();

        return [
            'company_id' => Company::select('id')->inRandomOrder()->limit(1)->get()[0],
            'code' => $faker->numberBetween(01, 10),
            'name' => $branch_name,
            'address' => $faker->address(),
            'city' => $branch_name,
            'contact' => $faker->e164PhoneNumber(),
            'remarks' => '',
            'status' => '1'
        ];
    }
}
