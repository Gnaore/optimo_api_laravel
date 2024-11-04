<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeInventaire extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function bordereau(){
        return  $this->belongsTo(\App\Models\Bordereau::class,'bordereau_code', 'code');
    }

    public function bien(){
        return  $this->belongsTo(\App\Models\Immobilisation::class,'id', 'code_inventaire_id');
    }
}
