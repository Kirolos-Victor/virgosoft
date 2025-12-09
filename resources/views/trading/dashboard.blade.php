@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <order-form initial-symbol="BTC"></order-form>
        </div>
        <div>
            <orderbook symbol="BTC"></orderbook>
        </div>
    </div>
</div>
@endsection
