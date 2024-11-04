<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SousFamille extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function famille(){
        return  $this->belongsTo(\App\Models\Famille::class,'famille_id');
    }
}
