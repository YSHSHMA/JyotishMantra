<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationSubscription extends Model
{
    use HasFactory;
    protected $table = 'donation_subscriptions';
    protected $fillable = ['id', 'user_id','order_id', 'subscription_id', 'plan_id', 'razorpay_plan_id', 'amount', 'frequency', 'quantity', 'status', 'total_count', 'paid_count', 'remaining_count', 'current_start', 'current_end', 'start_at', 'ended_at', 'payment_url', 'notes', 'created_at', 'updated_at'];
}
