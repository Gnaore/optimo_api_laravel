<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Immobilisation extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function localisation(){
        return  $this->belongsTo(\App\Models\Localisation::class,'localisation_id');
    }

    public function code_inventaire(){
        return  $this->belongsTo(\App\Models\CodeInventaire::class,'code_inventaire_id');
    }

    public function sous_famille(){
        return  $this->belongsTo(\App\Models\SousFamille::class,'sous_famille_id');
    }

    public function client(){
        return  $this->belongsTo(\App\Models\Client::class,'client_id');
    }

    public function acquisition(){
        return  $this->belongsTo(\App\Models\Acquisition::class,'acquisition_id');
    }

    public function avant_rebus(){
        return  $this->hasMany(\App\Models\AvantRebus::class,'immobilisation_id');
    }

    public function au_rebus(){
        return  $this->hasMany(\App\Models\AuRebus::class,'immobilisation_id');
    }

    public function transferts(){
        return  $this->hasMany(\App\Models\Transfert::class,'immobilisation_id');
    }
}
