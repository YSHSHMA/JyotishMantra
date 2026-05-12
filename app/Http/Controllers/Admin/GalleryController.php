<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\GalleryRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\GalleryPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Services\GalleryService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    
    public function __construct(
        private readonly GalleryRepositoryInterface   $galleryRepo, 
        private readonly TranslationRepositoryInterface     $translationRepo,   
        )  { }

    public function gallery_list(Request $request,$id):View {
        $gallery = $this->galleryRepo->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['temple_id' => $id, 'status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
       
        return view(GalleryPath::LIST[VIEW], [
            'gallery' => $gallery,
            'languages' => $languages,
            'defaultLanguage' => $defaultLanguage,
            'id' => $id
        ]);
    }

    public function gallery_add(GalleryRequest $request,GalleryService $galleryService,$id){
        $dataArray = $galleryService->getAddData($request);
        $savedAttributes = $this->galleryRepo->add(data: $dataArray);
        $this->translationRepo->add(request: $request, model: 'App\Models\Gallery', id: $savedAttributes->id);
        Toastr::success(translate('gallery_added_successfully'));
        Helpers::editDeleteLogs('Temple','Gallery','Insert');
        return back();
    }
    public function add_gallery(){

    }

    public function remove_image(GalleryService $galleryService,$id,$key):JsonResponse{

        $findData = $this->galleryRepo->getFirstWhere(params:['id'=>$id]);
        $name = json_decode($findData['images'])[$key]??'';
        if($name){
        $images = $galleryService->image_remove($findData,$name);
        $savedAttributes = $this->galleryRepo->update($id,data: $images);
        // Toastr::success(translate('Remove_Image_successfully'));
        }
        return response()->json(['success' => 1, 'message' => translate('Remove_Image_successfully')], 200);
    //    return redirect()->route('admin.temple.gallery.list',[$findData['temple_id']]);
    }

    public function delete_gallery(GalleryService $galleryService,$id):RedirectResponse{
        $findData = $this->galleryRepo->getFirstWhere(params:['id'=>$id]);
        $galleryService->deleteImages($findData);
        $savedAttributes = $this->galleryRepo->delete(params:['id'=>$id]);
        $this->translationRepo->delete(model:'App\Models\Gallery', id:$id);
        Toastr::success(translate('Remove_Image_successfully'));
        return redirect()->route('admin.temple.gallery.list',[$findData['temple_id']]);
    }

    public function update_gallery(Request $request,GalleryService $galleryService,$id){
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $gallery = $this->galleryRepo->getFirstWhere(params:['id'=>$id],relations:['translations'])??[]; 

        $id = $id;
        $temple_id = $id;
        return view(GalleryPath::UPDATE[VIEW],compact('gallery','defaultLanguage','languages','id','temple_id'));
    }

    public function edit_gallery(GalleryRequest $request,GalleryService $galleryService,$id):RedirectResponse{
        $data1 = $this->galleryRepo->getFirstWhere(params:['id'=>$id]);
        $data =    $galleryService->updateData($request,$data1['images']);
        $this->galleryRepo->update($id,data: $data);
        $this->translationRepo->update(request: $request, model: 'App\Models\Gallery', id: $id);
        return redirect()->route('admin.temple.gallery.list',[$data1['temple_id']]);
    }


    //

    public function add_new_gallery(Request $request ,$id){
        $gallery = $this->galleryRepo->getFirstWhere(params:['temple_id'=>$id],relations:['translations'])??[]; 
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
       
        return view(GalleryPath::NEWADD[VIEW], [
            'gallery' => $gallery??[],
            'languages' => $languages,
            'defaultLanguage' => $defaultLanguage,            
            'temple_id'=>$id,
        ]);
    }

    public function update_new_gallery(GalleryRequest $request,GalleryService $galleryService,$id){
        $data1 = $this->galleryRepo->getFirstWhere(params:['temple_id'=>$id]);
        if($data1){
            $data =    $galleryService->updateData($request,$data1['images']);
            $this->galleryRepo->update($data1['id'],data: $data);
            $this->translationRepo->update(request: $request, model: 'App\Models\Gallery', id: $data1['id']);
            // Toastr::success(translate('gallery_Update_successfully'));
            Helpers::editDeleteLogs('Temple','Gallery','Update');
            return response()->json(['success' => 1, 'message' => translate('Update_Image_successfully')], 200);
        }else{
            $dataArray = $galleryService->getAddData($request);
            $savedAttributes = $this->galleryRepo->add(data: $dataArray);
            $this->translationRepo->add(request: $request, model: 'App\Models\Gallery', id: $savedAttributes->id);
            // Toastr::success(translate('gallery_added_successfully'));
            return response()->json(['success' => 1, 'message' => translate('Add_Image_successfully')], 200);
        }
    }
}
