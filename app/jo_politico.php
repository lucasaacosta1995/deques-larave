<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_politico extends Model
{
    protected $primaryKey = 'id_politico';
    protected $table = 'jo_politicos';
    public $timestamps = false;


    public function rol(){
        return $this->hasMany('App\jo_rol_politico', 'id_rol','rol_id');
    }
}
