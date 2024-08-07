@extends('layout.admin')
@section('content')

@push('css')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.css')}}" />
@endpush

<div class="d-flex justify-content-between">
    <h5 class="page_heading">{{__($title ?? '-')}}  </h5>
    <button type="button" class="btn btn-outline-primary rounded-0 model_open" url="{{route('permission.form')}}"> <i class="bx bxs-plus-square"> </i> {{__('Add Permission')}} </button>
</div>
<hr class="page_row">


<div class="card">
    <div class="card-body">
        <div class="card-block table-border-style">
            <table id="data_table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th> {{__('Name')}} </th>
                        <th> {{__('Created')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<!-- Page-body end -->
@endsection

@push('scripts')
@push('css')
<script src="{{URL::asset('admin/plugins/datatable/jquery.dataTables.min.js')}}"></script>
<script>
    $(document).ready(function() {
        var table = $('#data_table').DataTable({
            responsive: true, 
            "bProcessing": true,
            "serverSide": true,
            "lengthMenu": [10,50,100,500],
            ajax: {
                url: "{{ route('permission.index') }}",
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
            ],
            "aoColumnDefs": [{
                "bSortable": false
            }, ],
        });

    });
</script>
@endpush