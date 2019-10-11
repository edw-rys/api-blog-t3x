<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $tables = 'categories';
    // 1 -> *
    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
