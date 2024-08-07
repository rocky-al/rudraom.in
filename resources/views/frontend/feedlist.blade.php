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
    font-size: 12px;
  }
 

#exampleModal{
z-index: 111111;
}

    </style>

 
  </head>
  <body>
     @include('frontend.header') 
<section class="full my-account">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="my-account-box">
          <div class="page-title-box page-title-boxNew d-flex justify-content-between mb-3">  
                <h4><i class="zmdi zmdi-view-list-alt zmdi-hc-fw"></i> Feeds List</h4>
                <a href="javascript:" id="" data-toggle="modal" data-target="#feedmodaladd" class="btn btn-primary btn-sm d-flex align-items-center" >Add Feed</a>
          </div>
         
          <div class="customer-personal-details">
         <div class="page_row">

<div class="card border-0">
    <div class="card-body p-0">
        <div class="card-block table-border-style">
            <div class="table-responsive">
              <table id="data_table" class="display" cellspacing="0" width="100%" class="table-responsive">

                  <thead>
                      <tr>
                          <th>#</th>
                          <th> {{__('Name')}} </th>
                          <th> {{__('Price')}} </th>
                          <th> {{__('Status')}} </th>
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
  </div>
   </div>
</section>

<!--  edit popup
    =========================== -->
<div id="feedmodal" class="modal fade delivery-address-edit" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0">
      <div class="modal-body p-0">
          <button type="button" class="close text-white opacity-10 text-9 mr-sm-n4 mt-sm-n2 font-weight-300 close_load" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
          <div class="row no-gutters"> 
            <div class="col-lg-12 bg-white rounded">
              <h3 class="text-4 mb-2">Update Feed</h3>
              <div class="container my-auto">
                <div class="row">
                  <div class="col-12 col-lg-12 mx-auto">
                    <div class="d-flex flex-column">
                      <form  class="form-border edit_form" method="post" >
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Name <span class="text-danger">*</span> </label>
                              <input id="name" name="name" type="text" class="form-control border-2"  placeholder="Enter Name" >
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Price <span class="text-danger">*</span> </label>
                              <input name="price" id="price" type="text" class="form-control border-2"  placeholder="Enter Price" >
                            </div>
                          </div>
                        </div>

                         <div class="form-group">
                          <label>Description <span class="text-danger">*</span> </label>
                          <textarea id="description" name="description" class="form-control border-2" placeholder="Enter Description" rows="4" cols="50"></textarea>
                         <!--  <input name="description" id="description" type="taxt" class="form-control border-2"  placeholder="Enter Description" > -->
                        </div>
                         <input name="id" id="item_id" type="hidden" class="form-control border-2"  placeholder="Enter Description" >

                        <div class="form-group">
                         <label>Status <span class="text-danger">*</span> </label>
                           {!! Form::select('status', array('0' => 'Inactive', '1' => 'Active'), '',['class' =>'form-control  border-2 status']); !!}
                        </div>

                           <div class="form-group">
                                   <label >Product Photo <span class="text-danger">*</span></label>
                                  <input  type="file" class="form-control border-2"  placeholder="Product Photo" name="photos[]" accept="image/png, image/jpg, image/jpeg"  id="photos"  multiple  >
                                   <small  style="position: relative;"> &nbsp;(Only formats are allowed: jpeg, jpg, png and Maximum: 3 photos)</small>
                          </div> 
                         <div class="images_show">
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



<div id="feedmodaladd" class="modal fade delivery-address-edit" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0">
      <div class="modal-body p-0">
          <button type="button" class="close text-white opacity-10 text-9 mr-sm-n4 mt-sm-n2 font-weight-300" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
          <div class="row no-gutters"> 
            <div class="col-lg-12 bg-white rounded">
              <h3 class="text-4 mb-2">Add Feed</h3>
              <div class="container my-auto">
                <div class="row">
                  <div class="col-12 col-lg-12 mx-auto">
                    <div class="d-flex flex-column">
                      <form  class="form-border add_form" method="post" >
                        <div class="row">
                          <div class="col-md-6">
                        <div class="form-group">
                          <label>Name <span class="text-danger">*</span> </label>
                          <input  name="name" type="text" class="form-control border-2"  placeholder="Enter Name" >
                         
                        </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                          <label>Price <span class="text-danger">*</span> </label>
                          <input name="price"  type="text" class="form-control border-2"  placeholder="Enter Price" >
                        </div></div>
                      </div>

                         <div class="form-group">
                          <label>Description <span class="text-danger">*</span> </label>
                            <textarea  name="description" class="form-control border-2" placeholder="Enter Description" rows="4" cols="50"></textarea>

                       
                        </div>
                         <input name="id" id="item_id1" type="hidden" class="form-control border-2"  placeholder="Enter Description" >

                        <div class="form-group">
                         <label>Status <span class="text-danger">*</span> </label>
                           {!! Form::select('status', array('0' => 'Inactive', '1' => 'Active'), '1',['class' =>'form-control  border-2 ']); !!}
                        </div>
                         <div class="form-group">
                                   <label >Product Photo <span class="text-danger">*</span></label>
                                  <input  type="file" class="form-control border-2"  placeholder="Product Photo" name="photos[]" accept="image/png, image/jpg, image/jpeg"  multiple  required>
                                   <small style="position: relative;"> &nbsp;(Only formats are allowed: jpeg, jpg, png and Maximum: 3 photos)</small>
                          </div>

                          <div class="shop-now-but">
                <button id="add_submit" type="submit" class="btn btn-primary add_feed_product">Submit</button>
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



