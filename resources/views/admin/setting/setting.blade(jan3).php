@extends('layout.admin')
@section('content')

<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
   <!--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
</div>
{{ Form::open(['url' => route('admin.update.setting'), 'class' => 'form_submit_setting', 'method'=>'post']) }}

<div class="card mt-3">
    <div class="card-body">
        
    

    <div class="row">
        <div class="col-md-12">
            <div class="row">

            <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Radius Location(Km)')}}*</label>
                    {{ Form::text('radius_location',$array['radius_location'] ,['class' =>'form-control', 'placeholder' =>'Radius Location(Km)', 'required' => true]) }}
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Services Charge')}}*</label>
                    {{ Form::text('service_charges',$array['service_charges'] ,['class' =>'form-control', 'placeholder' =>'Enter Service Charges', 'required' => true]) }}
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Address')}}*</label>
                    {{ Form::text('address',$array['address'] ,['class' =>'form-control', 'placeholder' =>'Enter Address', 'required' => true]) }}
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Logo')}}*</label>
                    {{ Form::file('logo' ,['class' =>'form-control', 'id' => 'fileControl', 'accept' =>'.png, .jpg, .jpeg']) }}
                </div>
                <div class="col-md-6 mb-3">
                    <img id="imagePreview" src="{{getSettingValue('logo') ? url('uploads/logo').'/'.getSettingValue('logo') : URL::asset('images/logo.png')}}" class="logo-icon" alt="logo icon">
                </div>
            </div>
        </div>
        <div class="col-md-12">

            <h6 class="border-top  pt-3">SMTP Config</h6><br>
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Hostname')}}*</label>
                    {{ Form::text('smtp_host',$array['smtp_host'] ,['class' =>'form-control', 'placeholder' =>'SMTP Host Name', 'required' => true]) }}
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Username')}}*</label>
                    {{ Form::text('smtp_username',$array['smtp_username'] ,['class' =>'form-control', 'placeholder' =>'SMTP User Name', 'required' => true]) }}
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Password')}}*</label>
                    <input class="form-control" value="{{$array['smtp_username']}}" name="smtp_password" type="password" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Port')}}*</label>
                    {{ Form::text('smtp_port',$array['smtp_port'] ,['class' =>'form-control', 'placeholder' =>'SMTP Port', 'required' => true]) }}
                </div>

                <div class="col-md-4 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('no-replt mail')}}*</label>
                    {{ Form::text('smtp_noreply',$array['smtp_noreply'] ,['class' =>'form-control', 'placeholder' =>'no-reply mail', 'required' => true]) }}
                </div>
            </div>
        </div>
    </div>

<div class="modal-footer pe-0">
    <!-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button> -->
    <button type="submit" class="btn btn-primary submit_btn_setting"> Submit </button>
</div>
{{ Form::close() }}

</div>
</div>

<script type="text/javascript">
    //image picker and preview 
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
    });

$('.form_submit_setting').validate({
    rules: {
        service_charges:{
            number:true
        }
        
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
        var button = $('.submit_btn_setting');
        // $(".form_submit_setting").unbind().submit(function (event) {
        //     event.preventDefault(); //prevent default action 
         var form_all = $(".form_submit_setting")[0];
            var formData = new FormData(form_all);

            var old_text = $(button).closest('.submit_btn_setting').text();

            //if ($('.form_submit_setting').valid()) {
                // $(button).prop('disabled', true);
                // $(button).text('Please wait...');
                // //model loader add 
                // $('#model_loader').removeClass('model_loader_class');
                // $('#model_loader').addClass('loader_image_heder');

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
                            $("#fileControl").val('');
                            $('#site_logo').attr('src',data.src);
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
 @endsection