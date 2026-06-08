@extends('admin.layouts.wizard')
@section('title','Edycja: '.$car->title)

@section('wizard-form-open')
<form method="POST" action="{{ route('admin.cars.update',$car) }}" enctype="multipart/form-data" id="wizardForm" class="wz-form">
    @csrf @method('PUT')
@endsection

@section('wizard-content')
    @include('admin.cars.wizard-form', ['car' => $car])
@endsection

@section('wizard-form-close')
</form>
@endsection

@section('wizard-preview')
    @include('admin.cars.wizard-preview', ['car' => $car])
@endsection
