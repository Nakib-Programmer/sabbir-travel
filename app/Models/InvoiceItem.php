<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id', 'medical_test_id', 'price', 'quantity', 'total', 
    ];
    public function medicalTest(){
        return $this->belongsTo(MedicalTest::class);
    }
}
