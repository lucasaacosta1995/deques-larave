<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_proyecto_voto extends Model
{
    protected $primaryKey = 'id_voto';
    protected $table = 'jo_proyecto_voto';
    public $timestamps = false;

    public function proyectos(){
        return $this->belongsTo('App\jo_proyecto','id_proyecto','proyecto_id');
    }
    public function users(){
        return $this->belongsTo('App\jo_user','id_user','user_id');
    }
}
