@extends('layout.admin')
@section('content')
@push('css')
<link rel='stylesheet' href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}">
@endpush

<div class="d-lg-flex align-items-center mb-2 mt-2">
    <div class="position-relative">
        <h5 class="page_heading"> {{$title ?? ''}} </h5>
    </div>
    <div class="ms-auto"> {!! addAction(route($page.'.add') , 'Add New '. ucfirst($page)) !!}</div>
</div>
<hr class="page_row">
<div class="card">
    <!-- <div class="card-header">
       
    </div> -->
    <div class="card-body">
        <div class="table-responsive">
            <table id="data_table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th> {{__('Name')}} </th>
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
            "bProcessing": true,
            "serverSide": true,
            "pageLength": 50,

            ajax: {
                url: "{{ route('role.index') }}",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                error: function() {
                    alert("{{__('something_went_wrong')}}");
                }
            },

            "aoColumns": [{
                    mData: 'id'
                },

                {
                    mData: 'name'
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