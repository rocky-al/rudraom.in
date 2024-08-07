@extends('layout.admin')
@section('content')
@push('css')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />

@endpush

<div class="d-flex justify-content-between">
<h5 class="page_heading">{{__($title ?? '-')}}  </h5>

<button type="button" class="btn btn-primary rounded-0 model_open" url="{{route('category.form')}}"> <i class="bx bxs-plus-square"> </i> {{__('Add Category')}} </button>

</div>
<hr class="page_row">
<div class="card">
    <div class="card-body">
        <div class="card-block table-border-style">
            
            <table id="data_table" class="display" cellspacing="0" width="100%" class="table-responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th> {{__('Name')}} </th>
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
     
            ajax: {
                url: "{{ route($page.'.list') }}",
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
@endpush
