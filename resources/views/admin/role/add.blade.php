@extends('layout.admin')
@section('content')


<div class="d-lg-flex align-items-center">
    <div class="position-relative">
        <h5 class="page_heading"> {{$title ?? ''}} </h5>
    </div>
    <div class="ms-auto"> {!! backAction(route($page.'.index') , 'Back') !!} </div>
</div>
<hr class="page_row">

<div class="card">
    <div class="card-body">
        {{ Form::open(['url' => route($page.'.create'), 'class' => 'form_submit', 'method'=>'post']) }}
        @include('admin.'.$page.'.form')
        {{ Form::close() }}
    </div>
</div>
@endsection