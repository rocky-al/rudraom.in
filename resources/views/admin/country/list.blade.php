@extends('layout.admin')
@section('content')
@push('css')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />

@endpush

<div class="d-flex justify-content-between">
<h5 class="page_heading">{{__($title ?? '-')}}  </h5>

<button type="button" class="btn btn-outline-primary rounded-0 model_open" url="{{route('country.form')}}"> <i class="bx bxs-plus-square"> </i> {{__('Add Country')}} </button>

</div>
<hr class="page_row">
<div class="card">
    <div class="card-body">
        <div class="card-block table-border-style">

              <div class="row mb-2">

            <div class="col-sm-4">
            <label>Country Name</label>
            <input type="name" class="form-control" name="name_search" id="name_search" placeholder="Search by name">
            </div>
           
            <div class="col-sm-2">
            <label>Status</label>
            <select class="form-control" name="status_search" id="status_search">
                <option value="">All Status</option>
                <option value="0">Inactive</option>
                <option value="1">Active</option>
            </select>
            </div>

        <!--      <div class="col-sm-2">
            <label>Created Date</label>
            <input type="date" class="form-control" name="date_search" id="date_search" placeholder="Search by name">
            </div> -->



            </div>

            
            <table id="data_table" class="display" cellspacing="0" width="100%" class="table-responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th> {{__('Name')}} </th>
                        <th> {{__('Status')}} </th>
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
                    d.name_search = $('#name_search').val(); 
                    d.status_search = $('#status_search').val();    
                    d.date = $('#date_search').val();  
                                  
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

         $('#name_search').keyup(function(){
            table.draw();
        });
        $('#date_search').on('change',function(){
            table.draw();
        });
        $('#status_search').on('change',function(){
            table.draw();
        });
    });
</script>
@endpush