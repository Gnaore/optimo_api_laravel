<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Localisation extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function site(){
        return  $this->belongsTo(\App\Models\Site::class,'site_id');
    }

    public function bordereaux(){
        return  $this->hasMany(\App\Models\Bordereau::class,'localisation_id');
    }

    public function immobilisations(){
        return  $this->hasMany(\App\Models\Immobilisation::class,'localisation_id');
    }
}