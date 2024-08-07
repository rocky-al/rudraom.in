<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
{{ Form::open(['url' => route('emailTemplate.manage'), 'class' => 'form_submit_email', 'method'=>'post', 'files' => 'yes']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Title')}}*</label>
                    {{ Form::text('title',$data->title ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter Title', 'required' => true]) }}
                </div>

                <div class="col mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Subject')}}*</label>
                    {{ Form::text('subject',$data->subject ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter subject', 'required' => true]) }}
                </div>

                {!! Form::hidden('id', $data->id ?? '') !!}

               
            </div>

            <div class="row">
            <div class="col mb-3">
                    <label for="editor" class="form-label">{{__('Description')}}*</label>
                    {{ Form::textarea('description',$data->description ?? '' ,['class' =>'form-control', 'id'=>'editor', 'placeholder' =>'Enter Description']) }}
                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button>
    <button type="submit" class="btn btn-primary submit_btn_email"> Submit </button>
</div>
{{ Form::close() }}

<script type="text/javascript">
     $(document).ready(function () {

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
$('.form_submit_email').validate({
    rules: {
        
    },
    messages: {
       
    },
    errorElement: 'span',
      errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-control').parent().append(error);
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      },
 /*  submitHandler: function (form) {
        $('.loader').show();
  $( "#event_add" ).submit();
        form.submit();

    } */
    submitHandler: function (form) {
        var button = $('.submit_btn_email');
        // $(".form_submit_email").unbind().submit(function (event) {
        //     event.preventDefault(); //prevent default action 
        var form_all = $(".form_submit_email")[0];
            var formData = new FormData(form_all);

            var old_text = $(button).closest('.submit_btn_email').text();

            // if ($('.form_submit_email').valid()) {
            //     $(button).prop('disabled', true);
            //     $(button).text('Please wait...');
            //     //model loader add 
            //     $('#model_loader').removeClass('model_loader_class');
            //     $('#model_loader').addClass('loader_image_heder');

                $.ajax({
                    url: $(form_all).attr("action"),
                    type: $(form_all).attr("method"),
                    data: formData,
                    contentType: false, //this is requireded please see answers above
                    processData: false,
                    beforeSend: function(){
                        $(button).prop('disabled', true);
                        $(button).text('Please wait...');
                        //model loader add 
                        $('#model_loader').removeClass('model_loader_class');
                        $('#model_loader').addClass('loader_image_heder');
                    },
                    success: function (data) {
                        if (data.status === true) {
                            if (typeof (data.redirect_url) != "undefined" && data.redirect_url !== null) {
                                window.location.replace(data.redirect_url);
                            }
                            $('#model_loader').addClass('model_loader_class');
                            $('#model_loader').removeClass('loader_image_heder');
                            toastr.success(data.message);
                            $('#manage_data_model').modal('hide');
                            //console.log($('#data_table').length);
                            if($('#data_table').length>0){
                            $('#data_table').DataTable().ajax.reload(); }
                            $(button).prop('disabled', false);
                            $(button).text(old_text);
                        } else {
                            toastr.error(data.message);
                            $(button).prop('disabled', false);
                            $(button).text(old_text);
                            $('#model_loader').addClass('model_loader_class');
                            $('#model_loader').removeClass('loader_image_heder');
                            $(button).prop('disabled', false);
                            $(button).text(old_text);
                        }
                    },
                    error: function (e) {
                        $(button).prop('disabled', false);
                        $(button).text(old_text);
                        $('#model_loader').addClass('model_loader_class');
                        $('#model_loader').removeClass('loader_image_heder');
                        $(button).prop('disabled', false);
                        $(button).text(old_text);
                        toastr.error(e.responseJSON.message);
                    }
                });
            //}
        //});
    }
});
</script>