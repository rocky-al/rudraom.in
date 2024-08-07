<div class="modal-header model_loader">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <img class="model_loader_class" src="{{URL::asset('images/model_loader.gif')}}" alt="Loader.gif" id="model_loader">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
{{ Form::open(['url' => route('admin.updatePassword'), 'class' => 'form_submit', 'method'=>'post']) }}
<div class="modal-body">
    <div class="row">


<div class="col-md-12">
    <div class="row">
        <div class="col-md-4 mb-3" >
            <label for="nameBackdrop" class="form-label">{{__('Current Password')}}*</label>
            <div class="forgotpassword">
                <div class="" id="show_hide_password">
                {{ Form::password('current_password' ,['class' =>'form-control', 'placeholder' =>'Current Password']) }}
                <a  href="javascript:;" class=""><i class='bx bx-hide'></i></a>
            </div>
        </div></div>
        {!! Form::hidden('id', $data->id ?? '') !!}
        <div class="col-md-4 mb-3">
            
            <label for="nameBackdrop" class="form-label">{{__('New Password')}}*</label>
            <div class="forgotpassword">
             <div class="" id="show_hide_password1">
            {{ Form::password('password' ,['class' =>'form-control', 'id' =>'password', 'placeholder' =>'New Password']) }}
             <a  href="javascript:;" class=""><i class='bx bx-hide'></i></a>
        </div>  </div></div>

        <div class="col-md-4 mb-3">
           
            <label for="nameBackdrop" class="form-label">{{__('Confirm Password')}}*</label>
            <div class="forgotpassword">
             <div class="" id="show_hide_password2">
            {{ Form::password('confirm_password' ,['class' =>'form-control', 'placeholder' =>'Confirm Password']) }}
             <a  href="javascript:;" class=""><i class='bx bx-hide'></i></a>
         </div>
        </div>  </div>

    </div>

   
</div>

</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button>
    <button type="submit" class="btn btn-primary submit_btn"> Update Password </button>
</div>
{{ Form::close() }}
<script src="{{URL::asset('admin/js/jquery.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });

          $("#show_hide_password1 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password1 input').attr("type") == "text") {
                $('#show_hide_password1 input').attr('type', 'password');
                $('#show_hide_password1 i').addClass("bx-hide");
                $('#show_hide_password1 i').removeClass("bx-show");
            } else if ($('#show_hide_password1 input').attr("type") == "password") {
                $('#show_hide_password1 input').attr('type', 'text');
                $('#show_hide_password1 i').removeClass("bx-hide");
                $('#show_hide_password1 i').addClass("bx-show");
            }
        });

            $("#show_hide_password2 a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password2 input').attr("type") == "text") {
                $('#show_hide_password2 input').attr('type', 'password');
                $('#show_hide_password2 i').addClass("bx-hide");
                $('#show_hide_password2 i').removeClass("bx-show");
            } else if ($('#show_hide_password2 input').attr("type") == "password") {
                $('#show_hide_password2 input').attr('type', 'text');
                $('#show_hide_password2 i').removeClass("bx-hide");
                $('#show_hide_password2 i').addClass("bx-show");
            }
        });
    });
</script>
