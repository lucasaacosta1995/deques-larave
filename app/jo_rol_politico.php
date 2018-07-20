<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_rol_politico extends Model
{
    protected $primaryKey = 'id_rol';
    protected $table = 'jo_roles_politicos';
    public $timestamps = false;

    public function politicos()
    {
        return $this->belongsTo('App\jo_politico', 'id_rol','rol_id');
    }
}
