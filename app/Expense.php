<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    private $id;
    private $category_id;
    private $name;
    private $amount;
    private $type;
    private $date;

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
