@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <wallet-overview :user-id="{{ auth()->id() }}"></wallet-overview>
</div>
@endsection
