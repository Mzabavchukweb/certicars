@extends('admin.layouts.wizard')
@section('title','Nowy samochód')

@section('wizard-form-open')
<form method="POST" action="{{ route('admin.cars.store') }}" enctype="multipart/form-data" id="wizardForm" style="display:contents">
    @csrf
@endsection

@section('wizard-content')
    @include('admin.cars.wizard-form', ['car' => null])
@endsection

@section('wizard-form-close')
</form>
@endsection

@section('wizard-preview')
    @include('admin.cars.wizard-preview', ['car' => null])
@endsection
