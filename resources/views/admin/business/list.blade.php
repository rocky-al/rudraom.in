@extends('layout.admin')
@section('content')
@push('css')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />
 

@endpush

<div class="d-flex justify-content-between">
<h5 class="page_heading">{{__($title ?? '-')}}  </h5>

<!-- <button type="button" class="btn btn-outline-primary rounded-0 model_open" url="{{route('users.form')}}"> <i class="bx bxs-plus-square"> </i> {{__('Add User')}} </button> -->

</div>
<hr class="page_row">
<div class="card">
    <div class="card-body">
        <div class="card-block table-border-style">
        <div class="">
            
            <table id="data_table" class="display  " cellspacing="0" width="100%" >

            <div class="row mb-2">

            <div class="col-sm-2">
            <label>Business Name</label>
            <input type="name" class="form-control" name="name_search" id="name_search" placeholder="Search by business">
            </div>

            <div class="col-sm-2">
            <label>Email</label>
            <input type="name" class="form-control" name="email_search" id="email_search" placeholder="Search by email">
            </div>

            <div class="col-sm-2">
            <label>Phone No</label>
            <input type="name" class="form-control" name="phone_search" id="phone_search" placeholder="Search by phone no">
            </div>

            <div class="col-sm-2">
            <label>Licence No</label>
            <input type="name" class="form-control" name="licence_search" id="licence_search" placeholder="Search by Licence no">
            </div>

            
            <div class="col-sm-2">
            <label>Category</label>
            <select class="form-control" name="category_search" id="category_search">
                <option value="">All Category</option>
                <?php 
 
                foreach($category_data as $val){
                ?>
                <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                <?php } ?>
            </select>
            </div>

            <div class="col-sm-2">
            <label>Status</label>
            <select class="form-control" name="status_search" id="status_search">
                <option value="">All Status</option>
                <option value="0">Pending</option>
                <option value="1">Approved</option>
                <option value="2">Rejected</option>
            </select>
            </div>


            </div>

                <thead>
                    <tr>
                        <th>#</th>
                        <th> {{__('Business Name')}} </th>
                        <th> {{__('Category Name')}} </th>
                        <th> {{__('Profile Image')}} </th>
                        <th> {{__('Email')}} </th>
                        <th> {{__('Phone No.')}} </th>
                        <th> {{__('Licence No')}} </th>
                        <th> {{__('Registration Date')}} </th>
                        <th> {{__('Is Active')}} </th>
                        <th> {{__('Status')}} </th>                    
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
@endsection

@push('scripts')
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
            "sDom": '<"top" <"row" <"" <"">>>>tr<"bottom" <"row" <"col-sm-4" l><"col-sm-4 text-center" i><"col-sm-4" p>>>',
     
            ajax: {
                url: "{{ route($page.'.list') }}",
                data: function (d) {
                    d.name_search = $('#name_search').val(); 
                    d.email_search = $('#email_search').val(); 
                    d.status_search = $('#status_search').val();     
                    d.phone_search = $('#phone_search').val();  
                    d.licence_search = $('#licence_search').val();  
                    d.category_search = $('#category_search').val();  
                                  
                },
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
                    mData: 'category_name'
                },
                 {
                    mData: 'profile_img'
                },
                {
                    mData: 'email'
                },

                {
                    mData: 'mobile'
                },

                {
                    mData: 'license_no'
                },
               

                {
                    mData: 'registration_date'
                },

                {
                    mData: 'is_active'
                },

                {
                    mData: 'status'
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
        $('#name_search').keyup(function(){
            table.draw();
        });
        $('#status_search').on('change',function(){
            table.draw();
        });  
        $("#email_search").keyup(function(){
            table.draw();
        });
        $("#phone_search").keyup(function(){
            table.draw();
        });
        $("#licence_search").keyup(function(){
            table.draw();
        });
        $('#category_search').on('change',function(){
            table.draw();
        }); 

        // show image in modal after 1st modal will not block

$('#exampleModal').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
          });
        

    });
</script>
@endpush
