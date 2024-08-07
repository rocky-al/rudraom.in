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
                  <div class="d-flex align-items-center justify-content-between">
                  <h4>  <a href="{{route('order.list')}}"><i class="zmdi zmdi-hc-fw"></i></a># {{$order_items->id}}</h4>
                    <div class="status-btn">
                  <?php 

                   if($order_items->order_status == 0) {
                $status = '<button type="button" class="btn btn-warning">Pending</button>';
                } 
                if ($order_items->order_status == 1) {
                $status = '<button type="button" class="btn btn-info">Confirmed</button>';
                } 
                if ($order_items->order_status == 2) {
                $status = '<button type="button" class="btn btn-info">Shipped</button>';
                } 
                if ($order_items->order_status == 3) {
                $status = '<button type="button" class="btn btn-success">Delivered</button>';
                } 
                 if ($order_items->order_status == 4) {
                $status = '<button type="button" class="btn btn-danger">Cancelled</button>';
                }
                 if ($order_items->order_status == 5) {
                $status = '<button type="button" class="btn btn-info">In-Tranist</button>';
                }



                  ?> 
                          <?php echo $status;?>
                        </div>
                  </div>
                </div>
                <div class="delivery-locations">
                  <div class="row">
                    <div class="col-md-8">
                      <div class="my-address-line d-flex">
                        <div class="my-address-icon">
                          <img src="https://ninehertz.orbitnapp.com/quennections/public/images/my-address-icon.png" alt="my-address-icon">
                        </div>
                        <div class="my-address-text">
                          <h4>{{isset($shipping_ads->apt_no) ? $shipping_ads->apt_no : ''}}</h4>
                          <p>{{isset($shipping_ads->address) ? $shipping_ads->address : ''}}</p>
                           <p>{{isset($city->name) ? $city->name : ''}},{{isset($city->city_code) ? $city->city_code : ''}}{{isset($shipping_ads->zip_code) ? ' ('.$shipping_ads->zip_code.')' : ''}} , {{isset($country->name) ? $country->name : ''}}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="order-summary">
                  <div class="order-summary-box">
                    <h4>Order Summary</h4>
                  </div>
                  <div class="cart-product-item">
                    <div class="row align-items-center">
                        <div class="col-6">
                          <div class="d-flex align-items-center">
                            <div class="thumb">
                            <a href="javascript:" onclick="imageZoom('uploads/item_image','{{$item_image->image}}')">
                                <img src="{{url('uploads/item_image/'.$item_image->image)}}"  class="img-fluid" alt="product">
                            </a>
                      
                            </div>
                            <div class="product-content">
                               <p> {{$order_items->item_name ?? '-'}} </p>
                              
                            </div>
                          </div>
                        </div>
                        <div class="col-6">
                            <div class="product-content product-content-end">
                                <div class="cart-total-prize"><i class="fa fa-dollar" aria-hidden="true"></i> {{$order_items->item_price ?? '0'}} * <span>{{$order_items->quantity}}</span></div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="order-summary-total">
                    <div class="cart-total">
                      <p class="total-price total-price-summary subtotal d-flex justify-content-between">
                          <span>Subtotal</span> 
                          <span><i class="fa fa-dollar" aria-hidden="true"></i> {{($order_items->item_price*$order_items->quantity) ?? '0'}}</span>
                      </p>
                      <p class="total-price total-price-summary  d-flex justify-content-between">
                          <span>Service Charges</span> 
                          <span><i class="fa fa-dollar" aria-hidden="true"></i> {{$order_items->shipping_charges ?? '0'}}</span>
                      </p>
                      <p class="total-price  d-flex justify-content-between">
                          <span>Total</span> 
                          <?php
                          $total_amt='0';
                          $total_amt=($order_items->item_price*$order_items->quantity)+$order_items->shipping_charges;
                           ?>
                          <span><i class="fa fa-dollar" aria-hidden="true"></i> {{$total_amt}}</span>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </section>
  </body>

@include('frontend.footer')
@include('frontend.copyright')    
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
