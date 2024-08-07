<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> New Payment Request </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
{{ Form::open(['url' => route($page.'.manage'), 'class' => 'form_submit', 'method'=>'post', 'files' => 'yes']) }}

<div class="modal-body">
    <div class="row">


        <div class="col-md-12">
            

            <div class="row">
                <div class="col mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Amount')}}*</label>
                    {{ Form::number('amount', null ,['class' =>'form-control', 'min'=>1, 'max'=>10000, 'placeholder' =>'Amount']) }}
                </div>
                <div class="col mb-3">
                </div>
            </div>
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