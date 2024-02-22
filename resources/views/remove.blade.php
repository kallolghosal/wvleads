@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Remove Leads</h2>
    <div class="row">
        <div class="col-6">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="owner" id="wv" value="wv" checked>
                <label class="form-check-label" for="wv">
                    WV
                </label>
                </div>
                <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="owner" id="cac" value="cac">
                <label class="form-check-label" for="cac">
                    CAC
                </label>
            </div>
            <br /><br />
            <label for="">Select range to delete</label>
            <div class="input-group ms-auto">
                <input type="text" class="form-control" placeholder="From" id="strt" aria-label="From">
                <span class="input-group-text"><-></span>
                <input type="text" class="form-control" placeholder="To" id="endt" aria-label="To">
                <button type="button" class="btn btn-success" id="rmlead">Remove Leads</button>
            </div>
            @if(session('status'))
                <p class="text-success">{{ session('status') }}</p>
            @endif
        </div>
        <div class="col-6"></div>
    </div>
</div>
@endsection