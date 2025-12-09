@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <order-form></order-form>
            </div>
            <div>
                <wallet-overview :user-id="{{ auth()->id() }}"></wallet-overview>
            </div>
        </div>
        <user-orders></user-orders>
    </div>
@endsection
