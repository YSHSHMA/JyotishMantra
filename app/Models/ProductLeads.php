<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\User;
use App\Models\Leads;


class ProductLeads extends Model
{
   protected $fillable = [
        'leads_id',
        'product_id',
        'final_price',
        'qty',
        'product_name',
        'product_price',
    ];

    protected $casts = [
        'leads_id' => 'integer',
        'product_id' => 'string',
        'final_price' => 'string',
        'qty' => 'string',
        'product_name' => 'string',
        'product_price' => 'string',
    ];

    public function productsData()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
    
    public function customers()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }

    public function lead()
    {
        return $this->belongsTo(Leads::class, 'leads_id', 'id');
    }
} 