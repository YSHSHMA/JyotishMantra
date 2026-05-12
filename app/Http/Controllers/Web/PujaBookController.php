<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Chadhava;
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
use App\Models\Vippooja;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\Wishlist;
use App\Utils\CartManager;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use function App\Utils\payment_gateways;

class PujaBookController extends Controller
{

    public function pujabookNow(Request $request, $slug)
    {
       
        $puja = Service::withCount('PoojaOrderReview')->withAvg('review', 'rating')->with('package')->where('slug', $slug)
            ->where('status', 1)->first();

        if (!$puja) {
            $puja = Vippooja::withCount('PoojaOrderReview')->withAvg('review', 'rating')->with('packages')->where('slug', $slug)
                ->where('status', 1)->first();
                $folder = 'pooja/vip/';
        } else {
                $folder = 'pooja/';
        }
        
        if (!$puja) {
            return redirect('/');
        }
        //  Decode images safely
        $images = json_decode($puja->images ?? '[]', true);

        // Build full URLs with default fallback
        $imagePaths = collect($images)->map(function ($img) use ($folder) {
            return getValidImage(
                path: 'storage/app/public/' . $folder . $img,
                type: 'product'
            );
        })->toArray();
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
        return view('web-views.puja.puja-book', compact('puja','imagePaths','bookingdate','forecastServiceId','servicesGet',
            'serviceReview','reviewCounts','totalReviews','paymentPublishedStatus','paymentGatewayPublishedStatus','payment_gateways_list','digital_payment'));
    }

    public function pujaleadstore(Request $request, $slug)
    {
        
        $servicedata = Service::where('id', $request->service_id)->where('slug', $slug) ->where('product_type', $request->type)
            ->where('status', 1)->first();

        if (!$servicedata) {
            $isAnusthan = ($request->type == 'anushthan') ? 1 : 0;
            $servicedata = Vippooja::where('id', $request->service_id)->where('slug', $slug)->where('is_anushthan', $isAnusthan)
                ->where('status', 1)
                ->first();
        }

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
        if($request->type=='pooja'){
            $forecastServiceId = $nextBooking?->service_id;
        }else{
            $forecastServiceId = $request->service_id;
        }
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
            'add_product_id' => $request->add_product_id ?? [],
            'platform'      => 'web', 
            'payment_status'=> 'pending', 
            'final_amount'      => $request->final_amount,
        ];


