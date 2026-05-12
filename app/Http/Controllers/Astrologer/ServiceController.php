<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Astrologer\Astrologer as Guruji;
use App\Models\Service_order;
use App\Models\PanditServiceGallery;
use App\Models\Service;
use App\Models\PanditServiceDetail;
use App\Models\Chadhava;
use App\Models\PanditPriceSlab;
use App\Models\PanditServicePackage;
use App\Models\Package;
use App\Models\Vippooja;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Utils\Helpers;
use Intervention\Image\Facades\Image;
use App\Repositories\TranslationRepository;



class ServiceController extends Controller
{
    protected TranslationRepository $translationRepo;

    public function __construct(TranslationRepository $translationRepo)
    {
        $this->translationRepo = $translationRepo;
    }

    public function createNew(Request $request, $id){
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $categories = json_decode($vendor->is_pandit_pooja_category, true) ?? [];
        /* Get already added services for this pandit (type = puja)  */
        $usedServiceIds = PanditPriceSlab::where('pandit_id', $id)->where('type', 'pooja')
            ->pluck('service_id')->unique()->toArray();

        $slabData = PanditPriceSlab::where('status', 1)->where('by_type', 'admin')->orderBy('min_qty')->get();
        $categoryServices = [];
        foreach ($categories as $category) {
            $catId = $category['id'] ?? null;
            if (!$catId) continue;
            $services = Service::where('status', 1)->where('product_type', 'pooja')->whereJsonContains('sub_category_id', (int) $catId)
                ->when(count($usedServiceIds) > 0, function ($q) use ($usedServiceIds) {
                    $q->whereNotIn('id', $usedServiceIds);
                })
                ->get()
                ->map(function ($service) {
                    return [
                        'id'          => $service->id,
                        'name'        => $service->name,
                        'pooja_venue' => $service->pooja_venue,
                        'thumbnail'   => $service->thumbnail
                            ? asset('storage/app/public/pooja/'.$service->thumbnail)
                            : asset('img2.jpg'),
                    ];
                });

            if ($services->isEmpty()) continue;

            $categoryServices[] = [
                'id'       => $catId,
                'name'     => $category['name'] ?? '',
                'services' => $services,
                'slabs'    => $slabData,
            ];
        }
        return view('guruji-views.service.create', compact('categoryServices', 'vendor'));
    }

    public function saveNew(Request $request, $vendorId)
    {
        $vendor    = Guruji::findOrFail($vendorId);
        $serviceId = $request->service_id;
        $singlePrice = 0;
        if ($vendor->type !== 'in house') {
            $singlePrice = (float) ($request->single_price ?? 0);
        }

        // SINGLE PRICE ROW
        $priceRow = PanditPriceSlab::where(['pandit_id'  => $vendorId,'service_id' => $serviceId,'id' => null,])->first();
        if ($priceRow) {
            if ($priceRow->edit_count < 2) {
                $priceRow->single_price = $singlePrice;
                $priceRow->by_type      = $vendor->type;
                $priceRow->added_by     = 'pandit'; // FIXED
                $priceRow->edit_count++;
                $priceRow->save();
            }
        } else {

            PanditPriceSlab::create([
                'pandit_id'    => $vendorId,
                'service_id'   => $serviceId,
                'single_price' => $singlePrice,
                'min_qty'      => 1,
                'max_qty'      => 1,
                'type'         => 'pooja',
                'by_type'      => $vendor->type,
                'added_by'     => 'pandit', //  FIXED
                'edit_count'   => 1,
            ]);
        }

        /**
         * SLAB PRICES
         */
        foreach ($request->slabs ?? [] as $slabId => $slab) {

            PanditPriceSlab::updateOrCreate(
                [
                    'pandit_id'  => $vendorId,
                    'service_id' => $serviceId,
                    'id'    => $slabId,
                ],
                [
                    'type'    => 'pooja',
                    'by_type' => $vendor->type,
                    'added_by'=> 'pandit', //  FIXED
                    'min_qty' => $slab['min_qty'],
                    'max_qty' => $slab['max_qty'],
                    'price'   => (float) $slab['price'],
                ]
            );
        }

        return back()->with('success', 'Service price saved successfully');
    }

