<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'passport', 'date', 'ref_id', 'medical_id','serial_no',
        'slip','country','user_id','passport_image'
    ];

    public function reference(){
        return $this->hasOne(Reference::class,'id','ref_id');
    }
    public function medical(){
        return $this->hasOne(Medical::class,'id','medical_id');
    }
    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);  
    }
}
