@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Export To CSV</h2>
    <div class="row">
        <div class="col-6">
            <label for="">Select range to export</label>
            <div class="input-group ms-auto">
                <input type="text" class="form-control" placeholder="From" id="strt" aria-label="From">
                <span class="input-group-text"><-></span>
                <input type="text" class="form-control" placeholder="To" id="endt" aria-label="To">
                <button type="button" class="btn btn-success" id="exprt">Export as CSV</button>
            </div>
            @if(session('error'))
                <p class="text-danger">{{ session('error') }}</p>
            @endif
        </div>
        <div class="col-6"></div>
    </div>
</div>
@endsection