    public function PujaList(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $panditSlabs = PanditPriceSlab::where('pandit_id', $vendor->id)->where('type', 'pooja')->get();
        $usedServiceIds = $panditSlabs->pluck('service_id')->unique()->toArray();
        $slabData = PanditPriceSlab::where('status', 1)->where('by_type', 'admin')->orderBy('min_qty')->get();

        /* -----------------------------
         | 3. Pandit prices (view ke liye)
         -----------------------------*/
        $panditPrices = $panditSlabs
            ->groupBy('service_id')
            ->map(function ($rows) {
                $first = $rows->first();
                return [
                    'venue'        => $first->venue,
                    'single_price' => $first->single_price,
                    'slabs'        => $rows->keyBy('slab_id')->map(function ($r) {
                        return ['price' => $r->price];
                    })->toArray(),
                ];
            })
            ->toArray();
    
        /* -----------------------------
         | 4. Category wise services
         -----------------------------*/
        $categories = json_decode($vendor->is_pandit_pooja_category, true) ?? [];
        $categoryServices = [];
    
        foreach ($categories as $category) {
            $catId = $category['id'] ?? null;
            if (!$catId) continue;
    
            $services = Service::where('status', 1)
                ->where('product_type', 'pooja')
                ->whereIn('id', $usedServiceIds) // ⭐ KEY LINE
                ->whereJsonContains('sub_category_id', (int)$catId)
                ->get()
                ->map(function ($service) {
                    return [
                        'id'          => $service->id,
                        'name'        => $service->name,
                        'mini_price'  => $service->mini_price,
                        'price'       => $service->price,
                        'pooja_venue' => $service->pooja_venue,
                        'thumbnail'   => $service->thumbnail
                            ? asset('storage/app/public/pooja/' . $service->thumbnail)
                            : asset('img2.jpg'),
                    ];
                });
    
            if ($services->isEmpty()) continue;
    
            $categoryServices[] = [
                'id'       => $catId,
                'name'     => $category['name'] ?? '',
                'services' => $services,
                'slabs'    => $slabData,
            ];
        }
        // dd($categoryServices,$panditPrices);
        return view('guruji-views.service.puja', compact('vendor','categoryServices','panditPrices'));

    }
    

    public function updatePuja(Request $request, $id)
    {
        $vendor = Guruji::findOrFail($id);
        $serviceId   = $request->service_id;
        $singlePrice = (int) $request->single_price;
    
        /* -------- SINGLE QTY (1–1) -------- */
    
        $singleRow = PanditPriceSlab::where([
            'pandit_id'  => $vendor->id,
            'service_id' => $serviceId,
            'type'       => 'pooja',
            'min_qty'    => 1,
            'max_qty'    => 1,
        ])->first();
    
        if ($singleRow) {
            if ($singleRow->single_price != $singlePrice && $singleRow->edit_count < 2) {
                $singleRow->update([
                    'single_price' => $singlePrice,
                    'edit_count'   => $singleRow->edit_count + 1,
                ]);
            }
        } else {
            PanditPriceSlab::create([
                'pandit_id'    => $vendor->id,
                'service_id'   => $serviceId,
                'single_price'=> $singlePrice,
                'min_qty'      => 1,
                'max_qty'      => 1,
                'type'         => 'pooja',
                'by_type'      => $vendor->type,
                'edit_count'   => 1,
            ]);
        }
    
        /* -------- MULTI QTY SLABS -------- */
    
        foreach ($request->slabs ?? [] as $slab) {
    
            if (empty($slab['min_qty']) || empty($slab['max_qty'])) {
                continue;
            }
    
            $slabRow = PanditPriceSlab::where([
                'pandit_id'  => $vendor->id,
                'service_id' => $serviceId,
                'type'       => 'pooja',
                'min_qty'    => $slab['min_qty'],
                'max_qty'    => $slab['max_qty'],
            ])->first();
    
            if ($slabRow) {
    
                if ($slabRow->price != $slab['price'] && $slabRow->edit_count < 2) {
                    $slabRow->update([
                        'price'      => $slab['price'],
                        'edit_count' => $slabRow->edit_count + 1,
                    ]);
                }
    
            } else {
    
                PanditPriceSlab::create([
                    'pandit_id'  => $vendor->id,
                    'service_id' => $serviceId,
                    'min_qty'    => $slab['min_qty'],
                    'max_qty'    => $slab['max_qty'],
                    'price'      => $slab['price'],
                    'type'       => 'pooja',
                    'by_type'    => $vendor->type,
                    'edit_count' => 1,
                ]);
            }
        }
    
        return back()->with('success', 'Puja price updated successfully');
    }
    
