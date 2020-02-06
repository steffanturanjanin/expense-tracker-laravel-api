<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    private $id;
    private $user_id;
    private $name;
    private $icon;

    public function user()
    {
        return $this->hasOne('App\User');
    }
}
