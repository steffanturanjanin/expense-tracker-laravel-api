<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function sumExpenses($expenses) {
        $sum = 0;
        foreach ($expenses as $expense) {
            $sum = $sum + $expense->amount;
        }
        return $sum;
    }
}
