<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $table ='couriers';
    protected $primaryKey = 'id_courier';
    protected $fillable = ['name_courier','jenis_courier','level_courier'];
}
