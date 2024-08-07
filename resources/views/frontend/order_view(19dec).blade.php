<!doctype html>
<html lang="en">
 <head>
  <title>@yield('title','') | Quennctions</title>
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
     @include('frontend.header') 
<section class="full my-account">
  <div class="container">
    <div class="row">
    <div class="col-md-12">
        <div class="my-account-box">
          <div class="page-title-box">
            <h4>
                <a href="{{route('order.list')}}"><i class="zmdi zmdi-hc-fw"></i></a> Order Details</h4>
          </div>
          <div class="delivery-date-time">
            <div class="row">
              <div class="col-md-8">
                <div class="delivery-date-left">
                  <h5>Total Item :  <span>{{$total_items}}</span></h5>
                 <!--  <p>Delivery Timing <span>10 May</span>, <span> 3.00PM - 6.00PM</span></p> -->
                </div>
              </div>
            <!--   <div class="col-md-4">
                <div class="delivery-status-btn">
                  <h6>Delivered</h6>
                </div>
              </div> -->
            </div>
          </div>
         <!--  <div class="payment-type">
            <div class="payment-type-box">
              <div class="row ">
                <div class="col-md-8">
                  <p>Your order payment <i class="fa fa-inr" aria-hidden="true"></i> <span>46.50</span></p>
                </div>
                <div class="col-md-4">
                  <div class="payment-type-right">
                    <h6>COD</h6>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <div class="delivery-locations">
            <div class="row">
              <div class="col-md-8">
                <div class="my-address-line d-flex">
                  <div class="my-address-icon">
                   <!--  <img src="images/my-address-icon.png" alt="my-address-icon"> -->
                   Shipping Address :
                  </div>
                  <div class="my-address-text">
                    <h4>House No.  345A</h4>
                    <p>Aashirwad Farms, Village Gaushala Khedi Shikohpur,  Bhagwanpur, 247662, Uttarakhand</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="order-summary">
            <div class="order-summary-box">
              <h4>Order Summary</h4>
            </div>

            <?php foreach($order_items as $items){?>
            <div class="cart-product-item">
              <div class="row align-items-center">
                  <div class="col-2 pr-0">
                      <div class="thumb">
                          <!-- <a href="#"><img src="images/gobi-img.jpg" alt="products"></a> -->
                           <a href="#"><img src="https://ninehertz.orbitnapp.com/quennections/uploads/logo/1669705985_Quennections_logo.png" alt="web-logo" width="50px"></a>
                      </div>
                  </div>
                  <div class="col-6 pl-0">
                      <div class="product-content">
                          <a href="#" class="product-title">{{$items->item_name}}</a>
                          <div class="product-cart-info">
                             Qty : <span>{{$items->quantity}}</span>
                          </div> 
                      </div>
                  </div>
                  <div class="col-4">
                      <div class="product-content product-content-end">
                         <!--  <div class="cart-total-prize"><i class="fa fa-dollar" aria-hidden="true"></i> {{$items->item_price}} * <span>{{$items->quantity}}</span></div> -->
                           <div class="cart-total-prize"><i class="fa fa-dollar" aria-hidden="true"></i> {{$items->item_price}} </div>
                      </div>
                  </div>
              </div>
            </div>

        <?php } ?> 


            <div class="order-summary-total">
              <div class="cart-total">
                <p class="total-price total-price-summary subtotal d-flex justify-content-between">
                    <span>Subtotal</span> 
                    <span><i class="fa fa-dollar" aria-hidden="true"></i> {{$total_price}}</span>
                </p>
                <p class="total-price total-price-summary  d-flex justify-content-between">
                    <span>Saving</span> 
                    <span><i class="fa fa-dollar" aria-hidden="true"></i>0.00</span>
                </p>
                <p class="total-price  d-flex justify-content-between">
                    <span>Total</span> 
                    <span><i class="fa fa-dollar" aria-hidden="true"></i> {{$total_price}}</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

  </div>
</section>
  </body>

@include('frontend.footer')  
  <script type="text/javascript">
   $("#logout").click(function () {
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
    });

</script> 

</html>