<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
{{ Form::open(['url' => route('business.manage'), 'class' => 'form_submit_user', 'method'=>'post', 'files' => 'yes']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">

               <!--  <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Name')}}*</label>
                    {{ Form::text('name',$data->name ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter  name', 'required' => true]) }}
                </div>

              
                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Email')}}*</label>
                    {{ Form::text('email',$data->email_address ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter email', 'required' => true]) }}
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Phone No.')}}*</label>
                    {{ Form::number('mobile',$data->phone_no ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter phone no.', 'required' => true]) }}
                </div>

                 <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Address')}}*</label>
                    {{ Form::text('address',$data->address ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter  address', 'required' => true]) }}
                </div>

              
                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Licence No')}}*</label>
                    {{ Form::text('licence_no',$data->license_no ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter license no', 'required' => true]) }}
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Regestration Date')}}*</label>
                    {{ Form::date('regs_date',$data->registration_date ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter phone no.', 'required' => true]) }}
                </div>


                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Opening Time')}}*</label>
                    {{ Form::text('opening_time',$data->opening_time ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter  opening time', 'required' => true]) }}
                </div>

              
                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Closing Time')}}*</label>
                    {{ Form::text('closing_time',$data->closing_time ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter closing time', 'required' => true]) }}
                </div>

               <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Image')}}*</label>
                    {{ Form::file('image' ,['class' =>'form-control', 'id' => 'fileControl', 'accept' =>'.png, .jpg, .jpeg']) }}
                </div> -->
                <div class="col-md-12 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Status')}}*</label>
                  {!! Form::select('status', array('0' => 'Pending', '1' => 'Approved','2' => 'Rejected'), $data->status,['class' =>'form-control status', 'required' => true]); !!}
                </div>

                 <div class="col-md-12 mb-3 reason" style="display:none;">
                    <label for="nameBackdrop" class="form-label">{{__('Reason')}}*</label>
                   {!! Form::textarea('reason',$data->reject_reason,['class'=>'form-control', 'rows' => 2, 'cols' => 40,'placeholder' =>'Rejected Reason', 'required' => true]) !!}
                </div>

               <!--   <div class="col-md-6 mb-3">
                    <img id="imagePreview" src="{{$data->profile_img ? url('uploads/business_image').'/'.$data->profile_img : URL::asset('images/default_user.png')}}" class="logo-icon" alt="profile image">
                </div>
 -->
                   
                {!! Form::hidden('id', $data->id ?? '') !!}

               
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button>
    <button type="submit" class="btn btn-primary submit_btn_user"> Submit </button>
</div>
{{ Form::close() }}

<script type="text/javascript">

 /*     //image picker and preview 
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#fileControl").change(function() {
        readURL(this);
    });*/
    var check= $(".status").val();
    if(check==2){
        $(".reason").show();
    }
    $(document).on('change', '.status', function() {
    var check= $(".status").val();
    if(check==2){
        $(".reason").show();
    }
    else{
        $(".reason").hide();
    }


});


$('.form_submit_user').validate({
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
        var button = $('.submit_btn_user');
        // $(".form_submit_user").unbind().submit(function (event) {
        //     event.preventDefault(); //prevent default action 
        var form_all = $(".form_submit_user")[0];
            var formData = new FormData(form_all);

            var old_text = $(button).closest('.submit_btn_user').text();

            // if ($('.form_submit_user').valid()) {
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
                             $('#data_table').DataTable().ajax.reload(null, false );}
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