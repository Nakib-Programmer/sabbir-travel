<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function patient(){
        return $this->hasOne(Patient::class,'medical_id','id');
    }
}