    public function ChadhavaList(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $panditPrices = json_decode($vendor->is_pandit_chadhava ?? '{}', true);
        $panditTimes  = json_decode($vendor->is_pandit_chadhava_time ?? '{}', true);
        $panditCommission  = json_decode($vendor->is_pandit_chadhava_commission ?? '{}', true);
        $services = collect();
        if (!empty($panditPrices)) {
            $services = Chadhava::whereIn('id', array_keys($panditPrices))->where('status', 1)->get();
        }
        return view('guruji-views.service.chadhava', compact('services','panditPrices','panditTimes','panditCommission'));
    }

    public function updateChadhava(Request $request)
    {
        $gurujiId = auth('guruji')->id();
        if (!$gurujiId) {
            Toastr::warning('Unauthorized access');
            return redirect()->back();
        }
        $vendor = Guruji::findOrFail($gurujiId);
        $prices      = $request->prices ?? [];
        $times       = $request->times ?? [];
        $commissions = $request->commission ?? [];
        if ($vendor->type === 'in house') {
            foreach ($prices as $key => $value) {
                $prices[$key] = 0;
            }
            foreach ($commissions as $key => $value) {
                $commissions[$key] = 0;
            }
        }
        $vendor->is_pandit_chadhava            = json_encode($prices);
        $vendor->is_pandit_chadhava_time       = json_encode($times);
        $vendor->is_pandit_chadhava_commission = json_encode($commissions);
        $vendor->save();
        Toastr::success('Chadhava updated successfully');
        return redirect()->back();
    }

