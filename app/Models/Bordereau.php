<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bordereau extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function localisation(){
        return  $this->belongsTo(\App\Models\Localisation::class,'localisation_id');
    }

    public function site(){
        return  $this->belongsTo(\App\Models\Site::class, 'site_code', 'code');
    }
}
