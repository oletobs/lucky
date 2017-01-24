<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = ['name', 'w_o_w_class_id', 'specc', 'guild_rank'];

    protected $hidden = ['w_o_w_class_id', 'wowClass', 'guild_id', 'created_at'];

    protected $appends = ['class_name'];

    protected $casts = [
        'stats' => 'array'
    ];

    public function guild()
    {
        return $this->belongsTo('App\Guild');
    }

    public function wowClass()
    {
        return $this->belongsTo('App\WOWClass', 'w_o_w_class_id');
    }

    public function getClassNameAttribute()
    {
        return $this->wowClass->name;
    }
}
