<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Jaap;
use App\Models\JaapCount;
use App\Models\UserMantra;
use App\Models\RamLekhan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use App\Utils\Helpers;
use App\Models\Sankalp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class JaapController extends Controller
{

    public function getAllJaap()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized user'
            ], 401);
        }

        $jaaps = Jaap::where('status', 1)
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')          // Admin jaap
                ->orWhere('user_id', $user->id); // User jaap
            })
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 0 ELSE 1 END') // Admin first
            ->orderByDesc('id') // User ke jaap DESC me
            ->select('id', 'user_id', 'name', 'mantra', 'image')
            ->get();

        if ($jaaps->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No jaap available.'
            ], 404);
        }

        
        $data = $jaaps->map(function ($item) {
        $translations = $item->translations()->pluck('value', 'key')->toArray();
            return [
                'id'     => $item->id,
                'jaap'     => $item->name,
                'hi_name' => $translations['name'] ?? null,
                'mantra' => $item->mantra,
                'hi_mantra' => $translations['mantra'] ?? null,
                'image'  => $item->image,
                'type'   => $item->user_id ? 'user' : 'admin',
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data
        ], 200);
    }

    public function getMantraByJaap($id)
    {
        
        $jaap = Jaap::where('status', 1)
            ->where('id', $id)
            ->select('id', 'mantra', 'image', 'created_at', 'updated_at')
            ->get();

        if ($jaap->isNotEmpty()) {
            $data = $jaap->map(function ($item) {
                
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                return [
                    'id' => $item->id,
                    'en_mantra' => $item->mantra,
                    'hi_mantra' => $translations['mantra'] ?? null,
                    'image' => $item->image,
                ];
            });

            $filteredData = $data->filter(function ($item) {
                return !empty($item['en_mantra']);
            })->values();

            if ($filteredData->isEmpty()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No mantra found.',
                ]);
            }

            return response()->json([
                'status' => 200,
                'data' => $filteredData,
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' => 'No mantra available.',
        ]);
    }

    public function jaapCount(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (!$user || empty($user->id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $type = $request->input('type');
        $name = $request->input('name');
        $location = $request->input('location');
        $count = $request->input('count');
        $duration = $request->input('duration');
        $date = $request->input('date');
        $time = $request->input('time');

        if (empty($type) || empty($name) || empty($location) || empty($count) || empty($duration) || empty($date) || empty($time)) {
            return response()->json(['status' => 400, 'message' => 'Invalid request parameters'], 400);
        }

        $item = new JaapCount();
        $item->user_id = $user->id; 
        $item->type = $type;
        $item->name = $name;
        $item->location = $location;
        $item->count = $count;
        $item->duration = $duration;
        $item->date = $date;
        $item->time = $time;
        $item->save();

        return response()->json(['status' => 200, 'message' => 'created successfully']);
    }

    public function getJaapCount(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (!$user || empty($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $jaapData = JaapCount::where('user_id', $user->id)->get();

        if ($jaapData->isEmpty()) {
            return response()->json(['status' => 404, 'message' => 'No data found'], 404);
        }

        $totalCount = $jaapData->sum('count');

        return response()->json([
            'status' => 200,
            'data' => $jaapData,
            'total_count' => $totalCount 
        ], 200);
    }

    public function RamLekhan(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (!$user || empty($user->id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $type = $request->input('type');
        $name = $request->input('name');
        $location = $request->input('location');
        $count = $request->input('count');
        $duration = $request->input('duration');
        $date = $request->input('date');
        $time = $request->input('time');

        if (empty($type) || empty($name) || empty($location) || empty($count) || empty($duration) || empty($date) || empty($time)) {
            return response()->json(['status' => 400, 'message' => 'Invalid request parameters'], 400);
        }

        $item = new RamLekhan();
        $item->user_id = $user->id; 
        $item->type = $type;
        $item->name = $name;
        $item->location = $location;
        $item->count = $count;
        $item->duration = $duration;
        $item->date = $date;
        $item->time = $time;
        $item->save();

        return response()->json(['status' => 200, 'message' => 'created successfully']);
    }

    public function getRamLekhan(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (!$user || empty($user->id)) {
            return response()->json(['message' => 'Unauthorized']);
        }

        $Data = RamLekhan::where('user_id', $user->id)->get();

        if ($Data->isEmpty()) {
            return response()->json(['status' => 400, 'message' => 'No data found'], 400);
        }

        $totalCount = $Data->sum('count');

        return response()->json([
            'status' => 200,
            'data' => $Data,
            'total_count' => $totalCount 
        ], 200);
    }

    public function deleteRamLekhan(Request $request, $id): JsonResponse
    {

        $item = RamLekhan::where('id', $id)->first();

        $item->delete();

        return response()->json(['status' => 200, 'message' => 'Data Deleted successfully']);
    }

    public function deleteJaapCount(Request $request, $id): JsonResponse
    {

        $item = JaapCount::where('id', $id)->first();

        $item->delete();

        return response()->json(['status' => 200, 'message' => 'Data Deleted successfully']);
    }


    public function storemantra(Request $request)
    {
        // Basic required check
        if (!$request->user_id || !$request->mantra) {
            return response()->json([
                'status'  => false,
                'message' => 'user_id and mantra are required'
            ], 422);
        }

        // User exist check
        $user = User::where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid user id. User does not exist.'
            ], 404);
        }

        // Save mantra
        $data = Jaap::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'mantra'  => $request->mantra,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Mantra added successfully',
            'data'    => $data
        ], 200);
    }

    public function storeSankalp(Request $request)
    {
        // ================= AUTH USER =================
        $authUser = Auth::guard('api')->user();

        if (!$authUser) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ================= VALIDATION =================
        $validator = Validator::make($request->all(), [
            'sankalp_name'     => 'required|string|max:255',
            'user_mantras_id'  => 'required|integer|exists:jaaps,id',
            'hours'            => 'required|integer|min:1',
            'count'            => 'required|integer|min:1',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'start_time'       => 'nullable',
            'end_time'         => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ================= ACTIVE USER CHECK =================
        $user = User::where('id', $authUser->id)
            ->where('is_active', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User inactive or not found'
            ], 422);
        }

        // ================= MANTRA OWNERSHIP CHECK =================
        $mantra = Jaap::where('id', $request->user_mantras_id)
            ->where('status', 1)
            ->where(function ($q) use ($authUser) {
                $q->whereNull('user_id')              // admin mantra
                ->orWhere('user_id', $authUser->id); // user ka khud ka mantra
            })
            ->first();

        if (!$mantra) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid mantra access'
            ], 403);
        }

        // ================= ORDER ID GENERATE =================
        $year  = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');

        $lastOrder = Sankalp::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastOrder && $lastOrder->order_id
            ? ((int) substr($lastOrder->order_id, -4)) + 1
            : 1;

        $orderId = 'JAAP-SANKALP-' . $year . '-' . $month . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // ================= DAYS CALCULATION =================
        $days = Carbon::parse($request->start_date)
            ->diffInDays(Carbon::parse($request->end_date)) + 1;

        // ================= CREATE SANKALP =================
        $sankalp = Sankalp::create([
            'sankalp_name'    => $request->sankalp_name,
            'user_id'         => $authUser->id, // 🔒 ONLY AUTH USER
            'user_mantras_id' => $request->user_mantras_id,
            'hours'           => $request->hours,
            'count'           => $request->count,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'day'             => $days,
            'order_id'        => $orderId,
        ]);

        // ================= WHATSAPP =================
        $message_data = [
            'service_name' => $mantra->mantra,
            'booking_date' => $sankalp->created_at->format('d-m-Y'),
            'start_date'   => $sankalp->start_date,
            'end_date'     => $sankalp->end_date,
            'orderId'      => $orderId,
            'customer_id'  => $authUser->id,
        ];

        Helpers::whatsappMessage('whatsapp', 'Jaap_Sankalp', $message_data);

        return response()->json([
            'status'  => true,
            'message' => 'Sankalp created successfully',
            'data'    => $sankalp
        ], 200);
    }
}