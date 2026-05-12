<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanditTransectionHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'type',
        'temple_id',
        'trust_id',
        'purohit_id',
        'emp_id',
        'credit',
        'debit',
        'balance',
        "credit_date",
        'debit_date',
        "pay_transactionid",
        'note',
        'bank_holder_name',
        'bank_name',
        'ifsc_code',
        'account_number',
        'status'
    ];

    public function Trusts()
    {
        return $this->hasOne(DonateTrust::class, 'id', 'trust_id');
    }
    public function Temple()
    {
        return $this->hasOne(Temple::class, 'id', 'temple_id');
    }
    public function Pandit()
    {
        return $this->hasOne(Purohit::class, 'id', 'purohit_id');
    }
}
