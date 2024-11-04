<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acquisition extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function nature(){
        return  $this->belongsTo(\App\Models\Nature::class,'nature_id');
    }

    public function code_inventaire(){
        return  $this->belongsTo(\App\Models\CodeInventaire::class,'code_inventaire_id');
    }

    public function bon_commande(){
        return  $this->belongsTo(\App\Models\BonCommande::class,'bon_commande_id');
    }
}