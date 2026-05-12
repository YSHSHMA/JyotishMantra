<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;


class Sahitya extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'chapter',
        'verse',
        'description',
        'image',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'slug' => 'string',
        'chapter' => 'integer',
        'verse' => 'integer',
        'description' => 'string',
        'image' => 'string',
        'status' => 'string',
    ];

    protected $appends = ['verse_data'];

    public function getVerseDataAttribute()
    {
        // Ensure the token is defined; otherwise, replace it with your actual token
        $token = 'Mahakal@2024@sahitya'; 

        $url = 'https://sahitya-mahakal.rizrv.net/bhagvad-geeta?chapter=' . $this->attributes['chapter'] . '&verse=' . $this->attributes['verse'];
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return null;
        }

        return json_decode($response, true);

    }

   // public function scopeActive(): mixed
   //  {
   //      return $this->where('status',1);
   //  }

    public function translations(): MorphMany
    {
        return $this->morphMany('App\Models\Translation', 'translationable');
    }

     public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }

        return $this->translations[0]->value??$name;
    }

      public function getDefaultNameAttribute(): string|null
    {
        return $this->translations[0]->value ?? $this->name;
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });
    }
}



//controller

<?php

namespace App\Http\Controllers\Admin\Sahitya;

use App\Contracts\Repositories\SahityaRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Sahitya;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\SahityaAddRequest;
use App\Http\Requests\Admin\SahityaUpdateRequest;
use App\Http\Resources\SahityaResource;
use App\Traits\PaginatorTrait;
use App\Services\SahityaService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SahityaController extends BaseController
{
    use PaginatorTrait;

    public function __construct(
        private readonly SahityaRepositoryInterface       $sahityaRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {    
        return $this->getAddView($request);
    }

     public function getList(Request $request): Application|Factory|View
    {
        $sahityas = $this->sahityaRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Sahitya::LIST[VIEW], compact('sahityas'));
    }


    public function getAddView(Request $request): View
    {
        $sahityas = $this->sahityaRepo->getListWhere(
            searchValue: $request->get('searchValue'),
            dataLimit: getWebConfig(name: 'pagination_limit')
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];


        // Determine the view to use based on conditions (if any)
        $view = Sahitya::LIST[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('sahityas', 'language', 'defaultLanguage',));
    }


    public function getUpdateView(string|int $id): View
    {
        $sahitya = $this->sahityaRepo->getFirstWhere(
            params: ['id' => $id],
            relations: ['translations']
        );

        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];

        // Determine the view to use based on conditions (if any)
        $view = Sahitya::UPDATE[VIEW]; // Example view selection, adjust as needed

        return view($view, compact('sahitya', 'language', 'defaultLanguage'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $data = [
          'status' => $request->get('status', 0),
        ];
        $this->rashiRepo->update(id:$request['id'], data:$data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function add(SahityaAddRequest $request, SahityaService $sahityaService): RedirectResponse
    {
         $dataArray = $sahityaService->getAddData(request:$request);
            $savedAttributes = $this->sahityaRepo->add(data:$dataArray);
            $this->translationRepo->add(request:$request, model:'App\Models\Sahitya', id:$savedAttributes->id);

        Toastr::success(translate('sahitya_added_successfully'));
        return redirect()->route('admin.sahitya.view');
    }



     public function update(SahityaUpdateRequest $request, $id, SahityaService $sahityaService): RedirectResponse
        {
            $app = $this->sahityaRepo->getFirstWhere(params:['id'=>$request['id']]);
            $dataArray = $sahityaService->getUpdateData(request: $request, data:$app);
            $this->sahityaRepo->update(id:$request['id'], data:$dataArray);
            $this->translationRepo->update(request:$request, model:'App\Models\Sahitya', id:$request['id']);

            Toastr::success(translate('sahitya_updated_successfully'));
            return redirect()->route('admin.sahitya.view');
        }



    public function delete(Request $request): JsonResponse
    {
        $sahitya = $this->sahityaRepo->getFirstWhere(['id' => $request->input('id')]);

        if (!$sahitya) {
            return response()->json(['error' => translate(' not found')], 404);
        }

        $this->sahityaRepo->delete(['id' => $request->input('id')]);
        $this->translationRepo->delete(model: 'App\Models\Sahitya', id: $request->input('id'));

        return response()->json(['message' => translate('deleted successfully')]);
    }

}



//view

@extends('layouts.back-end.app')

@section('title', translate('sahitya'))

@section('content')
<div class="content container-fluid">
   <div class="mb-3">
      <h2 class="h1 mb-0 d-flex gap-2">
         <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" alt="">
         {{ translate('sahitya_Setup') }}
      </h2>
   </div>
   <div class="row">
      <!-- Form for adding new sahitya -->
      <div class="col-md-12 mb-3">
         <div class="card">
            <div class="card-body">
               <form action="{{ route('admin.sahitya.store') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  <!-- Language tabs -->
                  <ul class="nav nav-tabs w-fit-content mb-4">
                     @foreach($language as $lang)
                     <li class="nav-item text-capitalize">
                        <a class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                           id="{{$lang}}-link">
                           {{ getLanguageName($lang).'('.strtoupper($lang).')' }}
                        </a>
                     </li>
                     @endforeach
                  </ul>
                  <div class="row">
                     <div class="col-md-8">
                        <!-- Input fields for sahitya name -->
                        @foreach($language as $lang)
                        <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form"
                           id="{{$lang}}-form">
                           <div class="form-group">
                           <label class="title-color" for="name">{{ translate('Name') }}<span
                              class="text-danger">*</span>
                           ({{ strtoupper($lang) }})</label>
                           <input type="text" name="name[]" class="form-control" id="name"
                              placeholder="{{ translate('enter_Name') }}" {{$lang == $defaultLanguage? 'required':''}}>
                           </div>

                           <div class="form-group">
                               <label for="name" class="title-color">
                                   {{ translate('description') }}
                                   <span class="text-danger">*</span>
                                   ({{ strtoupper($lang) }})
                               </label>
                               <textarea name="description[]" class="form-control ckeditor" id="description" value=""
                                   placeholder="{{ translate('ex') }} : {{ translate('description') }}" {{ $lang == $defaultLanguage ? '': '' }}></textarea>
                           </div>
                       <input type="hidden" name="lang[]" value="{{ $lang }}">
                        </div>
                        @endforeach
                     </div>
                     <div class="col-md-4 mb-4">
                        <div class="form-group">
                           <label for="chapter-select"  class="title-color text-capitalize">{{ translate('select_chapter') }}</label>
                            <select id="chapter-select" name="chapter" class="js-example-responsive form-control w-100 action-display-data" onchange="updateVersesDropdown()">
                                <option value="chapter">-- Select Chapter --</option>
                                <!-- Options will be populated using JavaScript -->
                            </select>
                        </div>
                          <!-- Input field for number of verses -->
                         <div id="verse-field" class="form-group" style="display: none;">
                             <label for="verse-select" class="title-color text-capitalize">{{ translate('select_verse') }}</label>
                             <select id="verse-select" name="verse" class="js-example-responsive form-control w-100 action-display-data">
                                 <option value="verse">-- Select Verse --</option>
                                 <!-- Options will be populated dynamically -->
                             </select>
                         </div>
                        <div class="text-center">
                           <img class="upload-img-view" id="detail-viewer"
                              src="{{ dynamicAsset(path: 'public\assets\back-end\img\400x400\img2.jpg') }}"
                              alt="">
                        </div>
                        <div class="form-group">
                           <label for="detail_image" class="title-color">
                              {{ translate('thumbnail') }}<span class="text-danger">*</span>
                           </label>
                           <span class="ml-1 text-info">
                              {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                           </span>
                           <div class="custom-file text-left">
                              <input type="file" name="image" id="image"
                                 class="custom-file-input image-preview-before-upload" data-preview="#detail-viewer"
                                 required accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                              <label class="custom-file-label" for="detail-image">
                                 {{ translate('choose_file') }}
                              </label>
                           </div>
                        </div>
                     </div>                  
                  </div>
                  <!-- Buttons for form actions -->
                  <div class="d-flex flex-wrap gap-2 justify-content-end">
                     <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary">{{ translate('submit') }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Section for displaying sahityalist -->
      <div class="col-md-12">
         <div class="card">
            <div class="px-3 py-4">
               <!-- Search bar -->
               <div class="row align-items-center">
                  <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                     <h5 class="mb-0 d-flex align-items-center gap-2">{{ translate('sahitya_list') }}
                        <span class="badge badge-soft-dark radius-50 fz-12">{{ $sahityas->total() }}</span>
                     </h5>
                  </div>
                  <div class="col-sm-8 col-md-6 col-lg-4">
                     <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group input-group-custom input-group-merge">
                           <div class="input-group-prepend">
                              <div class="input-group-text">
                                 <i class="tio-search"></i>
                              </div>
                           </div>
                           <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                             placeholder="{{ translate('search_by_name') }}"
                              aria-label="{{ translate('search_by_name') }}" required>
                           <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <!-- Table displaying sahitya -->
            <div class="text-start">
               <div class="table-responsive">
                  <table id="datatable"
                     class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                     <thead class="thead-light thead-50 text-capitalize">
                        <tr>
                           <th>{{ translate('SL') }}</th>
                           <th>{{ translate('Name') }} </th>
                           <th>{{ translate('Image') }} </th>
                           <th class="text-center">{{ translate('status') }}</th>
                           <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        <!-- Loop through sahitya -->
                        @foreach($sahityas as $key => $sahitya)
                        <tr>
                           <td>{{$sahityas->firstItem()+$key}}</td>
                           <td class="overflow-hidden max-width-100px">
                               <span data-toggle="tooltip" data-placement="right" title="{{$sahitya['defaultname']}}">
                                   {{ Str::limit($sahitya['defaultname'],20) }}
                               </span>
                           </td>
                            <td>
                                <div class="avatar-60 d-flex align-items-center rounded">
                                    <img class="img-fluid" alt=""
                                         src="{{ getValidImage(path: 'storage/app/public/sahitya/'.$sahitya['image'], type: 'backend-sahitya') }}">
                                </div>
                            </td>
                           <td>
                              <!-- Form for toggling status -->
                              <form action="{{route('admin.sahitya.status-update') }}" method="post"
                                 id="sahitya-status{{$sahitya['id']}}-form">
                                 @csrf
                                 <input type="hidden" name="id" value="{{$sahitya['id']}}">
                                 <label class="switcher mx-auto">
                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                       id="sahitya-status{{ $sahitya['id'] }}" value="1" {{ $sahitya['status'] == 1 ? 'checked' : '' }}
                                       data-modal-id="toggle-status-modal"
                                       data-toggle-id="sahitya-status{{ $sahitya['id'] }}"
                                       data-on-image="sahitya-status-on.png"
                                       data-off-image="sahitya-status-off.png"
                                       data-on-title="{{ translate('Want_to_Turn_ON').' '.$sahitya['defaultname'].' '. translate('status') }}"
                                       data-off-title="{{ translate('Want_to_Turn_OFF').' '.$sahitya['defaultname'].' '.translate('status') }}"
                                       data-on-message="<p>{{ translate('if_enabled_this_will_be_available_on_the_website_and_customer_app') }}</p>"
                                       data-off-message="<p>{{ translate('if_disabled_this_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                    <span class="switcher_control"></span>
                                 </label>
                              </form>
                           </td>
                           <!-- Actions for editing and deleting sahitya -->
                           <td>
                              <div class="d-flex justify-content-center gap-2">
                                 <a class="btn btn-outline-info btn-sm square-btn"
                                    title="{{ translate('edit') }}"
                                    href="{{route('admin.sahitya.update',[$sahitya['id']])}}">
                                    <i class="tio-edit"></i>
                                 </a>
                                 <a class="sahitya-delete-button btn btn-outline-danger btn-sm square-btn"
                                    id="{{ $sahitya['id'] }}">
                                    <i class="tio-delete"></i>
                                 </a>
                              </div>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Pagination for sahityalist -->
            <div class="table-responsive mt-4">
               <div class="d-flex justify-content-lg-end">
                  {!! $sahityas->links() !!}
               </div>
            </div>
            <!-- Message for no data to show -->
            @if(count($sahityas) == 0)
            <div class="text-center p-4">
               <img class="mb-3 w-160"
                  src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}"
                  alt="{{ translate('image') }}">
               <p class="mb-0">{{ translate('no_data_to_show') }}</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>

<!-- Hidden HTML element for delete route -->
<span id="route-admin-sahitya-delete"
   data-url="{{ route('admin.sahitya.delete') }}"></span>
<!-- Toast message for sahityadeleted -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
   <div id="sahitya-deleted-message" class="toast hide" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="toast-body">
         {{ translate('Section deleted') }}
      </div>
   </div>
</div>
@foreach($sahityas as $sahitya)

<div class="modal fade" id="imageModal{{$sahitya['id']}}" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel{{$sahitya['id']}}" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel{{$sahitya['id']}}">{{ translate('Image Preview') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body text-center">
           <div class="row">
                  @php
                      $images = json_decode($sahitya['image'], true);
                  @endphp
                  @if($images)
                     @foreach($images as $image)
                        <div class="col-md-4 mb-3">
                           <img class="img-fluid rounded" alt="Sahitya Image"
                              src="{{ getValidImage(path: 'storage/app/public/sahitya/' . $image, type: 'backend-sahitya') }}">
                        </div>
                     @endforeach
                  @else
                     <p>{{ translate('No Images Available') }}</p>
                  @endif
               </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
         </div>
      </div>
   </div>
</div>

@endforeach

@endsection


@push('script')
<!-- Include SweetAlert2 for confirmation dialogs -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

 {{-- datepicker --}}
 <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
 <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
 <script type="text/javascript">
     $(document).ready(function() {
         $('.ckeditor').ckeditor();
     });
 </script>


<script>
   "use strict";
   // Retrieve localized texts
   let getYesWord = $('#message-yes-word').data('text');
   let getCancelWord = $('#message-cancel-word').data('text');
   let messageAreYouSureDeleteThis = $('#message-are-you-sure-delete-this').data('text');
   let messageYouWillNotAbleRevertThis = $('#message-you-will-not-be-able-to-revert-this').data('text');
   
   // Handle delete button click
   $('.sahitya-delete-button').on('click', function () {
      let sahityaId = $(this).attr("id");
      Swal.fire({
         title: messageAreYouSureDeleteThis,
         text: messageYouWillNotAbleRevertThis,
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: getYesWord,
         cancelButtonText: getCancelWord,
         icon: 'warning',
         reverseButtons: true
      }).then((result) => {
         if (result.value) {
            // Send AJAX request to delete sahitya
            $.ajax({
               url: $('#route-admin-sahitya-delete').data('url'),
               method: 'POST',
               data: {
                  _token: '{{ csrf_token() }}',
                  id: sahityaId
               },
               success: function (response) {
                  // Show success message
                  toastr.success('sahitya deleted successfully', '', { positionClass: 'toast-bottom-left' });
                  // Reload the page
                  location.reload();
               },
               error: function (xhr, status, error) {
                  // Show error message
                  toastr.error(xhr.responseJSON.message);
               }
            });
         }
      });
   });
</script>
 <script>
     // Object with chapters and their respective number of verses
     const chapterVerses = {
         1: 47, 
         2: 72, 
         3: 43,  
         4: 42,   
         5: 29,
         6: 47,
         7: 30,
         8: 28,
         9: 34,
         10: 42,
         11: 55,
         12: 20,
         13: 35,
         14: 27,
         15: 20,
         16: 24,
         17: 28,
         18: 78
     };

     // Populate the chapter select dropdown
     const chapterSelect = document.getElementById('chapter-select');
     for (let i = 1; i <= 18; i++) {
         let option = document.createElement('option');
         option.value = i;
         option.text = `Chapter ${i}`;
         chapterSelect.appendChild(option);
     }

     // Function to update the verses dropdown based on the selected chapter
     function updateVersesDropdown() {
         const selectedChapter = chapterSelect.value;
         const verseSelect = document.getElementById('verse-select');
         const verseField = document.getElementById('verse-field');

         // Clear existing options in the verses dropdown
         verseSelect.innerHTML = '<option value="">-- Select Verse --</option>';

         if (selectedChapter) {
             // Get the number of verses for the selected chapter
             const numberOfVerses = chapterVerses[selectedChapter];

             // Populate the verses dropdown
             for (let i = 1; i <= numberOfVerses; i++) {
                 let option = document.createElement('option');
                 option.value = i;
                 option.text = `Verse ${i}`;
                 verseSelect.appendChild(option);
             }

             // Show the verse dropdown
             verseField.style.display = 'block';
         } else {
             // Hide the verse dropdown if no chapter is selected
             verseField.style.display = 'none';
         }
     }
 </script>

@endpush



//edit

@extends('layouts.back-end.app')

@section('title', translate('sahitya'))

@section('content')
<div class="content container-fluid">

    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/app.png') }}" class="mb-1 mr-1"
                alt="">
            {{ translate('update_sahitya') }}
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.sahitya.update', [$sahitya['id']]) }}"
                        method="post" enctype="multipart/form-data">
                        @csrf

                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach($language as $lang)
                            <li class="nav-item text-capitalize">
                                <span class="nav-link form-system-language-tab cursor-pointer {{$lang == $defaultLanguage? 'active':''}}"
                                    id="{{$lang}}-link">
                                    {{ getLanguageName($lang).' ('.strtoupper($lang).')' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>

                          <div class="row mb-3">
                            <div class="col-md-8">
                                 @foreach ($language as $lang)
                                        <?php
                                        if (count($sahitya['translations'])) {
                                            $translate = [];
                                            foreach ($sahitya['translations'] as $translations) {
                                                if ($translations->locale == $lang && $translations->key == 'name') {
                                                    $translate[$lang]['name'] = $translations->value;
                                                }
                                                if ($translations->locale == $lang && $translations->key == 'description') {
                                                    $translate[$lang]['description'] = $translations->value;
                                                }
                                                
                                            }
                                        }
                                        ?>
                                <div class="form-group {{$lang != $defaultLanguage ? 'd-none':''}} form-system-language-form" id="{{$lang}}-form">
                                    <div class="form-group">
                                        <label class="title-color" for="name">{{ translate('Name') }} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="name[]"
                                        value="{{ $lang == $defaultLanguage ? $sahitya['name'] : $translate[$lang]['name'] ?? '' }}"
                                        class="form-control" id="name"
                                        placeholder="{{ translate('ex') }} : {{ translate('Name') }}"
                                        {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                   
                                    </div>
                                    <div class="form-group">
                                        <label class="title-color" for="description">{{ translate('description') }}
                                            ({{ strtoupper($lang) }})</label>
                                        <textarea name="description[]" class="form-control ckeditor" id="description"
                                            {{ $lang == $defaultLanguage ? 'required' : '' }}>{!! $translate[$lang]['description']??$sahitya['description'] !!}</textarea>
                                    </div>
                                     <input type="hidden" name="lang[]" value="{{$lang}}">
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                   <label for="chapter-select"  class="title-color text-capitalize">{{ translate('select_chapter') }}</label>
                                    <select id="chapter-select" name="chapter" class="js-example-responsive form-control w-100 action-display-data" onchange="updateVersesDropdown()" chapter="{{ $sahitya['chapter'] }}">
                                        <!-- Options will be populated using JavaScript -->
                                    </select>
                                </div>
                                 <!-- Verse Select Field -->
                                <div class="form-group">
                                    <label for="verse-select" class="title-color text-capitalize">{{ translate('select_verse') }}</label>
                                    <select id="verse-select" name="verse" class="js-example-responsive form-control w-100 action-display-data">
                                        <option value="">-- Select Verse --</option>
                                        <!-- Options populated dynamically -->
                                    </select>
                                </div>
                                <div class="text-center">
                                    <img class="upload-img-view" id="viewer"
                                        src="{{ getValidImage(path: 'storage/app/public/sahitya/'.$sahitya->image, type: 'backend-sahitya') }}"
                                        alt="">
                                </div>

                                <div class="form-group">
                                    <label for="image" class="title-color">
                                        {{ translate('Thumbnail') }}
                                    </label>
                                    <span class="ml-1 text-info">
                                        {{ THEME_RATIO[theme_root_path()]['Brand Image'] }}
                                    </span>
                                    <div class="custom-file text-left">
                                        <input type="file" name="image" id="image"
                                            class="custom-file-input image-preview-before-upload"
                                            data-preview="#viewer"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="image">
                                            {{ translate('choose_file') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                            <button type="submit" class="btn btn--primary">{{ translate('update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        // Object with chapters and their respective number of verses
        const chapterVerses = {
            1: 47, 
            2: 72, 
            3: 43,  
            4: 42,   
            5: 29,
            6: 47,
            7: 30,
            8: 28,
            9: 34,
            10: 42,
            11: 55,
            12: 20,
            13: 35,
            14: 27,
            15: 20,
            16: 24,
            17: 28,
            18: 78
        };

        // Pre-selected values
        const preselectedChapter = "{{ $sahitya['chapter'] }}";
        const preselectedVerse = "{{ $sahitya['verse'] }}";

        // Populate the chapter select dropdown
        const chapterSelect = document.getElementById('chapter-select');
        for (let i = 1; i <= 18; i++) {
            let option = document.createElement('option');
            option.value = i;
            option.text = `Chapter ${i}`;
            option.selected = i == preselectedChapter; // Preselect the chapter
            chapterSelect.appendChild(option);
        }

        // Function to update the verses dropdown based on the selected chapter
        function updateVersesDropdown() {
            const selectedChapter = chapterSelect.value;
            const verseSelect = document.getElementById('verse-select');

            // Clear existing options in the verses dropdown
            verseSelect.innerHTML = '<option value="">-- Select Verse --</option>';

            if (selectedChapter) {
                // Get the number of verses for the selected chapter
                const numberOfVerses = chapterVerses[selectedChapter];

                // Populate the verses dropdown
                for (let i = 1; i <= numberOfVerses; i++) {
                    let option = document.createElement('option');
                    option.value = i;
                    option.text = `Verse ${i}`;
                    option.selected = i == preselectedVerse; // Preselect the verse
                    verseSelect.appendChild(option);
                }
            }
        }

        // Call the function to pre-populate verses based on the stored chapter
        updateVersesDropdown();
    </script>
@endpush