@include('frontend.footer')  
@include('frontend.copyright') 


<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

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
     
            ajax: {
                url: "{{route('feed.list') }}",
                data : { status : $('.searchEmail').val() },
                error: function() {
                    $.alert('something_went_wrong!');

                }
            },

            "aoColumns": [{
                    mData: 'id'
                },

                {
                    mData: 'name'
                },
                 {
                    mData: 'price'
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

          $(".searchEmail").keyup(function(){
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
    $.get('feed/' + id + '/edit', function (data) {
      
            for (var i = 0; i < data.item_images.length; i++) {

          // const element = '  <img src="' + data.item_images[i].image + '">'
          //document.querySelector('.image-area').innerHTML += element;

              const element = '<div class="image_view" id="img_'+data.item_images[i].id+'"> <div class="business_image"> <img  onclick=imageZoom("uploads/item_image","'+data.item_images[i].image+'") src="{{url('uploads/item_image')}}/'+ data.item_images[i].image + '"> </div><a data-id="'+data.item_images[i].id+'" class="remove-image" href="#" style="display: flex;">&#215;</a></div>'
          document.querySelector('.images_show').innerHTML += element;


}

         //$('#userCrudModal').html("Edit category");
         //$('#submit').val("Edit category");
         $('#feedmodal').modal('show');
         $('#item_id').val(data.data.id);
         $('#price').val(data.data.item_price);
         $('#name').val(data.data.item_name);
         $('.status').val(data.data.status);
         $('#description').val(data.data.item_description);
     })
});

$.validator.addMethod('positiveNumber',
    function (value) { 
        return Number(value) > 0;
    }, 'Enter a positive number.');

$('body').on('click', '#edit_submit', function (event) {

   $(".edit_form").validate({
      rules: {
          name: {
              required: true,
              maxlength:80
          } ,
          price: {
              required: true,
              number:true,
              positiveNumber:true,
              maxlength:6
              
          } ,
          description:{
          required: true,
          }
      },
      messages: {

        price: {
      
            maxlength:"Please enter no more than 6 digits.",
          } 
        
      },

      submitHandler: function (form) {

    event.preventDefault();
    var formData = new FormData($(".edit_form")[0]);
   
    $.ajax({
      url: 'manage',
      type: "POST",
      type: "POST",
      enctype: 'multipart/form-data',
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      dataType: 'json',
          beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true);
                            $('.loader').show();
                      
                        },
      success: function (data) {

          if (data.status === true) {
                           
                            $('#feedmodal').modal('hide');
                            toastr.success(data.message);
                            $('.loader').hide();
                              $('button[type="submit"]').prop('disabled', false);
                             if($('#data_table').length>0){
                            $('#data_table').DataTable().ajax.reload(); }
                            // setTimeout(location.reload.bind(location), 1000);
                            $('.images_show').empty();
                            $('#photos').val('');
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


$(document).on('click', '.remove-image', function (e) {
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
            url:'{{url("removeimage")}}/' +id,
             data:{
                  "_token": "{{ csrf_token() }}",
                            },
             success:function(data) {
          if (data.status === true) {
                //$('#feedmodal').modal('hide');
                  $('#img_'+data.id+'').remove();
                toastr.success(data.message);
                //setTimeout(location.reload.bind(location), 1000);

           
                }
                  }
             });
  } 
});
});




$('body').on('click', '.add_feed_product', function (event) {

  $(".add_form").validate({
      rules: {
          name: {
              required: true,
              maxlength:80
          } ,
          price: {
              required: true,
              number:true,
              positiveNumber:true,
              maxlength:6
             
          } ,
          description:{
          required: true,
          }
      },
      messages: {

         price: {
      
            maxlength:"Please enter no more than 6 digits.",
          } 
        
      },
     
 submitHandler: function (form) {
    event.preventDefault();
    var formData = new FormData($(".add_form")[0]);
    
    $.ajax({
      url: 'manage',
      type: "POST",
      enctype: 'multipart/form-data',
      data: formData,
      contentType: false,
      cache: false,
      processData: false,
      dataType: 'json',
       beforeSend: function(){
                            $('button[type="submit"]').prop('disabled', true);
                            $('.loader').show();
                      
                        },
      success: function (data) {

          if (data.status === true) {
                           
                            $('#feedmodaladd').modal('hide');
                             $('.loader').hide();
                             $('button[type="submit"]').prop('disabled', false);
                            toastr.success(data.message);
                            $(".add_form")[0].reset();
                             if($('#data_table').length>0){
                            $('#data_table').DataTable().ajax.reload(); }
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

$('.close_load').click(function() {
    //location.reload();
       //event.preventDefault();
   $('.images_show').empty();

});


   /* $("#feedmodal").modal({
        show: false,
        backdrop: 'static'
    });*/

$('#feedmodal').on("hidden.bs.modal", function() {
   $('.images_show').empty();
});

// for edit time feed show image  after feedmodal will not block

$('#exampleModal').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
          });

</script>
  </body>
</html>
