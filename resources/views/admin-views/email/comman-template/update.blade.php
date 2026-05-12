@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')
@section('title', translate('email'))
@push('css_or_js')
<style>
    .preview-box {
        border: 1px solid #ccc;
        padding: 10px;
        min-height: 200px;
        background: #f9f9f9;
    }
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <span class="tio-circle nav-indicator-icon"></span>
            {{ translate('Email') }}
        </h2>
        @if (Helpers::modules_permission_check('Email', 'Template List', 'share'))
        <a class="btn btn-outline-danger btn-sm square-btn float-end" href="{{ route('admin.email.sendEmailTesting',['id'=> $getData['id']]) }}" title="{{ translate('send_mail') }}">
            <i class="tio-share"></i>
        </a>
        @endif
    </div>
    <div class="row">
        <form action="{{ route('admin.email.edit-email-template')}}" method="post">
            @csrf
            <div class="col-md-6">
                <label for="">Email Use Name</label>
                <input type="text" name="type" value="{{ $getData['type']}}" class="form-control">
                <input type="hidden" name="id" value="{{ $getData['id']}}" class="form-control">
            </div>
            <div class="col-md-6"> </div>
            <div class="col-md-12">
                <label for="">Email Template</label>
                <textarea name="template" class="form-control ckeditor">
                {{ $getData['html']}}
                </textarea>
                @if (Helpers::modules_permission_check('Email', 'Template List', 'edit'))                
                <input type="submit" class="btn btn-success">
                @endif
            </div>
        </form>
        <div class="col-md-12 mt-3"><label>Live Preview</label>
            <div class="preview-box" id="preview"></div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ensure CKEditor is loaded
        if (typeof CKEDITOR === "undefined") {
            console.error("CKEditor failed to load.");
            return;
        }

        document.querySelectorAll(".ckeditor").forEach((element) => {
            // Ensure the instance does not already exist
            if (CKEDITOR.instances[element.name]) {
                CKEDITOR.instances[element.name].destroy();
            }

            // Initialize CKEditor
            let editor = CKEDITOR.replace(element.name, {
                // extraPlugins: "uploadimage,uploadfile",
                // filebrowserUploadUrl: "upload.php?type=Files",
                // filebrowserUploadMethod: "form",
                // filebrowserImageUploadUrl: "upload.php?type=Images",
                // removePlugins: "easyimage,cloudservices",
                // toolbar: [{
                //         name: "document",
                //         items: ["Source", "-", "NewPage", "Preview"]
                //     },
                //     {
                //         name: "clipboard",
                //         items: ["Cut", "Copy", "Paste", "PasteText", "Undo", "Redo"]
                //     },
                //     {
                //         name: "insert",
                //         items: ["Image", "Table", "HorizontalRule", "SpecialChar", "UploadFile"]
                //     },
                //     {
                //         name: "styles",
                //         items: ["Styles", "Format", "Font", "FontSize"]
                //     },
                //     {
                //         name: "colors",
                //         items: ["TextColor", "BGColor"]
                //     },
                //     {
                //         name: "tools",
                //         items: ["Maximize"]
                //     }
                // ]
            });

            // Live Preview: Find corresponding preview box and update
            editor.on("change", function() {
                let previewBox = document.getElementById("preview");
                if (previewBox) {
                    previewBox.innerHTML = editor.getData();
                }
            });
        });
    });
</script>
@endpush