<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrustWallet extends Model
{
    use HasFactory;
    protected $table = 'trust_wallets';
    protected $fillable = ['id', 'type', 'trust_id', 'temple_id', 'purohit_id', 'debit', 'credit', 'balance', 'debit_date', 'credit_date', 'request_by', 'transfer_by', 'status', 'created_at', 'updated_at'];

}
