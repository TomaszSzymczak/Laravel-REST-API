<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    public function magazines()
    {
        return $this->hasMany('\App\Magazine');
    }
}
