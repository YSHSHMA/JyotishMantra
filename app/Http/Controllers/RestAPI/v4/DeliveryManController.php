<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;

class DeliveryManController extends Controller
{
    public function __construct(
        private DeliveryMan $delivery_man,
    ){}

    public function deliveryman_info(Request $request, $deliveryman_id)
    {
        $delivery_man = $this->delivery_man->with(['rating'])
            ->withCount(['review', 'orders as delivered_orders_count'=> function($query){
                $query->where(['order_status'=>'delivered', 'delivery_type' => 'self_delivery']);
            }])
            ->find($deliveryman_id);

        return response()->json($delivery_man, 200);
    }
}
