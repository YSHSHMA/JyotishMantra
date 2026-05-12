<?php

namespace App\Http\Controllers\Astrologer;


use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Translation;
use App\Models\Astrologer\Astrologer as Guruji;
use App\Contracts\Repositories\TranslationRepositoryInterface;

class PackageController extends Controller
{
    // IndividualList Create List
    public function packageCreate(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }

        $languages = getWebConfig(name: 'pnc_language') ?? [];
        $defaultLanguage = $languages[0] ?? 'en';

        $packages = Package::where(function ($q) use ($vendor) {
            $q->where('pandit_id', $vendor->id)
              ->orWhere(function ($q2) {
                  $q2->whereNull('pandit_id')
                     ->where('type', 'panditpooja');
              });
        })
        ->orderBy('id', 'desc')
        ->with('translations')
        ->get();
    
        return view(
            'guruji-views.package.create',
            compact('vendor', 'languages', 'defaultLanguage', 'packages')
        );
    }


    public function packageStore(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }

        $defaultLang = 'en';
        // Extract EN title & description
        $enTitle = null;
        $enDescription = null;

        foreach ($request->lang as $index => $lang) {
            if ($lang === $defaultLang) {
                $enTitle = $request->title[$index] ?? null;
                $enDescription = $request->description[$index] ?? null;
                break;
            }
        }

        // MAIN TABLE (ONLY ENGLISH)
        $package = PanditPackage::updateOrCreate(
            ['id' => $request->package_id],
            [
                'guruji_id'   => $vendor->id,
                'pandit_id'   => $request->pandit_id,
                'title'       => $enTitle,
                'description' => $enDescription,
                'person'      => $request->person,
                'type'        => $request->type,
                'color'       => $request->color,
                'position'    => $request->position ?? 0,
                'status'      => 1,
            ]
        );

        // TRANSLATION TABLE (ONLY NON-ENGLISH)
        foreach ($request->lang as $index => $lang) {

            if ($lang === $defaultLang) {
                continue; //  English skip
            }

            if (!empty($request->title[$index])) {
                Translation::updateOrCreate(
                    [
                        'translationable_type' => PanditPackage::class,
                        'translationable_id'   => $package->id,
                        'locale'               => $lang,
                        'key'                  => 'title',
                    ],
                    [
                        'value' => $request->title[$index],
                    ]
                );
            }

            if (!empty($request->description[$index])) {
                Translation::updateOrCreate(
                    [
                        'translationable_type' => PanditPackage::class,
                        'translationable_id'   => $package->id,
                        'locale'               => $lang,
                        'key'                  => 'description',
                    ],
                    [
                        'value' => $request->description[$index],
                    ]
                );
            }
        }

        Toastr::success(
            $request->package_id
                ? translate('package_updated_successfully')
                : translate('package_added_successfully')
        );

        return redirect()->back();
    } 

}
