<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_congreso extends Model
{
    protected $primaryKey = 'id_congreso';
    protected $table = 'jo_congresos';
    public $timestamps = false;


    public function pais(){
        return 1;
    }

    public function provincia(){
        return $this->hasMany('App\jo_provincia', 'id','provincia_id');
    }
    //
}
