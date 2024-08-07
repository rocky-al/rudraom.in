@extends('layout.admin')
@section('content')
<div class="card">
<div class="card-header">
        <div class="d-lg-flex align-items-center mb-2 mt-2">
            <div class="position-relative">
                <h5> {{$title ?? ''}} </h5>
            </div>
            <div class="ms-auto"> {!! backAction(route($page.'.index') , 'Back') !!} </div>
        </div>
    </div>
    <div class="card-body">
        {{ Form::open(['url' => route('role.update', $data->id), 'class' => 'form_submit', 'method'=>'post']) }}
        @include('admin.'.$page.'.form')
        {{ Form::close() }}
    </div>
</div>
@endsection