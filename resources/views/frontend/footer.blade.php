
<script src="{{url('public/frontend/js/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{url('public/frontend/js/owl-carousel.min.js')}}" type="text/javascript"></script>
<script src="{{url('public/frontend/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{url('public/frontend/js/wow.min.js')}}" type="text/javascript"></script>
<script type="text/javascript" src="{{url('public/frontend/js/slick.min.js')}}"></script>
<script type="text/javascript" src="{{url('public/frontend/js/gijgo.min.js')}}"></script>
<script type="text/javascript" src="{{url('public/commonFile/js/toastr.min.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<script type="text/javascript">

 
   $("#logout").click(function () {
       swal({
      title: "Are you sure?",
      //text: "Once deleted, you will not be able to recover this imaginary file!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
  })
          .then((willDelete) => {
    if (willDelete) {
      $.ajax({
            type: "GET",
            url:"{{route('frontend.logout')}}",
            contentType: false,
            cache: false,
            processData: false,
                        success: function(data) {
                            if (data.status === true) {
                              toastr.success(data.message);
                                if (data.redirect_url != '') {

                                   window.setTimeout(function(){location.replace(data.redirect_url)},500)
                                   ;
                                }
                                
                            } else {
                                toastr.error(data.message); 
                            }
                        },
                      
        });
   
  } 
});

    });

</script> 




<!-- footer -->

<!--<footer class="full nutriwala-footer">
  <div class="nutriwala-footer-top">
    <div class="container">
     
 
     
    </div>
  </div>
  <div class="nutriwala-copyright">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <p>&#169; Quennections 2022 . All Rights Reserved</p>
        </div>
        <div class="col-md-6">
          <div class="nutriwala-link">
            <ul>
              <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
              <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>-->


<style>
  .card-header.card-header-fixed {
    bottom: 4.5%;
  }
div.loader{
position: fixed;
display: block;
z-index: 1111;
left: 0;
top: 0;
height: 100%;
width: 100%;}

.loader img{
left: 0;
top: 0;
position: absolute;
right: 0;;
bottom:0;
margin:auto;    width: 150px;
    z-index: 1111;} 


div.loader:after{position:absolute;background:rgba(0, 0, 0, 0.6);content:'';width:100%;height:100%;top:0;left:0;}

</style>
<div class="loader" style="display:none;">
    <img src="{{url('images/loader.gif')}}" alt="Loader">
</div>



<!-- end-footer -->
