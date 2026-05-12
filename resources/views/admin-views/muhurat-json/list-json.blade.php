@extends('layouts.back-end.app')

@section('content')
    <div class="container">
        <h1>JSON Entries</h1>

        @if ($data)
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Detail</th>
                        {{-- <th>Image</th> --}}
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $entry)
                        <tr>
                            <td>{{ ucfirst($entry['type']) }}</td>
                            <td>{{ $entry['details'] }}</td>
                            {{-- <td>
                            @if ($entry['image'])
                                <img src="{{url('public/' . $entry['image']) }}" alt="{{ $entry['type'] }}" style="width: 80px;">
                            @else
                                No Image
                            @endif
                        </td> --}}
                            <td>
                                <a href="{{ route('admin.edit-json', ['type' => $entry['type'], 'index' => $loop->index]) }}"
                                    class="btn btn-primary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No entries found.</p>
        @endif
    </div>
@endsection
