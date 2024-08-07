<!doctype html>
<html lang="en">
<head>
    @include('admin.particles.header')
    @stack('css')
</head>

<body class="bg-login">
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            @yield('content')
        </div>
    </div>


    @stack('scripts')
    <script src="{{URL::asset('commonFile/js/validate.js')}}"></script>

    <script src="{{URL::asset('commonFile/js/toastr.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $(".form_submit").submit(function(event) {
                event.preventDefault(); //prevent default action 

                var old_text = $('button[type="submit"]').text();
                if ($(this).valid()) {
                    $('button[type="submit"]').prop('disabled', true);
                    $('button[type="submit"]').text('Please wait...');
                } else {
                    $('button[type="submit"]').prop('disabled', false);
                    $('button[type="submit"]').text(old_text);
                }

                if ($('.form_submit').valid()) {
                    $.ajax({
                        url: $(this).attr("action"),
                        type: $(this).attr("method"),
                        data: $(this).serialize(),
                        success: function(data) {
                            if (data.status === true) {
                                if (data.redirect_url != '') {
                                    window.location.replace(data.redirect_url);
                                }
                                toastr.success(data.message);
                                $('button[type="submit"]').prop('disabled', false);
                                $('button[type="submit"]').text(old_text);

                            } else {
                                toastr.error(data.message);
                                $('button[type="submit"]').prop('disabled', false);
                                $('button[type="submit"]').text(old_text);
                            }
                        },
                        error: function(e) {
                            $('button[type="submit"]').prop('disabled', false);
                            $('button[type="submit"]').text(old_text);
                            toastr.error(e.responseJSON.message);
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>