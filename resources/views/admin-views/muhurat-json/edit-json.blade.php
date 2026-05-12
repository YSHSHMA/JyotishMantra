@extends('layouts.back-end.app')

@section('content')
<div class="container">
    <h1>Edit JSON Data for {{ ucfirst($type) }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.update-json', ['index' => $index]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" id="type" class="form-control" value="{{ $entry['type'] ?? '' }}" required>
        <div class="form-group">
            <label for="muhurat">Detail</label>
            <input type="text" name="details" id="details" class="form-control" value="{{ $entry['details'] ?? '' }}" required>
        </div>
        <div class="form-group">
            <label for="muhurat">Title</label>
            <input type="text" name="titleLink" id="titleLink" class="form-control" value="{{ $entry['titleLink'] ?? '' }}" required>
        </div>
        <div class="form-group">
            <label for="muhurat">About Festival</label>
            <input type="text" name="about_festival" id="about_festival" class="form-control" value="{{ $entry['about_festival'] ?? '' }}" required>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            @if(!empty($entry['image']))
                <img src="{{ url('public/' . $entry['image']) }}" alt="{{ $entry['type'] }}" style="width: 100px; display: block;">
            @endif
            <input type="file" class="form-control" id="image" name="image">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
