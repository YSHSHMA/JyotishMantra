{{-- @php
    dd($offlinePoojaRefundPolicy);
@endphp --}}
@extends('layouts.back-end.app')

@section('title', translate('pandit/pooja_Refund_Policy_Update'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .gj-timepicker-bootstrap [role=right-icon] button .gj-icon {
            top: 14px;
            right: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png') }}" alt="">
                {{ translate('pandit/pooja_Refund_Policy_Update') }}
            </h2>
        </div>

        <div class="col-12">
            <form class="product-form text-start"
                action="{{ route('admin.service.offline.pooja.refund.policy.update', $offlinePoojaRefundPolicy['id']) }}"
                method="post" enctype="multipart/form-data" id="service_form">
                @csrf
                <div class="card">
                    <div class="px-4 pt-3">
                        <ul class="nav nav-tabs w-fit-content mb-4">
                            @foreach ($languages as $language)
                                <li class="nav-item text-capitalize">
                                    <a class="nav-link form-system-language-tab  {{ $language == $defaultLanguage ? 'active' : '' }}"
                                        href="#"
                                        id="{{ $language }}-link">{{ getLanguageName($language) . '(' . strtoupper($language) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title-color">{{ translate('days') }}</label>
                                    <select name="days" class="form-control">
                                        <option value="1"
                                            {{ $offlinePoojaRefundPolicy['days'] == '1' ? 'selected' : '' }}>1</option>
                                        <option value="2"
                                            {{ $offlinePoojaRefundPolicy['days'] == '2' ? 'selected' : '' }}>2</option>
                                        <option value="3"
                                            {{ $offlinePoojaRefundPolicy['days'] == '3' ? 'selected' : '' }}>3</option>
                                        <option value="4"
                                            {{ $offlinePoojaRefundPolicy['days'] == '4' ? 'selected' : '' }}>4</option>
                                        <option value="5"
                                            {{ $offlinePoojaRefundPolicy['days'] == '5' ? 'selected' : '' }}>5</option>
                                        <option value="6"
                                            {{ $offlinePoojaRefundPolicy['days'] == '6' ? 'selected' : '' }}>6</option>
                                        <option value="7"
                                            {{ $offlinePoojaRefundPolicy['days'] == '7' ? 'selected' : '' }}>7</option>
                                        <option value="8"
                                            {{ $offlinePoojaRefundPolicy['days'] == '8' ? 'selected' : '' }}>8</option>
                                        <option value="9"
                                            {{ $offlinePoojaRefundPolicy['days'] == '9' ? 'selected' : '' }}>9</option>
                                        <option value="10"
                                            {{ $offlinePoojaRefundPolicy['days'] == '10' ? 'selected' : '' }}>10</option>
                                        <option value="11"
                                            {{ $offlinePoojaRefundPolicy['days'] == '11' ? 'selected' : '' }}>11</option>
                                        <option value="12"
                                            {{ $offlinePoojaRefundPolicy['days'] == '12' ? 'selected' : '' }}>12</option>
                                        <option value="13"
                                            {{ $offlinePoojaRefundPolicy['days'] == '13' ? 'selected' : '' }}>13</option>
                                        <option value="14"
                                            {{ $offlinePoojaRefundPolicy['days'] == '14' ? 'selected' : '' }}>14</option>
                                        <option value="15"
                                            {{ $offlinePoojaRefundPolicy['days'] == '15' ? 'selected' : '' }}>15</option>
                                        <option value="16"
                                            {{ $offlinePoojaRefundPolicy['days'] == '16' ? 'selected' : '' }}>16</option>
                                        <option value="17"
                                            {{ $offlinePoojaRefundPolicy['days'] == '17' ? 'selected' : '' }}>17</option>
                                        <option value="18"
                                            {{ $offlinePoojaRefundPolicy['days'] == '18' ? 'selected' : '' }}>18</option>
                                        <option value="19"
                                            {{ $offlinePoojaRefundPolicy['days'] == '19' ? 'selected' : '' }}>19</option>
                                        <option value="20"
                                            {{ $offlinePoojaRefundPolicy['days'] == '20' ? 'selected' : '' }}>20</option>
                                        <option value="21"
                                            {{ $offlinePoojaRefundPolicy['days'] == '21' ? 'selected' : '' }}>21</option>
                                        <option value="22"
                                            {{ $offlinePoojaRefundPolicy['days'] == '22' ? 'selected' : '' }}>22</option>
                                        <option value="23"
                                            {{ $offlinePoojaRefundPolicy['days'] == '23' ? 'selected' : '' }}>23</option>
                                        <option value="24"
                                            {{ $offlinePoojaRefundPolicy['days'] == '24' ? 'selected' : '' }}>24</option>
                                        <option value="25"
                                            {{ $offlinePoojaRefundPolicy['days'] == '25' ? 'selected' : '' }}>25</option>
                                        <option value="26"
                                            {{ $offlinePoojaRefundPolicy['days'] == '26' ? 'selected' : '' }}>26</option>
                                        <option value="27"
                                            {{ $offlinePoojaRefundPolicy['days'] == '27' ? 'selected' : '' }}>27</option>
                                        <option value="28"
                                            {{ $offlinePoojaRefundPolicy['days'] == '28' ? 'selected' : '' }}>28</option>
                                        <option value="29"
                                            {{ $offlinePoojaRefundPolicy['days'] == '29' ? 'selected' : '' }}>29</option>
                                        <option value="30"
                                            {{ $offlinePoojaRefundPolicy['days'] == '30' ? 'selected' : '' }}>30</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title-color">{{ translate('percent') }}</label>
                                    <select name="percent" class="form-control">
                                        <option value="0"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '0' ? 'selected' : '' }}>5</option>
                                        <option value="5"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '5' ? 'selected' : '' }}>5</option>
                                        <option value="10"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '10' ? 'selected' : '' }}>10
                                        </option>
                                        <option value="15"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '15' ? 'selected' : '' }}>15
                                        </option>
                                        <option value="20"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '20' ? 'selected' : '' }}>20
                                        </option>
                                        <option value="25"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '25' ? 'selected' : '' }}>25
                                        </option>
                                        <option value="30"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '30' ? 'selected' : '' }}>30
                                        </option>
                                        <option value="35"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '35' ? 'selected' : '' }}>35
                                        </option>
                                        <option value="40"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '40' ? 'selected' : '' }}>40
                                        </option>
                                        <option value="45"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '45' ? 'selected' : '' }}>45
                                        </option>
                                        <option value="50"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '50' ? 'selected' : '' }}>50
                                        </option>
                                        <option value="55"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '55' ? 'selected' : '' }}>55
                                        </option>
                                        <option value="60"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '60' ? 'selected' : '' }}>60
                                        </option>
                                        <option value="65"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '65' ? 'selected' : '' }}>65
                                        </option>
                                        <option value="70"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '70' ? 'selected' : '' }}>70
                                        </option>
                                        <option value="75"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '75' ? 'selected' : '' }}>75
                                        </option>
                                        <option value="80"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '80' ? 'selected' : '' }}>80
                                        </option>
                                        <option value="85"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '85' ? 'selected' : '' }}>85
                                        </option>
                                        <option value="90"
                                            {{ $offlinePoojaRefundPolicy['percent'] == '90' ? 'selected' : '' }}>90
                                        </option>
                                    </select>
                                </div>
                            </div>

                            @foreach ($languages as $language)
                                <?php
                                if (count($offlinePoojaRefundPolicy['translations'])) {
                                    $translate = [];
                                    foreach ($offlinePoojaRefundPolicy['translations'] as $translation) {
                                        if ($translation->locale == $language && $translation->key == 'message') {
                                            $translate[$language]['message'] = $translation->value;
                                        }
                                    }
                                }
                                ?>
                                <div class="{{ $language != 'en' ? 'd-none' : '' }} form-system-language-form col-md-12"
                                    id="{{ $language }}-form">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="{{ $language }}_message">{{ translate('message') }}
                                            ({{ strtoupper($language) }})
                                        </label>
                                        <textarea {{ $language == 'en' ? 'required' : 'required' }} class="form-control" rows="10" name="message[]">{!! $translate[$language]['message'] ?? $offlinePoojaRefundPolicy['message'] !!}</textarea>
                                    </div>

                                    <input type="hidden" name="lang[]" value="{{ $language }}">
                                </div>
                            @endforeach
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <span id="image-path-of-product-upload-icon"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}"></span>
    <span id="image-path-of-product-upload-icon-two"
        data-path="{{ dynamicAsset(path: 'public/assets/back-end/img/400x400/img2.jpg') }}"></span>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    <span id="message-upload-image" data-text="{{ translate('upload_Image') }}"></span>
    <span id="message-are-you-sure" data-text="{{ translate('are_you_sure') }}"></span>
    <span id="message-want-to-add-or-update-this-service"
        data-text="{{ translate('want_to_update_this_service') }}"></span>
    <span id="message-please-only-input-png-or-jpg"
        data-text="{{ translate('please_only_input_png_or_jpg_type_file') }}"></span>
    <span id="message-service-added-successfully" data-text="{{ translate('service_added_successfully') }}"></span>
    <span id="system-session-direction" data-value="{{ Session::get('direction') }}"></span>
    <span id="message-file-size-too-big" data-text="{{ translate('file_size_too_big') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/tags-input.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/admin/product-add-update.js') }}"></script>

    <script type="text/javascript">
        $('.service-add-requirements-check').on('click', function() {
            getServiceAddRequirementsCheck()
        });
    </script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    {{-- ck editor --}}
    <script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/js/sample.js') }}"></script>
@endpush
