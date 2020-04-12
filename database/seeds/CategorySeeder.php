<?php

use Illuminate\Database\Seeder;
use App\Category;
use App\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (User::all() as $user) {
            for ($i = 0; $i < $faker->numberBetween(5, 15); $i++) {
                $user->categories()->save(factory(Category::class)->make());
            }
        }
    }
}
