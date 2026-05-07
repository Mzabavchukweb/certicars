@extends('admin.layouts.app')
@section('title','Nowy samochód')
@section('content')
<form method="POST" action="{{ route('admin.cars.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.cars.form', ['car' => null])
</form>
@endsection