    public function CounsellingList(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $panditPrices = json_decode($vendor->consultation_charge ?? '{}', true);
        $panditTimes  = json_decode($vendor->consultation_commission ?? '{}', true);
        $services = collect();
        if (!empty($panditPrices)) {
            $services = Service::whereIn('id', array_keys($panditPrices))->where('product_type', 'counselling')->with('category')->get();
        }
        return view('guruji-views.service.counselling', compact('services','panditPrices','panditTimes'));
    }
    public function updateCounselling(Request $request)
    {
        $gurujiId = auth('guruji')->id();   

        if (!$gurujiId) {
            Toastr::warning('Unauthorized access');
            return redirect()->back();
        }

        $vendor = Guruji::findOrFail($gurujiId);
        if ($vendor->type === 'in house') {
            if (!empty($request->prices) || !empty($request->commission)) {
                Toastr::error('You are not allowed to edit the price');
                return redirect()->back();
            }
            $vendor->consultation_commission = json_encode([]);
            $vendor->consultation_charge     = json_encode([]);
        } 
        else {
            $vendor->consultation_commission = json_encode($request->commission ?? []);
            $vendor->consultation_charge     = json_encode($request->prices ?? []);
        }

        $vendor->save();

        Toastr::success('Counselling updated successfully');
        return redirect()->back();
    }
    // ---------------------------Guruji Individual puja,Counselling,Gallery,Details for Puja Vaneu---------------------------------
    //  Guruji Puja Start
    public function IndividualPujaList(Request $request, $id)
    {

        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $packages = Package::where(function ($q) use ($id) {
                $q->where('pandit_id', $id)
                ->orWhere(function ($q2) {
                    $q2->whereNull('pandit_id')
                        ->where('type', 'panditpooja');
                });
            })->with('translations')->latest()->get();
        if ($packages->isEmpty()) {
            Toastr::error(translate('no_package_available'));
            return back();
        }
        $services = Service::select('id', 'name', 'pooja_venue', 'thumbnail','sub_category_id')
        ->where('status', 1)
        ->where('product_type', 'pooja')
        ->get();
        $groupedPackages = PanditServicePackage::where('pandit_id', $id)->where('type', 'pooja')->get()->groupBy('service_id');
        $activeCounselling = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'counselling')
            ->distinct('service_id')
            ->count('service_id');
        $activeServices = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'pooja')
            ->distinct('service_id')
            ->count('service_id');
        return view('guruji-views.service.individual.puja-individual',compact('vendor', 'packages', 'services', 'groupedPackages','activeServices','activeCounselling')
        );
    } 

    public function PujaIndividualSave(Request $request, $id)
    {
        $panditId   = $request->pandit_id;
        $rowIds     = $request->row_id;
        $services   = $request->service_id;
        $packages   = $request->package_id;
        $prices     = $request->price;
        $statusList = $request->status_hidden;
        $serviceIds = $request->service_id;
        $thumbnails = $request->thumbnail;

        $finalThumbnail = [];
        $thumbPointer = 0;  
        $assignedThumb = [];

        foreach ($serviceIds as $index => $serviceId) {
            if (array_key_exists($serviceId, $assignedThumb)) {
                $finalThumbnail[$index] = $assignedThumb[$serviceId];
                continue;
            }

            $currentThumb = $thumbnails[$thumbPointer] ?? null;
            $assignedThumb[$serviceId] = $currentThumb;
            $finalThumbnail[$index] = $currentThumb;
            $thumbPointer++;
        }

        // $keepIds = [];
        foreach ($packages as $key => $packageId) {

            if (empty($packageId)) continue;
        
            $rowId  = $rowIds[$key] ?? null;
            $status = $statusList[$key] ?? 0;
        
            if ($rowId) {
        
                $records = PanditServicePackage::find($rowId);
                $thumbnail = $records->thumbnail ?? null;

                if (!empty($finalThumbnail[$key])) {
                
                    $file = $finalThumbnail[$key];
                
                    // filename: 2025-12-11-xxxx.webp
                    $filename = date('Y-m-d') . '-' . uniqid() . '.webp';
                
                    $path = storage_path('app/public/astrologers/service-thumbnail/' . $filename);
                
                    // convert to webp & save
                    Image::make($file)
                        ->encode('webp', 90)
                        ->save($path);
                
                    $thumbnail = $filename;
                }
        
                $records->update([
                    'package_id' => $packageId,
                    'price'      => $prices[$key],
                    'thumbnail'  => $thumbnail,
                    'status'     => $status,
                ]);
        
            } else {
        
                $thumbnail = null;

                if (!empty($finalThumbnail[$key])) {

                    $file = $finalThumbnail[$key];
                    $filename = date('Y-m-d') . '-' . uniqid() . '.webp';
                    $path = storage_path('app/public/astrologers/service-thumbnail/' . $filename);

                    Image::make($file)
                        ->encode('webp', 90)
                        ->save($path);

                    $thumbnail = $filename;
                }

        
                PanditServicePackage::create([
                    'pandit_id'  => $panditId,
                    'service_id' => $services[$key],
                    'package_id' => $packageId,
                    'price'      => $prices[$key],
                    'thumbnail'  => $thumbnail,
                    'type'       => 'puja',
                    'status'     => $status,
                ]);
            }
        }
        Toastr::success('Pandit packages updated successfully');
         return back()->with('success', 'Individual Puja price updated successfully');
    }
    // Guruji Puja End
    // Guruji Counselling Start
    public function IndividualCounsellingList(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $services = Service::where('status', 1)->where('product_type', 'counselling')->where('status',1)->get();
        $panditCounsellings = PanditServicePackage::where('pandit_id', $id)->where('type', 'counselling')->get();
        $activeCounselling = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'counselling')
            ->distinct('service_id')
            ->count('service_id');
        $activeServices = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'pooja')
            ->distinct('service_id')
            ->count('service_id');
        return view('guruji-views.service.individual.counselling-individual',compact('vendor', 'services', 'panditCounsellings','activeServices','activeCounselling'));
    }

 
    public function CounsellingIndividualSave(Request $request, $id)
    {
        $panditId   = $id;
        $method     = $request->method;
        $services   = $request->service_id ?? [];
        $thumbnails = $request->file('thumbnail') ?? [];
        $prices     = $request->price ?? [];
        $updateIds  = $request->update_id ?? [];
    
        $uploadPath = storage_path('app/public/astrologers/service-thumbnail/');
    
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        /* ================= UPDATE ================= */
        if ($method === 'update') {
            foreach ($updateIds as $index => $packageId) {
                $record = PanditServicePackage::find($packageId);
                if (!$record) continue;
                $price     = $prices[$index] ?? $record->price;
                $thumbnail = $record->thumbnail;
                if (!empty($thumbnails[$index])) {
                    $file     = $thumbnails[$index];
                    $filename = date('Y-m-d') . '-' . uniqid() . '.webp';
    
                    Image::make($file)
                        ->encode('webp', 90)
                        ->save($uploadPath . $filename);
    
                    $thumbnail = $filename;
                }
    
                $record->update([
                    'price'     => $price,
                    'thumbnail' => $thumbnail,
                ]);
            }
    
            Toastr::success('Pandit counselling updated successfully');
        }
    
        /* ================= CREATE ================= */
        else {
    
            foreach ($services as $index => $serviceId) {
    
                if (empty($serviceId)) continue;
    
                $price     = $prices[$index] ?? 0;
                $thumbnail = null;
    
                if (!empty($thumbnails[$index])) {
    
                    $file     = $thumbnails[$index];
                    $filename = date('Y-m-d') . '-' . uniqid() . '.webp';
    
                    Image::make($file)
                        ->encode('webp', 90)
                        ->save($uploadPath . $filename);
    
                    $thumbnail = $filename;
                }
    
                PanditServicePackage::create([
                    'pandit_id'  => $panditId,
                    'type'       => 'counselling',
                    'service_id' => $serviceId,
                    'price'      => $price,
                    'thumbnail'  => $thumbnail,
                ]);
            }
    
            Toastr::success('Pandit counselling created successfully');
        }
    
        return back();
    }
    // Guruji Counselling End
    // Guruji Gallery Start
    public function IndividualGalleryList(Request $request,$id)
    {
        $vendor = Guruji::where('id', $id)->first();
        $gallery = PanditServiceGallery::where('pandit_id', $id)->with('translations')->first();
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $activeCounselling = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'counselling')
            ->distinct('service_id')
            ->count('service_id');
        $activeServices = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'pooja')
            ->distinct('service_id')
            ->count('service_id');
        return view('guruji-views.service.individual.gallery-individual', compact('vendor','gallery','languages','defaultLanguage','activeServices','activeCounselling'));
    }
   
    public function upload(string $dir, string $format, $image): string
    {
        $path = storage_path('app/public/' . $dir);

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $fileName = date('YmdHis') . '_' . Str::random(10) . '.' . $format;

        Image::make($image)
            ->encode($format, 90)
            ->save($path . $fileName);

        return $fileName;
    }


    public function getProcessedImages(object $request): array
    {
        $imageNames = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {

                $fileName = $this->upload(
                    dir: 'astrologers/gallery/',
                    format: 'webp',
                    image: $image
                );

                $imageNames[] = [
                    'image_name' => $fileName,
                ];
            }
        }

        return [
            'image_names' => $imageNames
        ];
    }

    
    public function storeGallery(Request $request)
    {
        $gallery = PanditServiceGallery::where('pandit_id', $request->pandit_id)
            ->with('translations')
            ->first();
    
        $processedImages = $this->getProcessedImages($request);
    
        if ($gallery) {
    
            $array = array_merge(json_decode($gallery['images']),$processedImages['image_names']);
            $galleryUpdate = PanditServiceGallery::where('pandit_id', $request->pandit_id)->first();
            $galleryUpdate->title = $request['title'][array_search('en', $request['lang'])];
            $galleryUpdate->images = json_encode($array);
            $galleryUpdate->save();
    
            $this->translationRepo->update(
                request: $request,
                model: PanditServiceGallery::class,
                id: $gallery->id
            );
    
            return response()->json(['success' => 1, 'message' => translate('Update_Image_successfully')]);
        }
    
        $gallery = PanditServiceGallery::create([
            'pandit_id' => $request->pandit_id,
            'title'     => $request['title'][array_search('en', $request['lang'])],
            'images'    => json_encode($processedImages['image_names']),
        ]);
    
        $this->translationRepo->add(
            request: $request,
            model: PanditServiceGallery::class,
            id: $gallery->id
        );
    
        return response()->json(['success' => 1, 'message' => translate('Add_Image_successfully')]);
    }
    

    public function image_remove($data,$name){
        $images = [];
        $removeImage = '';
        foreach (json_decode($data['images']) as $image) {
            if ($image != $name) {
                $images[] = $image;
            }else{
                $removeImage = $image;
            }
        }
        return [
            'images' => $images,
        ];
    }

    public function deleteGallery($id, $key)
    {
        $findData = PanditServiceGallery::find($id);

        if (!$findData) {
            return response()->json(['success' => 0], 404);
        }

        $images = json_decode($findData->images, true);
        $name = $images[$key] ?? null;

        if ($name) {
            unset($images[$key]);

            // reindex array
            $findData->images = json_encode(array_values($images));
            $findData->save();
        }

        return response()->json([
            'success' => 1,
            'message' => translate('Remove_Image_successfully')
        ], 200);
    }
    // Guruji Gallery End
    // Details Start For the Puja Vaneu
    public function addDetail($id)
    {
        $vendor = Guruji::where('id', $id)->first();
        $panditDetail = PanditServiceDetail::where('pandit_id', $id)->with('translations')->get();
        $groupedDetails = [];
        foreach ($panditDetail as $pd) {
            $groupedDetails[$pd->service_id][] = $pd;
        }
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $activeCounselling = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'counselling')
            ->distinct('service_id')
            ->count('service_id');
         $activeServices = PanditServicePackage::where('pandit_id', $id)
            ->where('type', 'pooja')
            ->distinct('service_id')
            ->count('service_id');
        return view('guruji-views.service.individual.details-individual', compact('vendor', 'groupedDetails', 'panditDetail','languages', 'defaultLanguage','activeServices','activeCounselling'));
    }

    public function storeDetail(Request $request)
    {
        $panditId  = $request->pandit_id;
        $services  = $request->service_id;
        $allLang   = $request->lang;        
        $addresses = $request->address;     
        $method    = $request->method;

        foreach ($services as $serviceId) {

            $langWise = $addresses[$serviceId];

            // DYNAMIC MAPPING
            $mapped = [];
            foreach ($allLang as $i => $code) {
                $mapped[$code] = $langWise[$i] ?? null;
            }

            // DEFAULT LANGUAGE
            $defaultLanguage = 'en';
            $defaultAddress  = $mapped[$defaultLanguage] ?? reset($mapped);

            // SAVE BASE ROW
            $savedService = PanditServiceDetail::updateOrCreate(
                [
                    'pandit_id'  => $panditId,
                    'service_id' => $serviceId,
                ],
                [
                    'address' => $defaultAddress,
                ]
            );

            // REMOVE DEFAULT LANGUAGE FROM TRANSLATION DATA
            $translationLang    = [];
            $translationAddress = [];

            foreach ($mapped as $code => $value) {
                if ($code !== $defaultLanguage) {
                    $translationLang[]    = $code;
                    $translationAddress[] = $value;
                }
            }

            // PREPARE TRANSLATION REQUEST
            $translationRequest = new Request([
                'lang'    => $translationLang,
                'address' => $translationAddress,
            ]);

            // SAVE TRANSLATIONS
            if($method == 'save'){
                $this->translationRepo->add(
                    request: $translationRequest,
                    model: PanditServiceDetail::class,
                    id: $savedService->id
                );
            } else{
                $this->translationRepo->update(
                    request: $translationRequest,
                    model: PanditServiceDetail::class,
                    id: $savedService->id
                );
            }
        }

        return back()->with('success', 'Service detail saved successfully!');
    }
}