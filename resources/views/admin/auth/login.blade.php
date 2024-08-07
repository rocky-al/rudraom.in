@extends('layout.admin_auth')
@section('content')
<div class="container-fluid">
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
        <div class="col mx-auto">
            <!-- <div class="mb-4 text-center">
                <img src="{{URL::asset('images/logo.png')}}" width="180" alt="" />
            </div> -->
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="text-center">
                            <h4 class="mb-2 signheading">Welcome to {{Constant::APP_NAME}}! </h4>
                            <p>Please sign-in to your account...
                            </p>
                        </div>
                        <div class="d-grid">
                        </div>
                        <div class="login-separater text-center mb-4"> <span>OR SIGN IN WITH EMAIL</span>
                            <hr />
                        </div>
                        <div class="form-body">
                            <!-- <form class="row g-3"> -->
                            {{ Form::open(['url' => route('admin.do_login'), 'class' => 'form_submit', 'method'=>'Post']) }}
                            <div class="col-12">
                                <label for="inputEmailAddress" class="form-label">Email Address</label>
                                {{ Form::email('email', null ,['class' =>'form-control mt-1', 'placeholder' =>'Enter Email' ]) }}
                            </div>
                            <div class="col-12 mt-3">
                                <label for="inputChoosePassword" class="form-label">Enter Password</label>
                                <div class="input-group" id="show_hide_password">
                                    {{ Form::password('password',['class' =>'form-control mt-1 border-end-0', 'placeholder' =>'Password', 'id'=>"inputChoosePassword" ]) }}<a href="javascript:;" class="input-group-text mt-1 bg-transparent"><i class='bx bx-hide'></i></a>
                                </div>
                            </div>

                            <div class="row mt-2 mb-4">
                                <div class="col-md-7">
                                </div>

                                <div class="col-md-5 text-end"> <a href="{{route('forget.password')}}">Forgot Password ?</a>

                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary"><i class="bx bxs-lock-open"></i>Sign in</button>
                                </div>
                            </div>
                            <!-- </form> -->
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
</div>
@endsection

@push('scripts')
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
    });
</script>

@endpush
