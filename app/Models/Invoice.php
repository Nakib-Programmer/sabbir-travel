<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'patient_id', 'expiration_date', 'subtotal','user_id','paid','due','status','note'
    ];
    public function item(){
        return $this->hasMany(InvoiceItem::class); 
    }
    public function patient(){
        return $this->hasOne(Patient::class,'id','patient_id'); 
    }
    
}
