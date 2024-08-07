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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js
"></script>

  <style>
    .error{
      color: red;
      font-size: 12px;}
     h5{
    color: #333753;
    font-size: 14px;
    font-family: 'celiasmedium';
     }

  </style>
  <style>
    .btn-close {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em 0.25em;
    color: #000;
    border: 0;
    border-radius: 0.25rem;
    opacity: .5;
}
    </style>

  </head>
  <body>
   @include('frontend.header') 


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">View Image</h5>
        <button type="button" class="btn-close modal_close" data-bs-dismiss="modal" aria-label="Close">X</button>
      </div>
      <div class="modal-body">
      <div class="modal-body" id="replaceModal"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary modal_close" data-bs-dismiss="modal">Close</button>
   
       
      </div>
    </div>
  </div>
</div>

<section class="full my-account">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="my-account-box">
          <div class="page-title-box">
            <h4><i class="zmdi zmdi-hc-fw"></i>My Account</h4>
          </div>
          <div class="customer-name">
            <h5>Hi! <span>{{$busns_detail->name}}</span></h5>
          <!--   <h6>Customer</h6>  -->
          </div>
          <div class="customer-personal-details">

          

             <form id="register_form" class="form-border" method="post">
                               
                               <div class="row">
                                <div class="form-group col-md-6" >
                                 <label >Business Category </label>
                                  @foreach($category as $category_list)
                                   <h5>{{$category_list->name}}</h5>
                                  @endforeach
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business Name </label>
                                    <h5>{{$busns_detail->name}}</h5>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business address </label>
                                   <h5>{{$busns_detail->address}}</h5>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business Phone number </label>
                                   <h5>{{$busns_detail->phone_no}}</h5>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business Email id </label>
                                  <h5>{{$busns_detail->email_address}}</h5>
                                </div>
                                 <div class="form-group col-md-6">
                                   <label >Business License number </label>
                                  <h5>{{$busns_detail->license_no}}</h5>
                                </div>
                                <div class="form-group col-md-6">
                                   <label >Business Registration date </label>
                                  <h5>{{$busns_detail->registration_date}}</h5>
                                </div>
                            

                             <!--    <div class="form-group col-md-6" >
                                  <label >Business Profile Image </label>
                                 <?php
                    if(isset($data->profile_img) && !empty($data->profile_img)){
                    ?>

                    <h5 style="border-bottom-width:0px;" > <img src="{{url('uploads/business_profile').'/'.$data->profile_img}}" class="image-icon" alt="profile image"> 
                    <?php } else { ?>
                        <h5>No Image</h5>
                        <?php     }   ?>
                        </h5>
                                </div>-->
                                <div class="form-group col-md-6">
                                   <label >Business location</label>
                               <h5>{{$busns_detail->location}}</h5>
                                </div>

                                <div class="form-group col-md-12 mb-0">
                                   <label style="width:100%">Business Photo </label>
                                    
                                    
                                   <?php 
                        if(count($businessImages) > 0){
                            foreach($businessImages as $val){   ?>

                        <div class="business_image">
                            <a href="javascript:" onclick="imageZoom('uploads/business_image','{{$val->image}}')">
                            <img src="{{url('uploads/business_image').'/'.$val->image}}" class="image-icon" >
                             </a>
                        </div>    

                        <?php }


                    }else{
                        echo 'No Photos';
                    }
                    ?>
                    
                    </div>
                               

                           
                                 
                                </div>
                               
                                  <div class="col-md-12">
             <!--      <div class="shop-now-but">
                    <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#createmodal">Update</button>
                  </div> -->
                </div>
                             </form>


          </div>

       
        </div>
      </div>
    </div>
  </div>
</section>


@include('frontend.footer') 
 @include('frontend.copyright')  
<script> 
   function imageZoom(folderPath, image) {
        
      $(document).ready(function() {
        $(document).on('click', '.modal_close', function() {
          jQuery('#exampleModal').modal('hide')

        })
    });
    
      
        $('#replaceModal').html('<img src="http://demo.dev9server.com/usermtoag/Quennections/' + folderPath + '/' + image + '" alt="Profile Image" style="width: 100%;">');
        
        jQuery('#exampleModal').modal('show')
    }

</script>

  </body>
</html>
