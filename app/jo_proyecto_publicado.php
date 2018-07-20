<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_proyecto_publicado extends Model
{
    protected $primaryKey = 'jo_proyecto_publicado';
    protected $table = 'jo_proyecto_publicado';
    public $timestamps = false;

    public function politico(){
        return $this->hasOne('App\jo_politico','id_politico','politico_id');
    }
}
