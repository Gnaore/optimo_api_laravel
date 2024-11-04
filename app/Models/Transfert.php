<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfert extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function immobilisation(){
        return  $this->belongsTo(\App\Models\Immobilisation::class,'immobilisation_id');
    }

    public function user(){
        return  $this->belongsTo(\App\Models\User::class,'user_id');
    }

    public function from_localisation(){
        return  $this->belongsTo(\App\Models\Localisation::class,'from_localisation_id');
    }

    public function to_localisation(){
        return  $this->belongsTo(\App\Models\Localisation::class,'to_localisation_id');
    }
}
