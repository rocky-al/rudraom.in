<!--  edit popup
    =========================== -->
<div id="changepasswordmodal" class="modal fade delivery-address-edit" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0">
      <div class="modal-body p-0">
          <button type="button" class="close text-white opacity-10 text-9 mr-sm-n4 mt-sm-n2 font-weight-300" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
          <div class="row no-gutters"> 
            <div class="col-lg-12 bg-white rounded">
              <h3 class="text-4 mb-2">Change Password</h3>
              <div class="container my-auto">
                <div class="row">
                  <div class="col-12 col-lg-12 mx-auto">
                    <div class="d-flex flex-column">
                      <form  class="form-border change_form" method="post" >
                        <div class="form-group">
                          <label>Old Password <span class="text-danger">*</span> </label>
                          <div class="show_hide_password eye_icon_set" id="show_hide_password">
                          <input id="old_pass" name="old_pass" type="password" class="form-control border-2"  placeholder="Enter old password" >
                            <a  style="display:none;" id="eye_show" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                            <a  id="eye_hide" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                                 </div>
                         
                        </div>
                        <div class="form-group">
                          <label>New Password <span class="text-danger">*</span> </label>
                           <div class="show_hide_password eye_icon_set" id="show_hide_password1">
                          <input id="new_pass" name="new_pass" type="password" class="form-control border-2"  placeholder="Enter new password" >
                           <a  style="display:none;" id="eye_show1" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                            <a  id="eye_hide1" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                                 </div>
                         
                        </div>
                         <div class="form-group">
                          <label>Confirm Password <span class="text-danger">*</span> </label>
                           <div class="show_hide_password eye_icon_set" id="show_hide_password2">
                          <input id="cnf_pass" name="cnf_pass" type="password" class="form-control border-2"  placeholder="Enter confirm password" >
                           <a  style="display:none;" id="eye_show2" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                            <a  id="eye_hide2" href="javascript:;" class=""><i class="zmdi zmdi-hc-fw"></i></a>
                                 </div>
                         
                        </div>

                        <div class="shop-now-but">
                <button id="change_password" type="submit" class="btn btn-primary">Submit</button>
              </div> 
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
<!-- popup edit End --> 



<!-- Modal Image view -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">View Image</h5>
        <button type="button" class="btn-close modal_close" data-bs-dismiss="modal" aria-label="Close">X</button>
      </div>
      <div class="modal-body">
      <div class="modal-body" id="replaceModal"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary modal_close" data-bs-dismiss="modal">Close</button>
   
       
      </div>
    </div>
  </div>
</div>
<!-- End Modal Image view -->



<!-- footer -->

<footer class="full nutriwala-footer">
  <div class="nutriwala-footer-top">
    <div class="container">
     
 
     
    </div>
  </div>
  <div class="nutriwala-copyright">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <p>&#169; Quennections {{date('Y')}} . All Rights Reserved</p>
        </div>
      <!--   <div class="col-md-6">
          <div class="nutriwala-link">
            <ul>
              <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
            </ul> 
          </div>
        </div> -->
      </div>
    </div>
  </div>
</footer>




<!-- end-footer -->


<script>
$('body').on('click', '#change_password', function (event) {

  $(".change_form").validate({
      rules: {
          old_pass: {
              required: true,
              minlength: 6
          } ,

           new_pass:{
          required: true,
           minlength: 6
          },
          cnf_pass: {
              required: true,
              minlength: 6,
             equalTo: "#new_pass"
          } ,
         
      },

     
 submitHandler: function (form) {
    event.preventDefault();
    var formData = new FormData($(".change_form")[0]);
    var id={{ Session::get('user')->id; }};
    $.ajax({
      url: 'change_pswd/'+id,
      type: "POST",
      enctype: 'multipart/form-data',
      data: formData,
            contentType: false,
            cache: false,
            processData: false,
      dataType: 'json',
       beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true);
                            $('.loader').show();
                      
                        },
      success: function (data) {
          if (data.status === true) {
                           
                            $('#changepasswordmodal').modal('hide');
                             $('.loader').hide();
                             $('button[type="submit"]').prop('disabled', false);
                            toastr.success(data.message);
                            $(".change_form")[0].reset();
                          }
          else{
              toastr.error(data.message);
              $('.loader').hide();
              $('button[type="submit"]').prop('disabled', false);
          }

      }
  });
  }
});

   });


</script>


<script> 
   function imageZoom(folderPath, image) {
        
      $(document).ready(function() {
        $(document).on('click', '.modal_close', function() {
          jQuery('#exampleModal').modal('hide')


        })
    });
    
      
        $('#replaceModal').html('<img src="{{url('')}}/' + folderPath + '/' + image + '" alt="Profile Image" style="width: 100%;">');
        
        jQuery('#exampleModal').modal('show')
    }

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

         $("#show_hide_password1 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password1 input').attr("type") == "text") {
                $('#show_hide_password1 input').attr('type', 'password');
                $('#eye_show1').hide();
                 $('#eye_hide1').show();

            } else if ($('#show_hide_password1 input').attr("type") == "password") {
                $('#show_hide_password1 input').attr('type', 'text');
                
                $('#eye_show1').show();
                $('#eye_hide1').hide();
            }
        });

          $("#show_hide_password2 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password2 input').attr("type") == "text") {
                $('#show_hide_password2 input').attr('type', 'password');
                $('#eye_show2').hide();
                 $('#eye_hide2').show();

            } else if ($('#show_hide_password2 input').attr("type") == "password") {
                $('#show_hide_password2 input').attr('type', 'text');
                
                $('#eye_show2').show();
                $('#eye_hide2').hide();
            }
        });

    });
</script>

<script> 
      $(document).ready(function() {
        $(document).on('click', '.close', function() {
        $(".change_form")[0].reset();
        $(".add_form")[0].reset();
        })
    });
  </script>  


