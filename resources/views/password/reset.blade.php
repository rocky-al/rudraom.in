@extends('layout.admin_auth')
@section('content')
<div class="container-fluid">
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
        <div class="col mx-auto">
            <div class="card">
                <div class="card-body">
                <div class="border p-4 rounded">
                  

                    
                    <div class="text-center">
                            <h4 class="mb-2"> Genrate New Password 🔒 </h4>
                            <p>We received your reset password request. Please enter your new password!
                            </p>
                        </div>
                        <div class="d-grid">
                        </div>
                        <div class="login-separater text-center mb-4"> <span> WITH EMAIL</span>
                            <hr />
                        </div>




                    {{ Form::open(['url' => route('password.update'), 'class' => 'form_submit', 'id'=>'formAuthentication' , 'method'=>'Post']) }}

                    <div class="mb-3">
                        <label for="email" class="form-label">Password*</label>
                        {{ Form::password('password',['class' =>'form-control', 'id'=>'password', 'placeholder' =>'Enter Password' ]) }}
                    </div>

                    <input type="hidden" name="token" value="{{request()->token}}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Confirm Password</label>
                        {{ Form::password('confirm_password',['class' =>'form-control', 'placeholder' =>'Enter Confirm Password' ]) }}
                    </div>


                    <button class="btn btn-primary d-grid w-100 submit_btn" type="submit"> Reset Password </button>
                    {{ Form::close() }}
                    </div>

                    <div class="text-center mt-2">
                        <a href="{{route('admin.login')}}" class="d-flex align-items-center justify-content-center">
                            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Forgot Password -->


@endsection