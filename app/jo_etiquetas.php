<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_etiquetas extends Model
{
    protected $primaryKey = 'id_etiqueta';
    protected $table = 'jo_etiquetas';
    public $timestamps = false;

    public function sinonimos(){
        return $this->hasMany('App\jo_etiquetas_sinonimos','etiqueta_id','id_etiqueta');
    }
}
