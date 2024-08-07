<!doctype html>
<html lang="en">
  <head>
  <title>@yield('title','') | Quennections</title>
  <!-- initiate head with meta tags, css and script -->
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="images/png" href="{{url('public/frontend/images/favicon.png')}}">
    <!-- Bootstrap CSS -->
    <link href="{{url('public/frontend/css/bootstrap.min.css')}}" type="text/css" rel="stylesheet">
    <link href="{{url('public/frontend/css/font-awesome.min.css')}}" type="text/css" rel="stylesheet">
    <link href="{{url('public/frontend/css/material-design-iconic-font.min.css')}}" type="text/css" rel="stylesheet">
    <link href="{{url('public/frontend/css/gijgo.min.css')}}" type="text/css" rel="stylesheet">
    <link href="{{url('public/frontend/css/style.css')}}" type="text/css" rel="stylesheet">
    <link rel="stylesheet"href="{{url('public/commonFile/css/toastr.min.css')}}" />
    <link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />
      <!-- Sweet Alert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js
"></script>
  <style>
    .error{
      color: red;
      font-size: 12px;}
  </style>
  </head>
  <body>

 

<section class="full loginsignup">
  <div class="auth-sidebar">
    <div class="auth-sidebar-content">
      <div class="auth-sidebar-header">
        <div class="web-logo">
          <img src="{{getSettingValue('logo') ? url('uploads/logo').'/'.getSettingValue('logo') : URL::asset('images/logo.png')}}" style="width:180px">
        </div>
        <h2>Manage Your Business Account</h2>
      </div>
      <div class="auth-sidebar-ul">
        <ul>
          <li>Easy Establishment </li>
          <li>Add Your Feeds </li>
          <li>Get Orders </li>
          <li>Sale Increase </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="content">
  
    <div class="content-bottom">
      <div class="auth-content">

        <div id="registeraccount" class="nuti-login-register" role="dialog" aria-hidden="true">

          @if ($message = Session::get('success'))
  <strong>{{ $message }}</strong>
@endif
@if ($message = Session::get('error'))
  <strong style="color:red">{{ $message }}</strong>
