<!doctype html>
<html lang="en" dir="ltr">

<head>
@include('web.particles.header')

       @stack('css')
</head>

<body class="">

    <!--Loader-->
    <div id="global-loader"><img src="{{URL::asset('web/images/other/loader.svg')}}" class="loader-img floating" alt=""></div>
    @include('web.particles.nav')

   @yield('content')

    @include('web.particles.footer')
    @stack('scripts')

</body>

</html>