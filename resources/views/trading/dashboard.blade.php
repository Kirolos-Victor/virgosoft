@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <order-form :user-id="{{ auth()->id() }}"></order-form>
@endsection
