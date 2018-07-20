<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class jo_provincia extends Model
{
    public $name = '';
    protected $primaryKey = 'id';
    protected $table = 'provincias';
    public $timestamps = false;
}
