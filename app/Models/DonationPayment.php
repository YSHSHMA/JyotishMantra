<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationPayment extends Model
{
    use HasFactory;
    protected $table = 'donation_payments';
    protected $fillable = ['id', 'subscription_id', 'payment_id', 'amount', 'currency', 'status', 'method', 'captured_at', 'notes', 'created_at', 'updated_at'];
}
