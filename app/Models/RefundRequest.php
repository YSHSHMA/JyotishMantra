<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_details_id',
        'customer_id',
        'status',
        'approved_count',
        'denied_count',
        'amount',
        'product_id',
        'order_id',
        'refund_reason',
        'reason_name',
        'reason_data',
        'rma_no',
        'delivery_charge',
        'admin_tax_given',
        'admin_tax_given',
        'carrier_id',
        'patner_type',
        'approved_note',
        'rejected_note',
        'received_note',
        'payment_info',
        'change_by',
    ];
    protected $casts = [
        'order_details_id' => 'integer',
        'customer_id' => 'integer',
        'status' => 'string',
        'amount' => 'float',
        'product_id' => 'integer',
        'order_id' => 'integer',
        'refund_reason' => 'string',
        'rma_no' => 'string',
        'reason_data' => 'string',
        'delivery_charge' => 'float',
        'admin_tax_given' => 'float',
        'admin_tax_given' => 'float',
        'carrier_id' => 'string',
        'patner_type ' => 'string',
        'reason_name' => 'string',
        'approved_note' => 'string',
        'received_note' => 'string',
        'rejected_note' => 'string',
        'payment_info' => 'string',
        'change_by' => 'string'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    /* order_details->orderDetails */
    public function orderDetails(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class, 'order_details_id');
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    /* refund_status->refundStatus */
    public function refundStatus(): HasMany
    {
        return $this->hasMany(RefundStatus::class, 'refund_request_id');
    }
}