        $leads = Leads::create($cust_details);
        $insertedRowId = $leads->id;
        
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
                            'leads_id'      => $leads->id,
                        ]);
                    }
                }
            }
        }

        $typePrefixes = [
            'pooja'     => 'PJ',
            'vip'       => 'VPJ',
            'anushthan' => 'APJ',
        ];
        $prefix = $typePrefixes[$request->type] ?? '';
        if (!empty($insertedRowId)) {
            $leadno = $prefix . (100 + $insertedRowId + 1);
        } else {
            $leadno = $prefix . '101';
        }

        $orderData = Service_order::select('id')->latest()->first();
        if (!empty($orderData['id'])) {
            $orderId = $prefix . (100000 + $orderData['id'] + 1);
        } else {
            $orderId = $prefix . '100001';
        }
        Leads::where('id', $insertedRowId)->update(['leadno' => $leadno,'order_id' => $orderId]);

        $serviceOrderData = Leads::find($leads->id);
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
                'lead_id'      => $serviceOrderData->id,
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

    // Chadhava Book 22/08/2025
   // Chadhava Book 22/08/2025
   public function chadhavabookNow(Request $request, $slug)
   {
       $today = Carbon::today();
       $cutoff = $today->copy()->addDays(7);
       $chadhava = Chadhava::where('slug', $slug)->where('status', 1)->where(function ($query) use ($today) {
           $query->where(function ($q) use ($today) {
               $q->where('chadhava_type', 1)
               ->where('end_date', '>=', $today->toDateString());
           })->orWhere('chadhava_type', 0);
       })->withCount('PoojaOrderReview')->withAvg('review', 'rating')->first();
       if (!$chadhava) {
           abort(404, 'Chadhava not found');
       }
       $nextDate = $chadhava->getNextAvailableDate($cutoff);
       if (!$nextDate) {
           abort(404, 'No upcoming dates available for this Chadhava');
       }

       $chadhavaGet = Chadhava::where('id', $chadhava->id)->where('status',1)->withCount('PoojaOrderReview')->withAvg('review', 'rating')->get();
       $Faqs = \App\Models\FAQ::whereHas('Category', function ($query) {
           $query->where('name', 'Chadhava');
       })->with('Category')->get();
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

           $totalReviews = $chadhavaGet->sum('reviews_count');
           $paymentPublishedStatus = config('get_payment_publish_status');
           $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
           $payment_gateways_list = payment_gateways();
           $digital_payment = getWebConfig(name: 'digital_payment');
           return view('web-views.puja.chadhava-book', compact('chadhava','chadhavaGet','Faqs','serviceReview','reviewCounts',
               'reviewCounts','totalReviews','nextDate','paymentPublishedStatus','paymentGatewayPublishedStatus','payment_gateways_list','digital_payment'));
   }

   public function chadhavaleadstore(Request $request, $slug)
   {
      
       $today   = Carbon::today();
       $cutoff  = $today->copy()->addDays(7);
       $chadhavadata = Chadhava::where('id', $request->service_id)->where('slug', $slug)->where('status', 1)->first();
       if (!$chadhavadata) {
           return redirect()->back()->with('error', 'Chadhava not found.');
       }

       $chadhava = Chadhava::where('slug', $slug)->where('status', 1)
           ->where(function ($query) use ($today) {
               $query->where(function ($q) use ($today) {
                   $q->where('chadhava_type', 1)
                   ->where('end_date', '>=', $today->toDateString());
               })->orWhere('chadhava_type', 0);
           })->withCount('PoojaOrderReview')->withAvg('review', 'rating')->first();

       if (!$chadhava) {
           abort(404, 'Chadhava not found');
       }
       $nextDate = $chadhava->getNextAvailableDate($cutoff);
       if (!$nextDate) {
           abort(404, 'No upcoming dates available for this Chadhava');
       }

       $bookingDate = $request->input('booking_date');
       $personName  = $request->input('person_name');
       $personPhone = $request->input('person_phone');

       if (empty($personPhone)) {
           return redirect()->to('/');
       }

       $nameParts = explode(' ', trim($personName));
       $firstName = $nameParts[0] ?? '';
       $lastName  = implode(' ', array_slice($nameParts, 1));

       // Create or fetch customer
       $user = User::firstOrCreate(
           ['phone' => $personPhone],
           [
               'name'       => $personName,
               'f_name'     => $firstName,
               'l_name'     => $lastName,
               'email'      => 'user@mahakal.com',
               'password'   => bcrypt('12345678'),
               'verify_otp' => 1,
           ]
       );
       $customerId = $user->id;
       if ($user->wasRecentlyCreated) {
           Helpers::whatsappMessage('whatsapp', 'Welcome Message', ['customer_id' => $customerId]);
       }
       
       // create lead
       $cust_details = [
           'service_id'     => $chadhavadata->id,
           'type'           => 'chadhava',
           'product_id'     => $chadhavadata->product_id,
           'package_name'   => $chadhavadata->name,
           'person_phone'   => $personPhone,
           'person_name'    => $personName,
           'booking_date'   => $bookingDate,
           'customer_id'    => $customerId,
           'add_product_id' => $request->add_product_id ?? [],
           'platform'       => 'web',
           'payment_status' => 'pending',
           'final_amount'   => $request->final_amount ?? 0,
       ];

       $leads         = Leads::create($cust_details);
       $insertedRowId = $leads->id;
       
       $leadno   = 'CC' . (100 + $insertedRowId + 1);
       $lastOrder = Chadhava_orders::select('id')->latest()->first();
       $orderId  = 'CC' . (100000 + (($lastOrder->id ?? 0) + 1));
       $leads->update(['leadno' => $leadno, 'order_id' => $orderId]);

       $extraProducts = $request->add_product_id;
       if (!empty($extraProducts)) {
           if (is_string($extraProducts)) {
               $extraProducts = json_decode($extraProducts, true);
           }

           if (is_array($extraProducts)) {
               foreach ($extraProducts as $prod) {
                   $product = Product::find($prod['product_id'] ?? null);
                   if ($product) {
                       $qty        = (int) ($prod['qty'] ?? 1);
                       $unitPrice  = $product->unit_price ?? 0;
                       $finalPrice = $qty * $unitPrice;

                       ProductLeads::create([
                           'leads_id'      => $insertedRowId,
                           'product_id'   => $product->id,
                           'product_name' => $product->name,
                           'product_price'=> $unitPrice,
                           'qty'          => $qty,
                           'final_price'  => $finalPrice,
                       ]);
                   }
               }
           }
       }
     

       $serviceOrderData = Leads::find($insertedRowId);
       if (!$serviceOrderData) {
           abort(500, 'Lead not found after creation');
       }

       Chadhava_orders::create([
           'order_id'          => $serviceOrderData->order_id,
           'customer_id'       => $serviceOrderData->customer_id,
           'service_id'        => $serviceOrderData->service_id,
           'type'              => $serviceOrderData->type,
           'leads_id'          => $serviceOrderData->id,
           'booking_date'      => $serviceOrderData->booking_date,
           'wallet_amount'     => $serviceOrderData->via_wallet ?? 0,
           'transection_amount'=> $serviceOrderData->final_amount,
           'pay_amount'        => $serviceOrderData->final_amount,
       ]);

       $bookDate = date('d, F, l', strtotime($serviceOrderData->booking_date));
       return response()->json([
           'success' => true,
           'data'    => [
               'lead_id'      => $serviceOrderData->id,
               'chadhava_name' => $chadhavadata->name,
               'chadhava_venue' => $chadhavadata->chadhava_venue,
               'order_id'     => $serviceOrderData->order_id,
               'person_name'         => $serviceOrderData->person_name,
               'person_phone'       => $serviceOrderData->person_phone,
               'booking_date' => $bookDate,
               'products'     => ProductLeads::where('leads_id', $insertedRowId)
                               ->get(['product_id', 'product_name as name', 'product_price as price', 'qty'])
                               ->toArray(),
               'total_amount' => $serviceOrderData->final_amount,
           ],
       ]);
   }



}