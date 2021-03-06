<?php

namespace Database\Factories\User;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\User\UsersAddress;

class UsersAddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UsersAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'phone_number_extension' => '+'.mb_substr(
                str_shuffle("00000111112222233333444445555566666777778888899999"),
                2,
                3
            ),
            'phone_number' => $this->faker->phonenumber,
            'building_name' => $this->faker->buildingnumber,
            'street_address1' => $this->faker->StreetAddress,
            'city' => $this->faker->city,
            'country' => $this->faker->country,
            'postcode' => $this->faker->postcode,
        ];
    }
}
