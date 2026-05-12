@extends('layouts.back-end.app')

@section('content')
<div class="container mt-3">
    <h1>Add New Muhurat JSON Entry</h1>

    <form action="{{ route('admin.store-json') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="form-group col-md-4">
                <label for="year">Year</label>
                <input type="text" class="form-control" id="year" name="year" required>
            </div>
            <div class="form-group col-md-4">
                <label for="type">Type</label>
                <input type="text" class="form-control" id="type" name="type" required>
            </div>
            <div class="form-group col-md-4">
                <label for="titleLink">Title Link</label>
                <input type="text" class="form-control" id="titleLink" name="titleLink" required>
            </div>
            <div class="form-group col-md-4">
                <label for="image">Image</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="form-group col-md-8">
                <label for="message">Message</label>
                <input type="text" class="form-control" id="message" name="message" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="details">Details</label>
            <textarea class="form-control" id="details" name="details"></textarea>
        </div>

        <div class="form-group">
            <label for="details">About Festival</label>
            <textarea class="form-control" id="about_festival" name="about_festival"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Add Entry</button>
    </form>
</div>
@endsection
