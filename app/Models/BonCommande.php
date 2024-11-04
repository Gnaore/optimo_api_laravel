<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonCommande extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function fournisseur(){
        return  $this->belongsTo(\App\Models\Fournisseur::class,'fournisseur_id');
    }
}
