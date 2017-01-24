<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function servers()
    {
        return $this->hasMany('App\Server');
    }
}
