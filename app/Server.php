<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    public function region()
    {
        return $this->belongsTo('App\Region');
    }

    public function guilds()
    {
        return $this->hasMany('App\Guild');
    }
}
