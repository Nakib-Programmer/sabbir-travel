<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WafidAppointment extends Model
{
    use HasFactory;
     protected $fillable = [
        'passport',
        'merchant_reference',
        'gcc_slip_no',
        'first_name',
        'last_name',
        'nationality',
        'national_id',
        'gender',
        'marital_status',
        'country_traveling_to',
        'date_of_birth',
        'passport_expiry_date',
        'passport_issue_place',
        'passport_issue_date',
        'applied_position',
        'payment_status',
        'amount',
        'appointment_type',
        'medical_center_name',
        'medical_center_address',
        'medical_center_phone',
        'medical_center_email',
        'medical_center_website',
        'barcode',
        'generated_date',
        'valid_till',
    ];
}
