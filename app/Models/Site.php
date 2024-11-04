<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function zone(){
        return  $this->belongsTo(\App\Models\Zone::class,'zone_id');
    }

    public function localisations(){
        return  $this->hasMany(\App\Models\Localisation::class,'site_id');
    }
}
