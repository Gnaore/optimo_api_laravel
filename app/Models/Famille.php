<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Famille extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function category(){
        return  $this->belongsTo(\App\Models\Category::class,'category_id');
    }

    public function sousfamilles(){
        return  $this->hasMany(\App\Models\SousFamille::class,'famille_id');
    }
}
