<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_tramite_parlamentario extends Model
{

    protected $primaryKey = 'id_tramite';
    protected $table = 'jo_tramites_parlamentarios';
    public $timestamps = false;


    public function congreso(){
        return $this->hasMany('App\jo_congreso', 'id_congreso','congreso_id');
    }
}
