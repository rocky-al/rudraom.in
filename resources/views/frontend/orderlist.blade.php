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
          <div class="page-title-box d-flex justify-content-between mb-3">  
                <h4><i class="zmdi zmdi-view-list-alt zmdi-hc-fw"></i> Order Lists</h4>
               
          </div>
         
          <div class="customer-personal-details">
         <div class="page_row">

<div class="card border-0">
    <div class="card-body p-0">
        <div class="card-block table-border-style">
            
             <div class="row">
            <div class="col-sm-3 mb-2">
              <select class="form-control" name="name_search" id="name_search">
                <option value="">All User</option>
                <?php 
 
                foreach($users_data as $val){
                ?>
                <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                <?php } ?>
            </select>
            </div>

             <div class="col-sm-3 mb-2">
            <input type="name" class="form-control" name="item_search" id="item_search" placeholder="Search by item name">
            </div>
              <div class="col-sm-3 mb-2">
            <select id="status" class="form-control" name="status">
                <option value>All Status</option>
                <option value="0">Pending</option>
                <option value="1">Confirmed</option>
                <option value="2">Shipped</option>
                <option value="3">Delivered</option>
                <option value="4">Cancelled</option>
               <!--  <option value="5">In-Transit</option> -->
            </select>

        </div>
    </div>
                <div class="table-responsive">
                <table id="data_table" class="display" cellspacing="0" width="100%" >
                    <thead>
                        <tr>
                            <th>#</th>
                             <th> {{__('User Name')}} </th>
                            <th> {{__('Item Name')}} </th>
                            <th> {{__('Amount')}} </th>
                            <th> {{__('Quantity')}} </th>
                            <th> {{__('Status')}}</th>
                            <th> {{__('Created At')}}</th>
                            <th> {{__('Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            
        </div>
    </div>
</div>
       </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!--  edit popup
    =========================== -->
<div id="feedmodal" class="modal fade delivery-address-edit" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0">
      <div class="modal-body p-0">
          <button type="button" class="close text-white opacity-10 text-9 mr-sm-n4 mt-sm-n2 font-weight-300" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
          <div class="row no-gutters"> 
            <div class="col-lg-12 bg-white rounded">
              <h3 class="text-4 mb-2">Change Status</h3>
              <div class="container my-auto">
                <div class="row">
                  <div class="col-12 col-lg-12 mx-auto">
                    <div class="d-flex flex-column">
                      <form  class="form-border edit_form" method="post" >
                        
                       

                        
                         <input name="id" id="order_id" type="hidden" class="form-control border-2"  placeholder="Enter Description" >

                        <div class="form-group">
                         <label>Status <span class="text-danger">*</span> </label>
                          <!--  {!! Form::select('status', array('0' => 'Pending', '1' => 'Confirmed','2' => 'Shipped','3' => 'Delivered','5' => 'Rejected'), '',['class' =>'form-control  border-2 status']); !!} -->
                            <select id="status" class="form-control status" name="status">
                                <select>
                        </div>
                        <div class="shop-now-but">
                <button id="edit_submit" type="submit" class="btn btn-primary">Update</button>
              </div> 
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- popup edit End --> 


@include('frontend.footer')  
@include('frontend.copyright') 
<script>
   new WOW().init();
</script>

<!-- city-dropdowan-js -->

<script>
    function myFunction() {
        var x = document.getElementById("myDIV_pro");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>

<!-- End-city-dropdowan-js -->


<!-- category and cart right-open js -->

<script type="text/javascript">
  function cartopen() {
      document.getElementById("sitebar-cart").classList.add('open-cart');
  }

  function cartclose() {
      document.getElementById("sitebar-cart").classList.remove('open-cart');
  }

  function category_open() {
      document.getElementById("sitebar-category").classList.add('open-category');
  }

  function categoryclose() {
      document.getElementById("sitebar-category").classList.remove('open-category');
  }

</script>

<!-- End-category and cart right-open js -->

<!-- show-more-city-js -->
<script type="text/javascript">
     $(".show-morecity").click(function () {
        var type=$(this).attr("data-neexpend");
        console.log($(this).parent().closest("ul"))

        if($(this).parent().parent().find(".nurtiwala-city").hasClass("show-more-height")) {
            $(this).text("- Show Less "+type);
        } else {
            $(this).text("+ Expand more "+type);
        }

        $(this).parent().parent().find(".nurtiwala-city").toggleClass("show-more-height");
    });

</script>


<!-- End-show-more-city-js -->

<!-- sidemenu-js -->

<script type="text/javascript">
  $("#leftside-navigation .sub-menu > a").click(function(e) {
  $("#leftside-navigation ul ul").slideUp(), $(this).next().is(":visible") || $(this).next().slideDown(),
  e.stopPropagation()
})
</script>

<!-- End-sidemenu-js -->


<script src="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.js')}}"></script>
<script>
    $(document).ready(function() {
        var id="1";
        var table = $('#data_table').DataTable({
            responsive: true, 
            "order": [
                [0, "desc"]
            ], // order by desc 
            "bProcessing": true,
            "serverSide": true,
            "lengthMenu": [10,50,100,500],
             "sDom": '<"top" <"row" <"" <"">>>>tr<"bottom overflow-hidden" <"row" <"col-sm-4" l><"col-sm-4 text-center" i><"col-sm-4" p>>>',
     
            ajax: {
                url: "{{route('order.list') }}",
                 data: function (d) {
                    d.name_search = $('#name_search').val(); 
                    d.item_search = $('#item_search').val(); 
                    d.status=  $('#status').val();
                   
                },
                error: function() {
                    $.alert('something_went_wrong!');

                }
            },

            "aoColumns": [{
                    mData: 'id'
                },
                 {
                    mData: 'user_name'
                },

                {
                    mData: 'item_name'
                },
                 {
                    mData: 'price'
                },
                 {
                    mData: 'qnty'
                },

                 {
                    mData: 'status'
                },
                 {
                    mData: 'updated_at'
                },

                {
                    mData: 'actions'
                },
            ],
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [-1,-2,-3]
            }, ],
        });

       $('#name_search').on('change',function(){
           table.draw();
        });

        $('#status').on('change',function(){
           table.draw();
        });

        $('#item_search').keyup(function(){
            table.draw();
        });

    });
</script>

<script>


$(document).on('click', '.delete_button', function (e) {
      e.preventDefault();
      var id = $(this).data('id');
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
            type:'POST',
            url:'{{url("delete")}}/' +id,
             data:{
                  "_token": "{{ csrf_token() }}",
                            },
             success:function(data) {
          if (data.status === true) {
                toastr.success(data.message);
                 if($('#data_table').length>0){
                $('#data_table').DataTable().ajax.reload(); }
                }
                  }
             });
  } 
});
});

