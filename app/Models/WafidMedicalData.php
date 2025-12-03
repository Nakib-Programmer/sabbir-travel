<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WafidMedicalData extends Model
{
    use HasFactory;
     protected $fillable = [
        'status',
        'pdf_url',
        'print_url',
        'photo',
        'name',
        'phone',
        'gender',
        'passport',
        'age',
        'passport_expiry_on',
        'nationality_name',
        'applied_position_name',
        'marital_status',
        'traveled_country_name',
        'height',
        'medical_center',
        'weight',
        'medical_examination_date',
        'BMI',
        'blood_group',
    ];

    public function ghc(){
        return $this->hasOne(Medical::class,'name','medical_center');
    }
}
