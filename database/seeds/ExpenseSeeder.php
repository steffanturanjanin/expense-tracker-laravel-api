<?php

use Illuminate\Database\Seeder;
use App\Category;
use App\Expense;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (Category::all() as $category) {
            for ($i = 0; $i < $faker->numberBetween(0, 1000); $i++) {
                $expense = factory(Expense::class)->make();
                $expense->user_id = $category->user_id;
                $expense->category_id = $expense->type === 1 ? null : $category->id;
                $expense->save();
            }
        }
    }
}
