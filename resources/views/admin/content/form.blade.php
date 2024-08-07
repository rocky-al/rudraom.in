<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
{{ Form::open(['url' => route('content.manage'), 'class' => 'form_submit', 'method'=>'post', 'files' => 'yes']) }}
<div class="modal-body">
    <div class="row">


        <div class="col-md-12">
            <div class="row">
                <div class="col mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Title')}}*</label>
                    {{ Form::text('title',$data->title ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter Title']) }}
                </div>
                {!! Form::hidden('id', $data->id ?? '') !!}

                <div class="col mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Name')}}*</label>
                    {{ Form::text('name',$data->name ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter Name']) }}
                </div>
            </div>

             <div class="row">
            <div class="col mb-3">
                    <label for="editor" class="form-label">{{__('Description')}}</label>
                    {{ Form::textarea('description',$data->description ?? '' ,['class' =>'form-control', 'id'=>'editor', 'placeholder' =>'Enter Description','required' => true]) }}
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

<script>
    $(document).ready(function() {
      
CKEDITOR.replace('editor',{
           fullPage: true,
           //extraPlugins: 'panelbutton,colorbutton,colordialog,justify,indentblock,aparat,buyLink',
           // You may want to disable content filtering because if you use full page mode, you probably
           // want to  freely enter any HTML content in source mode without any limitations.
           allowedContent: true,
           autoGrow_onStartup: true,
           enterMode: CKEDITOR.ENTER_BR
       }).on('key',
         function(e){
             setTimeout(function(){
                 document.getElementById('editor').value = e.editor.getData();
             },10);

             });
       
    });
</script>