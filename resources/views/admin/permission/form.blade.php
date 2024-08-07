@php
use App\Models\Module;
$module = Module::where('status', Constant::ACTIVE)->pluck('name', 'id');
@endphp

<div class="modal-header">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
{{ Form::open(['url' => route('permission.manage'), 'class' => 'form_submit', 'method'=>'post']) }}
<div class="modal-body">
<div class="row">

    <div class="col mb-3">
        <label for="nameBackdrop" class="form-label">{{__('Module')}}</label>
        {!! Form::select('module_id', $module ?? [], null, ['class' =>'form-control select_2', 'placeholder' =>'Select Module']) !!}
    </div>

    <div class="col mb-3">
        <label for="nameBackdrop" class="form-label">{{__('Name')}}</label>
        {{ Form::text('name',$user->name ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter Role Name',  'required' , 'maxlength'=>'30']) }}
    </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button>
    <button type="submit" class="btn btn-primary submit_btn"> Submit </button>
</div>
{{ Form::close() }}