<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];

    public function sites(){
        return  $this->hasMany(\App\Models\Site::class,'zone_id');
    }
}
