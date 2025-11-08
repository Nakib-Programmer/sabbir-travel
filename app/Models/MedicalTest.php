<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalTest extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'price',
    ];
    public function invoice(){
        return $this->hasOne(InvoiceItem::class,'medical_test_id','id');
    }
    
}
