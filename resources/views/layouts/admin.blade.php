@extends('layouts.app')

@section('title', ($title ?? 'Admin').' — '.config('app.name'))

@section('content')
    @yield('admin_content')
@endsection
