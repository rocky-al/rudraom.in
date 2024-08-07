@extends('layout.admin')
@section('content')

<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> Send Notification To User </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
   <!--  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
</div>
{{ Form::open(['url' => route('notification.send'), 'id'=> 'form_submit', 'class' => 'form_submit_notification', 'method'=>'post']) }}
<div class="modal-body">
    <div class="row">
    	@php
    
@endphp


 <div class="col-md-12 mb-3">
    <label  class="form-label">{{__('Send To')}}</label>
</div>    
    <div class="col-md-12 mb-3">
                     <label  class="form-label">{{__('Select User')}}</label>
                     <input name ="chkall" id="select_usr" type="radio" value="off"  checked>

                  <label  style="margin-left: 200px;" class="form-label">{{__('All User')}}</label>
                  <input name ="chkall" id="chkall" type="radio"  value="on"  > 
     </div>        

    	 <div id="select_usr_show"  class="col-md-12 mb-3">
                 
                 
                     <select    name="user[]" id="user" class="fav_clr form-control" multiple="multiple">

                    <?php foreach ($data as $row) { ?>

                     <option value="{{$row['id']}}" >{{$row['name']}}</option>
                   <?php } ?>
                  </select> 
       </div>
                 <div class="col-md-12 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Title')}}*</label>
                    {{ Form::text('title',null ,['class' =>'form-control', 'placeholder' =>'title', 'required' => true]) }}
                </div>
                 <div class="col-md-12 mb-3">
                    <label for="nameBackdrop" class="form-label">{{__('Message')}}*</label>
                    {{ Form::textarea('message',null ,['class' =>'form-control', 'placeholder' =>'Notification message', 'required' => true,'rows' => 6, 'cols' => 40]) }}
                </div>
               
               

        </div>
        </div>

        <div class="modal-footer">
    <!-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button> -->
    <button type="submit" class="btn btn-primary submit_btn_notification"> Send Notification </button>
</div>
{{ Form::close() }}


<script>
   

$('#form_submit').validate({
    rules: {

       'user[]': {
                 required:true,   
                },
                
        
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
        var button = $('.submit_btn_notification');
        // $(".form_submit_profile").unbind().submit(function (event) {
        //     event.preventDefault(); //prevent default action 
        var form_all = $(".form_submit_notification")[0];
            var formData = new FormData(form_all);

            var old_text = $(button).closest('.submit_btn_notification').text();

            // if ($('.form_submit_profile').valid()) {
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
                            $('#admin_profile').attr('src',data.src);
                            $(form_all)[0].reset();
                            $(".fav_clr").val(null).trigger("change");
                            $('#select_usr_show').show();

                             
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



 <script>
$(document).ready(function(){

   

   $("#select_usr").change(function(){

     if($("#select_usr").is(':checked')){

        $("#user").attr("placeholder", "Type here to search");

          $("#select_usr_show").show();

          } 
          });
   $("#chkall").change(function(){

    if ($("#chkall").is(':checked')){
          $("#select_usr_show").hide();
    }
   });



     $('.fav_clr').select2({
    placeholder: 'Select user',
    width: '100%',




    });
});

$('.fav_clr').on("select2:select", function (e) { 

           var data = e.params.data.text;

           $("#user-error").hide();
           




  
});

$('.fav_clr').on('select2:close', function (e) {
    var uldiv = $(this).siblings('span.select2').find('ul')
     var count = $(this).select2('data').length
     if(count<1){

      $("#user-error").show();

     }


});


/* $("#chkall").click(function(){
        if($("#chkall").is(':checked')){
            $(".fav_clr > option").prop("selected", "selected");
            $(".fav_clr").trigger("change");
            $("#user-error").hide();
        } 
    });
 $("#chkall").click(function(){
        if($("#chkall").is(':unchecked')){
            $(".fav_clr > option").prop("selected", false);
            $(".fav_clr").trigger("change");
             $("#user-error").show();
        } 
    });*/

</script>
 @endsection
