<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_proyectos_etiquetas extends Model
{
    protected $primaryKey = 'jo_proyecto_etiqueta';
    protected $table = 'jo_proyectos_etiquetas';
    public $timestamps = false;

    public function proyectos(){
        return $this->belongsTo('App\jo_proyecto','id_proyecto','proyecto_id');
    }
    public function etiqueta(){
        return $this->hasOne('App\jo_etiquetas','id_etiqueta','etiqueta_id');
    }
}
