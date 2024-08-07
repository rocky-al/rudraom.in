@extends('layout.admin')
@section('content')
@push('css')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />

@endpush

<div class="d-flex justify-content-between">
<h5 class="page_heading">{{__($title ?? '-')}}  </h5>

<!-- <button type="button" class="btn btn-outline-primary rounded-0 model_open" url="{{route('category.form')}}"> <i class="bx bxs-plus-square"> </i> {{__('Add Category')}} </button> -->

</div>
<hr class="page_row">
<div class="card">
    <div class="card-body">
        <div class="card-block table-border-style">

              <div class="row mb-2">

                   <div class="col-sm-3 mb-2">
                        <input class="form-control" name="order_id" id="order_id" placeholder="Enter order id">
                     </div>
              
                    <div class="col-sm-3 mb-2">
                          <select class="form-control" name="name_search" id="name_search"  >
                            <option value="">All User</option>
                            <?php 
             
                            foreach($users_data as $val){
                            ?>
                            <option value="<?php echo $val->id; ?>"><?php echo $val->name; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    

            </div>

            
            <table id="data_table" class="display" cellspacing="0" width="100%" class="table-responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th> {{__('User Name')}} </th>
                        <th> {{__('Item Name')}} </th>
                        <th> {{__('Message')}} </th>
                        <th> {{__('Created Date')}}</th>
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
                    d.username_search = $('#name_search').val(); 
                    d.order_id = $('#order_id').val();                  
                },
                error: function() {
                    $.alert('something_went_wrong!');

                }
            },

            "aoColumns": [{
                    mData: 'id'
                },

                {
                    mData: 'order_id'
                },


                {
                    mData: 'name'
                },

                {
                    mData: 'item_name'
                },

                {
                    mData: 'message'
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

        $('#order_id').keyup(function(){
           table.draw();
         });

        $('#name_search').on('change',function(){
            table.draw();
        });
        // $('#status_search').on('change',function(){
        //     table.draw();
        // });
    });
</script>
@endpush