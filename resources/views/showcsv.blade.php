@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10">
            <h2>CSV file contains following Data</h2>
            
            <table class="table table-striped">
            @foreach ($csv as $v)
            <tr>
                <td>{{ $v['platform'] }}</td>
                <td>{{ $v['business_name'] }}</td>
                <td>{{ $v['full_name'] }}</td>
                <td>{{ $v['business_sector'] }}</td>
                <td>{{ $v['state'] }}</td>
                <td>{{ $v['city'] }}</td>
                <td>{{ $v['phone'] }}</td>
                <td>{{ $v['email'] }}</td>
            </tr>
            @endforeach
            </table>
            <a href="{{ route('store-file', ['name' => $name]) }}" class="btn btn-primary">Save Data</a>
            @if (session('status'))
                <h4>{{ session('status') }}</h4>
            @endif
        </div>
        <div class="col-md-2">
            
        </div>
    </div>
    
</div>
@endsection