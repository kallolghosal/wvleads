@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Import CSV</h2>
            <form action="{{route('store-file')}}" method="post" enctype="multipart/form-data">
                @csrf
                <label for="csvfile">Select CSV file to upload</label>
                <div class="input-group">
                    <input type="file" class="form-control" id="file" aria-describedby="inputGroupFileAddon04" name="file" aria-label="Upload">
                    @error('file')
                    <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
                    @enderror
                    <input class="btn btn-outline-secondary" value="Upload" type="submit" id="inputGroupFileAddon04">
                </div>
            </form>
            @if (session('status'))
                <h4>{{ session('status') }}</h4>
            @endif
        </div>
        <div class="col-md-6">
        
        </div>
    </div>
    
</div>
@endsection