$('body').on('click', '#editCompany', function (event) {
    event.preventDefault();
    var id = $(this).data('id');
    $.get('order/' + id + '/edit', function (data) {

            if(data.data.order_status==0){
                  $(".status").append("<option value='1'>Confirmed</option><option value='4'>Cancelled</option>");
              }

            if(data.data.order_status==1){
                  $(".status").append("<option value='2' class='ship_order'>Shipped</option><option value='4' class='cancel_order' >Cancelled</option>");
                  $(".form-group").append("<div style='margin-top:5px;' class='order_track'><label>Tracking no <span class='text-danger'>*</span> </label><input id='tracking_no' class='form-control border-2' name='tracking_num' type='text' placeholder='Enter tracking no' required></div>");
              }


           /*if(data.data.order_status==2){
                  $(".status").append("<option value='5'>In-Transit</option><option value='4'>Cancelled</option>");
              }*/


            if(data.data.order_status==2){
                  $(".status").append("<option value='3'>Delivered</option>");
                  
              }
               
               
               /* <option value="2">Shipped</option>
                <option value="3">Delivered</option>
                */
           

        // $('.status').val(data.data.order_status);
         $('#order_id').val(data.data.id);
         
     })
});


$('body').on('click', '#edit_submit', function (event) {

   $(".edit_form").validate({
      rules: {

        tracking_num:{
              maxlength: 25
        }
          
        
      },
      messages: {
      },

      submitHandler: function (form) {
        

    event.preventDefault();
   
    var status = $(".status").val();
    var id=$("#order_id").val();
    var tracking_no=$("#tracking_no").val();
   
   
    $.ajax({
      url: "{{route('order.manage')}}",
      type: "POST",
      data: {
        status:status,
        id:id,
        tracking_no:tracking_no
      
      },
      dataType: 'json',
             beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true);
                            $('.loader').show();
                      
                        },
      success: function (data) {

          if (data.status === true) {
                           
                            $('#feedmodal').modal('hide');
                            $('.loader').hide();                           
                            toastr.success(data.message);
                            if($('#data_table').length>0){
                            $('#data_table').DataTable().ajax.reload(); }
                            $('button[type="submit"]').prop('disabled', false);
                          }

          else{
              $('.loader').hide();
              toastr.error(data.message);
              $('button[type="submit"]').prop('disabled', false);
          }
      }
  });
}
});

   });


$('#feedmodal').on("hidden.bs.modal", function() {
   $('.status').empty();
   $('.order_track').empty();
});



$(document).on('change', '.status', function (e) {
   
     var val = $(this).val();
       if(val==2){
        $(".order_track").show();
       }
        else if (val==4){
        $(".order_track").hide();
       }


    });





</script>


  </body>
</html>
