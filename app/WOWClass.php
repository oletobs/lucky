<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WOWClass extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'w_o_w_classes';


    public function characters()
    {
        return $this->hasMany('App\Character');
    }
}