@endif
        
                  
                    
                          <div class="login login-main">
                            <div class="loginweblogo-mobile">
                              <img src="https://ninehertz.orbitnapp.com/quennections/uploads/logo/logo.png" style="width:70px">
                            </div>
                            <h3 class="text-4 mb-2">Sign in to Quennections</h3>
                            <p>Welcome Back!  Please enter your details.</p>
                            <div class="d-flex  flex-column align-items-center mb-3">
                              <form id="loginForm" class="form-border" method="post">
                                @csrf
                                <div class="form-group">
                                  <input name="email" type="email" class="form-control border-2"  placeholder="Email id" required>
                                </div>
                                <div class="form-group">
                                    <div class="" id="show_hide_password">
                                  <input name="password"  class="form-control border-2"  placeholder="Password"  type="password" required="" minlength="6" maxlength="12" required>
                                   <a  style="display:none;" id="eye_show" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>

                                    <a  id="eye_hide" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                                 </div>

                                </div>
                               
                                 <div class="form-group">
                                  <button type="submit" class="btn btn-primary form-control">Login</button>
                                </div>
                                 <h6 class="text-center">Not a member? <a href="javascript:" class="register_open">Register Now</a> </h6>
                             
                               
                              <!--  <div class="shop-now-but otp-btn">
                                  <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#createmodal">Sign Up</button>
                                </div>  -->
                              </form>
                            </div>
                          </div>
                          <div class="mx-auto sign_up" style="display:none;">
                            <div class="loginweblogo-mobile">
                              <img src="https://ninehertz.orbitnapp.com/quennections/uploads/logo/logo.png" style="width:70px">
                            </div>
                            <h3 class="text-4 mb-2">Create your account </h3>
                            <p>Enter the fields below to get started</p>
                           
                            <div class="d-flex  flex-column align-items-center mb-3 RegisterDiv">
                              <form id="register_form" class="form-border" method="post">
                               
                               <div class="row">
                                <div class="form-group col-md-6" >
                                 <label >Business Category <span class="text-danger">*</span></label>
                                  <select class="form-control" name="category" id="category"  >
                                      <option value=""  selected disable>Select Category</option>
                            @if(isset($category) && count($category) > 0)
                            @foreach($category as $category_list)
                                <option value="{{$category_list->id}}" >{{$category_list->name}}</option>
                            @endforeach
                            @endif
                        </select>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business Name <span class="text-danger">*</span></label>
                                  <input name="name_bsns" type="text" class="form-control border-2"  placeholder="Name of the business" required>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business address <span class="text-danger">*</span></label>
                                  <input name="address" type="text" class="form-control border-2"  placeholder="Business address" required>
                                </div>

                                 <div class="form-group col-md-6">
                                   <label >Business location <span class="text-danger">*</span></label>
                               <!--    <input name="location" type="text" class="form-control border-2"  placeholder="Business location" required> -->
                                           <input placeholder="Enter Address 2" class="form-control pac-target-input valid" name="address2" id="address2" onfocus="Address2initialize()" type="text" autocomplete="off" aria-required="true" aria-invalid="false" required>
                                             <input name="latitude" id="latitude2" value="26.8865278" type="hidden">
                                            <input name="longitude" id="longitude2" value="75.7696936" type="hidden">
                                </div>


                                 <div class="form-group col-md-6">
                                   <label >Business Phone number <span class="text-danger">*</span></label>
                                  <input name="phone_no" type="text" class="form-control border-2"  placeholder="Business Phone number" required>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business Email id <span class="text-danger">*</span></label>
                                  <input name="email" type="email" class="form-control border-2"  placeholder="Business Email id" required>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business License number <span class="text-danger">*</span></label>
                                  <input name="lic_no" type="text" class="form-control border-2"  placeholder="Business License number/Registration number (EIN)" required>
                                </div>
                                <div class="form-group col-md-6">
                                   <label >Business Registration date <span class="text-danger">*</span></label>
                                  <input name="regs_date" type="date" id="date" class="form-control border-2 reg_date"  placeholder="Business Registration date" required>
                                </div>

                                  <div class="form-group col-md-12">
                                   <label >Business Description <span class="text-danger">*</span></label>
                                 <textarea class="form-control" id="description" placeholder="Description" name="description" rows="6" cols="50" required></textarea>
                                </div>
                            
                                 <div class="form-group col-md-6" >
                                  <label >Business Profile Image <span class="text-danger">*</span></label>
                                  <input name="profife_pic" id="profife_pic" type="file" class="form-control border-2"  placeholder="Business profile image"  accept="image/png, image/jpg, image/jpeg"  onchange="pressed()" required>
                                <small style="position: relative;top: -5px;"> &nbsp;(Only formats are allowed: jpeg, jpg, png)</small><br>
                                <small style="position: relative;top: -5px; font-size:12px;"> &nbsp;<i>Note: For batter image view in the mobile app please upload in the 16 * 9 ratio (for example: 1080*600)</i></small>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business Photo <span class="text-danger">*</span></label>
                                  <input  type="file" class="form-control border-2"  placeholder="Business Photo" name="photos[]" accept="image/png, image/jpg, image/jpeg"  multiple  required>
                                    <small style="position: relative;top: -5px;"> &nbsp;(Only formats are allowed: jpeg, jpg, png)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                    <img style="display:none; width: 100px;" id="imagePreview" src="" class="logo-icon" alt="logo icon" >
                </div>
                                 
                                </div>
                               
                                 <div class="form-group">
                                  <button type="submit" class="btn btn-primary form-control">Register</button>
                                </div>
                               <h6 class="text-center ">Already have any account? <a href="javascript:" class="login_open">Log In</a> </h6>
                             </form>
                            </div>
                          </div>
                      
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>

      </div>
   
    </div>
  </div>
 
  </div>
