<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Astrologer\Astrologer as Guruji;
use App\Models\Service_order;
use App\Models\ServiceTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $gurujiId = auth('guruji')->id();

        // ================= BASIC COUNTS =================
        $serviceOrders = Service_order::where('pandit_assign', $gurujiId)->get();

        $totalOrders = $serviceOrders->count();

        $panditPujaOrders = Service_order::where('pandit_assign', $gurujiId)
            ->where('type', 'panditpooja')
            ->count();

        $panditCounsellingOrders = Service_order::where('pandit_assign', $gurujiId)
            ->where('type', 'panditcounselling')
            ->count();

        $ritualOrders = Service_order::where('pandit_assign', $gurujiId)
            ->whereIn('type', ['pooja', 'vip', 'anushthan'])
            ->count();

        $counselling = Service_order::where('pandit_assign', $gurujiId)
            ->where('type', 'counselling')
            ->count();

        $todayOrders = $serviceOrders->whereBetween('created_at', [
            now()->startOfDay(),
            now()->endOfDay()
        ])->count();


        // ================= SERVICE ORDER EARNING (RELATION) =================
        $orderEarnings = Service_order::where('pandit_assign', $gurujiId)
            ->where(function ($q) {
                $q->where('status', 1)
                ->orWhere('order_status', 1);
            })
            ->with('serviceTransactions')
            ->get()
            ->flatMap(function ($order) {
                return $order->serviceTransactions->map(function ($t) use ($order) {
                    return (float) $order->package_price - (float) $t->commission;
                });
            });

        $serviceOrderEarning = $orderEarnings->sum();
        $completedOrderCount = $orderEarnings->count();


        // ================= VIDEO / AUDIO / CHAT EARNING =================
        $astroServiceEarning = ServiceTransaction::where('astro_id', $gurujiId)
        ->whereIn('type', ['video', 'audio', 'chat'])
        ->get()
        ->sum(function ($t) {
            return (float) $t->amount - (float) $t->commission;
        });
    


        // ================= FINAL TOTAL EARNING =================
        $totalNetEarning = $serviceOrderEarning + $astroServiceEarning;


        return view('guruji-views.dashboard.index', compact(
            'serviceOrders',
            'totalOrders',
            'todayOrders',
            'panditPujaOrders',
            'panditCounsellingOrders',
            'ritualOrders',
            'counselling',
            'totalNetEarning',
            'completedOrderCount'
        ));
    }
    public function profileindex()
    {
        $gurujiId = auth('guruji')->id();
        if (!$gurujiId) {
            Toastr::warning(translate('unauthorized_access'));
            return redirect()->back();
        }
        $vendor = Guruji::where('id', $gurujiId)->first();
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->back();
        }
        $gallery = $vendor->banner ?? null;
        return view('guruji-views.dashboard.profile', compact('vendor', 'gallery'));
    }

    public function basic_info_update(Request $request,$id){
        
        if (auth('guruji')->id() != $id) {
            Toastr::warning(translate('unauthorized_access'));
            return redirect()->back();
        }
    
        $vendor = Guruji::findOrFail($id);
        if ($request->hasFile('image')) {
            // old image delete
            if (!empty($vendor->image) && file_exists(storage_path('app/public/astrologers/' . $vendor->image))) {
                unlink(storage_path('app/public/astrologers/' . $vendor->image));
            }
            $imageName = time() . '-astrologer.' . $request->image->extension();
            $request->image->storeAs('app/public/astrologers', $imageName);

            $vendor->image = $imageName;
        }
        $vendor->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'gender'  => $request->gender,
            'dob'     => $request->dob,
            'address' => $request->address,
        ]);
    
        Toastr::success(translate('profile_updated_successfully'));
        return redirect()->back();
    }
    public function password_update(Request $request, $id)
    {
        if (auth('guruji')->id() != $id) {
            Toastr::warning(translate('unauthorized_access'));
            return redirect()->back();
        }
        $vendor = Guruji::findOrFail($id);
        $key = 'password-update:' . auth('guruji')->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            Toastr::error("Too many attempts. Try again after {$seconds} seconds.");
            return redirect()->back();
        }
        if (!Hash::check($request->old_password, $vendor->password)) {
            RateLimiter::hit($key, 900);
            $remaining = 3 - RateLimiter::attempts($key);
            Toastr::error("Old password is incorrect. Attempts left: {$remaining}");
            return redirect()->back();
        }
        RateLimiter::clear($key);
        $vendor->password = Hash::make($request->password);
        $vendor->save();
        Toastr::success('Password updated successfully');
        return redirect()->back();
    }

        public function account_update(Request $request, $id)
        {
            if (auth('guruji')->id() != $id) {
                Toastr::warning(translate('unauthorized_access'));
                return redirect()->back();
            }
            $vendor = Guruji::findOrFail($id);
            /* ================= VALIDATION ================= */
            $validator = Validator::make($request->all(), [
                'holder_name'         => 'required|string|max:255',
                'bank_name'           => 'required|string|max:255',
                'account_no'          => 'required|digits_between:9,18',
                'confirm_account_no'  => 'required|same:account_no',
                'bank_ifsc'           => ['required','regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
                'bank_passbook_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'confirm_account_no.same' => 'Account number does not match',
                'bank_ifsc.regex'         => 'Invalid IFSC code format',
            ]);
            if ($validator->fails()) {
                Toastr::error($validator->errors()->first());
                return redirect()->back()->withErrors($validator)->withInput();
            }
            /* ================= IMAGE UPLOAD ================= */
            if ($request->hasFile('bank_passbook_image')) {

                if (!empty($vendor->bank_passbook_image) &&
                    file_exists(storage_path('app/public/astrologers/bankpassbook/' . $vendor->bank_passbook_image))) {
                    unlink(storage_path('app/public/astrologers/bankpassbook/' . $vendor->bank_passbook_image));
                }
            
                $imageName = time() . '-passbook.' . $request->bank_passbook_image->extension();
                $request->bank_passbook_image->storeAs('public/astrologers/bankpassbook', $imageName);
            
                $vendor->bank_passbook_image = $imageName;
            }
            
            /* ================= UPDATE DATA ================= */
            $vendor->update([
                'holder_name' => $request->holder_name,
                'bank_name'   => $request->bank_name,
                'account_no'  => $request->account_no,
                'bank_ifsc'   => $request->bank_ifsc,
            ]);
            Toastr::success(translate('account_details_updated_successfully'));
            return redirect()->back();
        }

    
}
