@extends('layout.admin')
@section('content')

@push('css')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />
@endpush

<div class="d-flex justify-content-between">
    <h5 class="page_heading"> Payment Request </h5>
    @can('Payment Request Add')
    <button type="button" class="btn btn-outline-primary rounded-0 model_open" url="{{route($page.'.form')}}"> <i class="bx bxs-plus-square"> </i> {{__('New Request')}} </button>
    @endcan
</div>
<hr class="page_row">

<div class="card">
    <div class="card-header">
        @include('admin.particles.filter')
    </div>
    <div class="card-body">
        <div class="card-block table-border-style">
            <table id="data_table" class="display" cellspacing="0" width="100%" class="table-responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th> Referal Id </th>
                        <th> User </th>
                        <th> Role </th>
                        <th> {{__('Amount')}} </th>
                        <th> {{__('Created')}}</th>
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
        var table = $('#data_table').DataTable({
            responsive: true, 
            "order": [
                [0, "desc"]
            ], // order by desc 
            "bProcessing": true,
            "serverSide": true,
            "lengthMenu": [10, 50, 100, 500],
            ajax: {
                url: "{{ route($page.'.index') }}",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.business_type = $('#business_type').val();
                },

                error: function() {
                    $.alert('something_went_wrong!');
                }
            },

            "aoColumns": [{
                    mData: 'id'
                },

               
                {
                    mData: 'referral_id'
                },
                {
                    mData: 'user'
                },

                
                {
                    mData: 'role'
                },



                {
                    mData: 'amount'
                },

              
                {
                    mData: 'created_at'
                },

                {
                    mData: 'actions'
                },
            ],
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [-1]
            }, ],
        });

    });
</script>
@endpush