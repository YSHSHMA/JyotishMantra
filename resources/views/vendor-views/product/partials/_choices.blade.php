@foreach ($choice_options as $key => $choice)
@if(!empty($choice['title'] ?? ''))
 @if(!empty($choice['options']) && is_array($choice['options']) && collect($choice['options'])->filter()->isNotEmpty())
    <div class="col-md-12 col-lg-6 {{ str_replace([' ', "\n", "\r", "\t"], '', ($choice['title'] ?? '') . ($choice_no[$key] ?? '')) }}">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <input type="hidden" name="choice_no[]" value="{{ $choice_no[$key] ?? '' }}">
                <input type="hidden" class="form-control" name="choice[]" value="{{ $choice['title'] }}"
                    placeholder="{{ translate('choice_Title') }}" readonly>
            </div>
            <div class="col-lg-12 col-md-12"> 
                <label class="title-color">{{ $choice['title'] }}</label>
                <input type="text" class="form-control call-update-sku option_value_new{{ $choice_no[$key] ?? '' }}"
                    name="choice_options_{{ $choice_no[$key] ?? '' }}[]" data-role="tagsinput"
                    value="@foreach ($choice['options'] as $c) {{ $c . ',' }} @endforeach">
            </div>
        </div>
    </div>
    @endif
    @endif
@endforeach
