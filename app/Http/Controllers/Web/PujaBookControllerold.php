<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Chadhava_orders;
use App\Models\Leads;
use App\Models\Order;
use App\Models\Package;
use App\Models\PoojaForecast;
use App\Models\Prashad_deliverys;
use App\Models\Product;
use App\Models\ProductCompare;
use App\Models\ProductLeads;
use App\Models\SellerWallet;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\ServiceReview;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\Wishlist;
use App\Utils\CartManager;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use function App\Utils\payment_gateways;

class PujaBookController extends Controller
{

    public function pujabookNow(Request $request, $slug)
    {
       
        $puja = Service::withCount('PoojaOrderReview')
        ->withAvg('review', 'rating')
        ->with('package')
        ->where('slug', $slug)->where('status',1)
        ->first();
        
        if (!$puja) {
            return redirect('/');
        }
        $servicesGet = collect([$puja]);
    
        $nextBooking = PoojaForecast::where('service_id', $puja->id)
            ->where('is_expired', 0)
            ->whereIn('type', ['weekly', 'special'])
            ->whereHas('service', function ($query) {
                $query->where('status', 1)
                    ->where('product_type', 'pooja');
            })
            ->orderBy('booking_date', 'asc')
            ->first();

        $bookingdate = $nextBooking?->booking_date;
        $forecastServiceId = $nextBooking?->service_id;
         // Reviews
        $allReviews = ServiceReview::with('userData')->get();
    
        $uniqueUserReviews = $allReviews
            ->groupBy('user_id')
            ->map(function ($userReviews) {
                return $userReviews->sortByDesc(function ($review) {
                    if (!empty($review->youtube_link)) return 3;
                    if (!empty($review->comment)) return 2;
                    return 1;
                })->first();
            })
            ->sortByDesc(function ($review) {
                if (!empty($review->youtube_link)) return 3;
                if (!empty($review->comment)) return 2;
                return 1;
            })
            ->values();
    
        $originalReviews = $uniqueUserReviews->where('is_edited', 0);
        $editedReviews = $uniqueUserReviews;
        $serviceReview = $originalReviews->count();
    
        $reviewCounts = [
            'excellent'     => $originalReviews->where('rating', 5)->count(),
            'good'          => $originalReviews->where('rating', 4)->count(),
            'average'       => $originalReviews->where('rating', 3)->count(),
            'below_average' => $originalReviews->where('rating', 2)->count(),
            'poor'          => $originalReviews->filter(fn($review) => $review->rating == 1 && !is_null($review->comment))->count(),
            'averageStar'   => round($originalReviews->avg('rating'), 1),
            'list'          => $editedReviews->take(10),
        ];
    
        $totalReviews = $servicesGet->sum('pooja_order_review_count');
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('web-views.puja.puja-book', compact('puja','bookingdate','forecastServiceId','servicesGet',
            'serviceReview','reviewCounts','totalReviews','paymentPublishedStatus','paymentGatewayPublishedStatus','payment_gateways_list','digital_payment'));
    }
    public function pujaleadstore(Request $request, $slug)
    {
       $servicedata = Service::where('id', $request->service_id)->where('slug', $slug)->where('product_type', 'pooja')->first();
        if (!$servicedata) {
            return redirect()->back()->with('error', 'Puja not found.');
        }
        $servicesGet = collect([$servicedata]);
        $PoojaShow = PoojaForecast::with([
                'service' => function ($query) {
                    $query->where('status', 1)
                        ->where('product_type', 'pooja');
                }
            ])
            ->whereIn('type', ['weekly', 'special'])
            ->orderBy('booking_date', 'asc')
            ->withCount('PoojaOrderReview')
            ->withAvg('review', 'rating')
            ->where('is_expired', 0)
            ->get()
            ->filter(function ($item) {
                return $item->service;
            })
            ->unique('service_id')
            ->values();
        $nextBooking = PoojaForecast::where('service_id', $servicedata->id)
        ->where('is_expired', 0)
        ->whereIn('type', ['weekly', 'special'])
        ->whereHas('service', function ($query) {
            $query->where('status', 1)->where('product_type', 'pooja');
        })
        ->orderBy('booking_date', 'asc')
        ->first();

        $date = $nextBooking?->booking_date;
        $forecastServiceId = $nextBooking?->service_id;
        $packageList = json_decode($servicedata->packages_id, true);
        $selectedPackage = collect($packageList)->firstWhere('package_id', $request->package_id);
        if (!$selectedPackage) {
            return redirect()->back()->with('error', 'Invalid package selection.');
        }

        $package = Package::find($request->package_id);
        if (!$package) {
            return redirect()->back()->with('error', 'Package not found.');
        }

        $bookingDate = $request->input('booking_date');
        $personName = $request->input('person_name');
        $personPhone = $request->input('person_phone');
        $nameParts = explode(' ', $personName);
        $firstName = $nameParts[0];
        $lastName = implode(' ', array_slice($nameParts, 1));

        if ($personPhone == '') {
            return redirect()->to('/');
        }

             $customerId = null;

        $userExists = User::where('phone', $personPhone)->exists();
        if (!$userExists) {
            $user = User::create([
                'name'     => $personName,
                'f_name'   => $firstName,
                'l_name'   => $lastName,
                'phone'    => $personPhone,
                'email'    => 'user@mahakal.com',
                'password' => bcrypt('12345678'),
                'verify_otp' =>1,
            ]);
            $customerId = $user->id;
            $data = ['customer_id' => $customerId];
            Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
        }else {
            $user = User::where('phone', $personPhone)->first();
            $customerId = $user->id;
        }
        

        $orderData = Service_order::select('id')->latest()->first();
            $orderId = !empty($orderData['id']) 
                ? 'PJ' . (100000 + $orderData['id'] + 1)
                : 'PJ' . (100001);

        $cust_details = [
            'service_id'    => $forecastServiceId,
            'type'          => $servicedata->product_type,
            'package_id'    => $selectedPackage['package_id'],
            'product_id'    => $servicedata->product_id,
            'package_price' => $selectedPackage['package_price'],
            'package_name'  => $package->title,
            'noperson'      => $package->person,
            'person_phone'  => $personPhone,
            'person_name'   => $personName,
            'booking_date'  => $date ?? $bookingDate,
            'customer_id'   => $customerId, 
            'add_product_id' => $request->add_product_id,
            'platform'      => 'web', 
            'payment_status'=> 'pending', 
            'final_amount'      => $request->final_amount, 
            'order_id'  => $orderId,
        ];


        $leads = Leads::create($cust_details);
        $insertedRowId = $leads->id;
        $serviceOrderData = Leads::find($leads->id);
        if ($request->has('add_product_id')) {
            $extraProducts = json_decode($request->add_product_id, true);

            if (is_array($extraProducts)) {
                foreach ($extraProducts as $prod) {
                    $product = Product::find($prod['product_id']);
                    if ($product) {
                        $qty = (int) $prod['qty'];
                        $unitPrice = $product->unit_price ?? 0;
                        $finalPrice = ($qty * $unitPrice);

                        ProductLeads::create([
                            'lead_id'       => $insertedRowId,
                            'product_id'    => $product->id,
                            'product_name'  => $product->name,
                            'product_price'    => $unitPrice,
                            'qty'           => $qty,
                            'final_price'   => $finalPrice,
                            'leads_id'      => $serviceOrderData->id,
                        ]);
                    }
                }
            }
        }

        if (!empty($insertedRowId)) {
            $leadno = 'PJ' . (100 + $insertedRowId + 1);
        } else {
            $leadno = 'PJ' . (101);
        }
        Leads::where('id', $insertedRowId)->update(['leadno' => $leadno]);

        

        if (!$serviceOrderData) {
            return; 
        }
        $existingServiceOrder = Service_order::where('order_id', $serviceOrderData->order_id)->first();

        $serviceOrderAdd = [
            'order_id'         => $serviceOrderData->order_id,
            'customer_id'      => $serviceOrderData->customer_id,
            'service_id'       => $serviceOrderData->service_id,
            'type'             => $serviceOrderData->type,
            'leads_id'         => $serviceOrderData->id,
            'package_id'       => $serviceOrderData->package_id,
            'package_price'    => $serviceOrderData->package_price,
            'booking_date'     => $serviceOrderData->booking_date,
            'wallet_amount'    => $serviceOrderData->via_wallet ?? 0,
            'transection_amount' => $serviceOrderData->final_amount,
            'pay_amount'       => $serviceOrderData->final_amount,
        ];
        $bookDate = date('d, F, l', strtotime($serviceOrderData->booking_date));
        Service_order::create($serviceOrderAdd);
   
        return response()->json([
            'success' => true,
            'data' => [
                'order_id'      => $serviceOrderData->order_id,
                'name'          => $serviceOrderData->person_name,
                'mobile'        => $serviceOrderData->person_phone,
                'booking_date'  => $bookDate,
                'package_name'  => $serviceOrderData->package_name ?? '',
                'products' => $serviceOrderData->add_product_id 
                    ? collect(json_decode($serviceOrderData->add_product_id, true))->map(function ($p) {
                        $prod = \App\Models\Product::find($p['product_id']);
                        return [
                            'id'    => $p['product_id'],
                            'name'  => $prod ? $prod->name : 'Unknown',
                            'price' => $p['price'],
                            'qty'   => $p['qty'],
                        ];
                    })->toArray()
                    : [],


                'total_amount'  => $serviceOrderData->final_amount,
            ]
        ]);

    }


}