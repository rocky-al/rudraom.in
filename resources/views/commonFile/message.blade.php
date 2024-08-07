@if(Session::has('error'))
<script>
    toastr.error("{{ Session::get('error') }}", '', {
        "showMethod": "slideDown",
        "hideMethod": "slideUp",
        timeOut: 2000
    });
</script>
@endif

@if(Session::has('success'))
<script>
    toastr.success("{{ Session::get('success') }}", '', {
        "showMethod": "slideDown",
        "hideMethod": "slideUp",
        timeOut: 2000
    });
</script>
@endif