</section>      
@include('frontend.footer')  

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;libraries=places&amp;key=AIzaSyDxAficSn655AdZI8XCDZZykL6kIPGmq2g"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">

     function Address2initialize() {
        var input = document.getElementById('address2');
        var options = {
            //  types: ['(regions)'],
           // componentRestrictions: {country: "in"}
        };
        //var options = {}
        var autocomplete = new google.maps.places.Autocomplete(input, options);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            var placeId = place.place_id;
            // to set city name, using the locality param
            /*var componentForm = {
             locality: 'short_name',
             };*/
            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };

            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    //   document.getElementById("city").value = val;
                }
            }
            document.getElementById("latitude2").value = lat;
            document.getElementById("longitude2").value = lng;
            // document.getElementById("location_id").value = placeId;
        });
    }



  $(document).ready(function() {

 $('#category').change(function(){
  //alert("ghjdh");
    if ($(this).val()!="")
    {
        $(this).valid();
    }
});


 

 window.pressed = function(){
    var a = document.getElementById('profife_pic');
    if(a.value == "" )
    {
        $('#imagePreview').hide();
    }
    };


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


   /* $("#profife_pic").change(function() {   
        readURL(this);
    });*/
    


    $(".register_open").click(function(){
    $(".login").hide();
    $(".sign_up").show();

    });

    $(".login_open").click(function(){
    $(".login").show();
    $(".sign_up").hide();

   });

   $("#loginForm").validate({
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
    submitHandler: function (form) {
        //form.submit();
        var formData = new FormData($("#loginForm")[0]);
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url:"{{route('frontend.login')}}",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true);
                            $('.loader').show();
                      
                        },
                        success: function(data) {
                            if (data.status === true) {
                                if (data.redirect_url != '') {
                                    window.location.replace(data.redirect_url);
                                }
                                toastr.success(data.message);
                                $('button[type="submit"]').prop('disabled', false);
                                $('.loader').hide();

                               

                            } else {
                                // /grecaptcha.reset();
                                toastr.error(data.message);
                                $('button[type="submit"]').prop('disabled', false);
                                $('.loader').hide();
                               
                            }
                        },
                        error: function(e) {
                            $('button[type="submit"]').prop('disabled', false);
                          
                            toastr.error(e.responseJSON.message);
                            $('.loader').hide();
                        }
        });
        return false;
    }
});

   $.validator.addMethod("custom_email", 
    function(value, element) {
        return /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(value);
    }, 
    "Please enter a valid email address."
);

   jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[\w]+$/i.test(value);
}, "Letters, numbers only please");

   jQuery.validator.addMethod("greaterThan", 
function(value, element, params) {

    if (!/Invalid|NaN/.test(new Date(value))) {
        return new Date(value) > new Date($(params).val());
    }

    return isNaN(value) && isNaN($(params).val()) 
        || (Number(value) > Number($(params).val())); 
},'Must be greater than {0}.');


var date = new Date().toISOString().slice(0,10);
$('#date').attr('max', date);

    $("#register_form").validate({
    rules: {
      phone_no:{
         digits:true,
         minlength:10,
         maxlength:10
      },
      lic_no:{
         alphanumeric: true
      },

      category:{
            required: true,
           
        },
      email:{
           required:true,
           custom_email: true
      },
      profife_pic:{
        required:true,
        extension: "jpg|jpeg|png"
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
    submitHandler: function (form) {
        //form.submit();
        var formData = new FormData($("#register_form")[0]);
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url:"{{route('frontend.register')}}",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true);
                            $('.loader').show();
                      
                        },
                        success: function(data) {
                            if (data.status === true) {
                               /* if (data.redirect_url != '') {
                                    window.location.replace(data.redirect_url);
                                }
                                toastr.success(data.message);*/
                                $('button[type="submit"]').prop('disabled', false);
                                $("#register_form")[0].reset();
                                $("#imagePreview").hide();
                                $('.loader').hide();

                                 swal({ 
                            title: "Congratulations!!",
                            text: "Your business account has been Registered Once the admin will approve your account, you will receive the Login details on your registered Email ID",
                            allowOutsideClick: false,
                            }).then(okay => {
                           if (okay) {
                            window.location.replace(data.redirect_url);
                          }
                          else{
                               window.location.replace(data.redirect_url);
                          }
                             });

                               

                            } else {
                              
                                // /grecaptcha.reset();
                                toastr.error(data.message);
                                $('button[type="submit"]').prop('disabled', false);
                                 $('.loader').hide();
                               
                            }
                        },
                        error: function(e) {
                            $('button[type="submit"]').prop('disabled', false);
                          
                            toastr.error(e.responseJSON.message);
                             $('.loader').hide();
                        }
        });
        return false;
    }
});

    });
</script>



<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#eye_show').hide();
                 $('#eye_hide').show();

            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                
                $('#eye_show').show();
                $('#eye_hide').hide();
            }
        });

    });
</script>

<!-- End-datepicker-js -->
 

  </body>
</html>
