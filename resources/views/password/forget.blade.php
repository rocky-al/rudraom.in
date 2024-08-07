@extends('layout.admin_auth')
@section('content')

<div class="container-fluid">
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
        <div class="col mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">

                        <div class="text-center">
                            <h4 class="mb-2"> Forgot Password? 🔒 </h4>
                            <p>Enter your registered email ID to reset the password
                            </p>
                        </div>
                        <div class="d-grid">
                        </div>
                        <div class="login-separater text-center mb-4"> <span>WITH EMAIL</span>
                            <hr />
                        </div>


                        <div class="form-body">
                            {{ Form::open(['url' => route('sendLink'), 'class' => 'form_submit', 'id'=>'formAuthentication' , 'method'=>'Post']) }}
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                {{ Form::email('email', null ,['class' =>'form-control submit_btn', 'placeholder' =>'Enter your email ' ]) }}

                            </div>

                            <div class="col-12">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary submit_btn">Send Reset Link</button>
                                </div>
                            </div>
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
    @endsection