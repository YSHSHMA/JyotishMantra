<?php

namespace App\Http\Controllers\Admin\Pandit;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pandit\Pandit;
use App\Models\Pandit\PanditAvailability;
use App\Models\Pandit\PanditExperties;
use App\Models\Service;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;


class PanditController extends Controller
{
    // pandit functions
    public function pending_list(){
        $pendingPandit = Pandit::where('status',0)->paginate(10);
        return view('admin-views.pandit.manage.pending-list',compact('pendingPandit'));
    }

    public function pandit_list(){
        $verfiedPandit = Pandit::where('status',1)->paginate(10);
        return view('admin-views.pandit.manage.list',compact('verfiedPandit'));
    }

    public function pandit_pooja(Request $request){
        $ids = $request->input('id');
        $pooja = "";
        if(!empty($ids)){
            $pooja = Service::whereIn('sub_category_id', $ids)->get();
        }
        return response()->json(['status'=>200,'pooja'=>$pooja]);
    }
    
    public function pandit_create(){
        $experties = PanditExperties::where('status',1)->get();
        $categories = Category::where('parent_id',33)->get();
        return view('admin-views.pandit.manage.add', compact('experties','categories'));
    }

    public function pandit_store(Request $request){
        // image
        $image = $request->file('image');
        $imageName = time() . '-pandit' . $image->getClientOriginalName();
        $image->storeAs('public/pandit/profile',$imageName);

        // qualification image
        $qualificationImage = $request->file('qualification_image');
        $qualificationImageName = time() . '-qualification' . $qualificationImage->getClientOriginalName();
        $qualificationImage->storeAs('public/pandit/qualification',$qualificationImageName);

        // availability
        $sunday = [];
        foreach($request->sunday_from as $sf_key=>$sf){
            if($sf!=null && $request->sunday_to[$sf_key]!=null){
                $sunday[$sf_key] = $sf .'-'. $request->sunday_to[$sf_key];
            }
        }
        
        $monday = [];
        foreach($request->monday_from as $mf_key=>$mf){
            if($mf!=null && $request->monday_to[$mf_key]!=null){
                $monday[$mf_key] = $mf .'-'. $request->monday_to[$mf_key];
            }
        }
        
        $tuesday = [];
        foreach($request->tuesday_from as $tuf_key=>$tuf){
            if($tuf!=null && $request->tuesday_to[$tuf_key]!=null){
                $tuesday[$tuf_key] = $tuf .'-'. $request->tuesday_to[$tuf_key];
            }
        }

        $wednesday = [];
        foreach($request->wednesday_from as $w_key=>$w){
            if($w!=null && $request->wednesday_to[$w_key]!=null){
                $wednesday[$w_key] = $w .'-'. $request->wednesday_to[$w_key];
            }
        }

        $thursday = [];
        foreach($request->thursday_from as $tf_key=>$tf){
            if($tf!=null && $request->thursday_to[$tf_key]!=null){
                $thursday[$tf_key] = $tf .'-'. $request->thursday_to[$tf_key];
            }
        }

        $friday = [];
        foreach($request->friday_from as $ff_key=>$ff){
            if($ff!=null && $request->friday_to[$ff_key]!=null){
                $friday[$ff_key] = $ff .'-'. $request->friday_to[$ff_key];
            }
        }

        $saturday = [];
        foreach($request->saturday_from as $stf_key=>$stf){
            if($stf!=null && $request->saturday_to[$stf_key]!=null){
                $saturday[$stf_key] = $stf .'-'. $request->saturday_to[$stf_key];
            }
        }

        $pandit = new Pandit;
        $pandit->name = $request->name;
        $pandit->email = $request->email;
        $pandit->password = Hash::make($request->password);
        $pandit->image = $imageName;
        $pandit->mobile_no = $request->mobile_no;
        $pandit->gender = $request->gender;
        $pandit->dob = $request->dob;
        $pandit->maritial = $request->maritial;
        $pandit->city = $request->city;
        $pandit->address = $request->address;
        $pandit->bio = $request->bio;
        $pandit->qualification = $request->qualification;
        $pandit->college = $request->college;
        $pandit->qualification_image = $qualificationImageName;
        $pandit->language_known = json_encode($request->language_known);
        $pandit->category = json_encode($request->category);
        $pandit->pooja = json_encode($request->pooja);
        $pandit->experties = json_encode($request->experties);
        $pandit->experience = $request->experience;
        $pandit->business_source = $request->business_source;
        $pandit->instagram = $request->instagram;
        $pandit->facebook = $request->facebook;
        $pandit->linkedin = $request->linkedin;
        $pandit->youtube = $request->youtube;
        $pandit->website = $request->website;
        $pandit->panda = $request->panda;
        $pandit->gotra = $request->gotra;
        $pandit->primary_mandir = $request->primary_mandir;
        $pandit->primary_mandir_location = $request->primary_mandir_location;
        if($pandit->save()){
            $panditId = Pandit::select('id')->latest()->first();
            if($panditId){
                $avalability = new PanditAvailability;
                $avalability->pandit_id = $panditId['id'];
                $avalability->sunday = json_encode($sunday);
                $avalability->monday = json_encode($monday);
                $avalability->tuesday = json_encode($tuesday);
                $avalability->wednesday = json_encode($wednesday);
                $avalability->thursday = json_encode($thursday);
                $avalability->friday = json_encode($friday);
                $avalability->saturday = json_encode($saturday);
                $avalability->save();
            }
            Toastr::success(translate('pandit_added_successfully'));
            return redirect()->route('admin.pandit.list');
        }
        Toastr::success(translate('unable_to_store_data'));
        return redirect()->back();
    }

    public function pandit_verify(Request $request){
        // dd($request->id);
        $pandit = Pandit::where('id',$request->id)->update(['status'=>$request->status]);
        if($pandit){
            Toastr::success(translate('status_updated_successfully'));
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occured'));
        return redirect()->back();
    }

    // experties functions
    public function experties_list(){
        $experties = PanditExperties::paginate(10);
        return view('admin-views.pandit.experties.list',compact('experties'));
    }

    public function experties_add(Request $request){
        $expertiesStore = new PanditExperties;
        $expertiesStore->name = $request->name;
        if($expertiesStore->save()){
            Toastr::success(translate('experties_added_successfully'));
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occured'));
        return redirect()->back();
    }

    public function experties_update(Request $request){
        $update = PanditExperties::where('id',$request->id)->update(['name'=>$request->name]);
        if($update){
            Toastr::success(translate('experties_updated_successfully'));
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occured'));
        return redirect()->back();
    }

    // public function experties_delete($id){
    //     $delete = PanditExperties::where('id',$id)->delete();
    //     if($delete){
    //         Toastr::success(translate('experties_deleted_successfully'));
    //         return redirect()->back();
    //     }
    //     Toastr::success(translate('an_error_occured'));
    //     return redirect()->back();
    // }

    public function experties_status(Request $request){
        $status = PanditExperties::where('id',$request->id)->update(['status'=>$request->status]);
        if($status){
            return response()->json(['status'=>200]);
        }
        return response()->json(['status'=>400]);
    }
}
