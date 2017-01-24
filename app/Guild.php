<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guild extends Model
{
    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['name','slug','last_modified','server_id'];

    public function members()
    {
        return $this->hasMany('App\Character');
    }

    public function server()
    {
        return $this->belongsTo('App\Server');
    }
}
