<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_proyecto extends Model
{
    protected $primaryKey = 'id_proyecto';
    protected $table = 'jo_proyectos';
    public $timestamps = false;

    public function autor(){
        return $this->hasOne('App\jo_politico', 'id_politico','autor_id');
    }
    public function congreso(){
        return $this->hasOne('App\jo_congreso', 'id_congreso','congreso_id');
    }
    public function tramite(){
        return $this->hasOne('App\jo_tramite_parlamentario', 'id_tramite','tramite_id');
    }
    public function tipo(){
        return $this->hasOne('App\jo_tipo_proyecto', 'id_tipo_proyecto','tipo_proyecto_id');
    }
    public function etiquetas(){
        return $this->hasMany('App\jo_proyectos_etiquetas','proyecto_id','id_proyecto');
    }
    public function votos(){
        return $this->hasMany('App\jo_proyecto_voto','proyecto_id','id_proyecto');
    }
    public function firmantes(){
        return $this->hasMany('App\jo_proyecto_publicado','proyecto_id','id_proyecto');
    }
    public function preguntas_respuestas(){
        return $this->hasMany('App\jo_pregunta_respuesta','proyecto_id','id_proyecto');
    }


//    public function getCantidadImpulsos(){
//        $cantidad = $this->votos()->where('votos.');
//        foreach ()
//    }
}
