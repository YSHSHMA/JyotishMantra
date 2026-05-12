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

    @import url('https://fonts.googleapis.com/css?family=Helvetica:700,400');
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <span class="tio-circle nav-indicator-icon"></span>
            {{ translate('Email') }}
        </h2>
    </div>
    <div class="row">
        <form action="{{ route('admin.email.save-email-template')}}" method="post">
            @csrf
            <div class="col-md-6">
                <label for="">Email Use Name</label>
                <input type="text" name="type" class="form-control">
            </div>
            <div class="col-md-6"> </div>
            <div class="col-md-12">
                <label for="">Email Template</label>
                <textarea name="template" class="form-control ckeditor">
                </textarea>
                @if (Helpers::modules_permission_check('Email', 'Create Email', 'add'))
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