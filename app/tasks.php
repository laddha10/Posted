<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tasks extends Model
{
 protected $table='tasks';
    public $timestamps=false;
    public $incrementing=false;

    protected $fillable=['title','created_at'];

}
