@push('scripts')
<link rel="stylesheet" href="{{URL::asset('admin/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css')}}" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endpush

@php

use App\Models\City;
use App\Models\User;
use App\Models\BusinessType;
$city = City::where('status', Constant::ACTIVE)->pluck('name', 'id');

$q = User::whereHas(
'roles',
function ($q) {
$q->whereIn('name', ['team leader']);
}
);

$user_id = $q->pluck('referral_id', 'referral_id');

$business_type = BusinessType::where('status', Constant::ACTIVE)->pluck('business_type', 'id');



@endphp

<form class="row mt-2 mb-2 searching_form">
    <div class="col-md-2">
        <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date">
    </div>
    <div class="col-md-2">
        <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date">
    </div>

    @if(in_array(Route::currentRouteName(), ['business.index']))

    <div class="col mb-3">
            {!! Form::select('business_type', $business_type ?? [], $data->business_type ?? '', ['class' =>'form-control', 'id'=>'business_type' ,'placeholder' => 'Business Type']) !!}
        </div>
        @endif



    @if(in_array(Route::currentRouteName(), ['employee.index']))
    <div class="col md-2">
        {!! Form::select('perent_referral_id',$user_id, null, ['class' =>'form-control user_id_select_2', 'id'=>'user_id', 'placeholder' => 'Refer Id']) !!}
    </div>
    @endif


    @if(in_array(Route::currentRouteName(), ['tm.index', 'user.index', 'employee.index']))
    <div class="col md-2">
        {!! Form::select('city_id',$city, null, ['class' =>'form-control city_select_2', 'id'=>'city_id', 'placeholder' => 'Select City']) !!}
    </div>
    @endif

    <div class="col-md">
        <button class="btn btn-outline-primary" type="submit"> <i class="fadeIn animated bx bx-search-alt"></i> Search </button>
        <button class="btn btn-primary refresh_button"> <i class="fadeIn animated bx bx-refresh"></i> Refresh </button>
    </div>
</form>


@push('scripts')
<script src="{{URL::asset('admin/plugins/bootstrap-material-datetimepicker/js/moment.min.js')}}"></script>
<script src="{{URL::asset('admin/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js')}}"></script>
<script>
    $(function() {

        $('#start_date').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false,
        });

        $('#end_date').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false
        });


        // serching code
        $('.searching_form').on('submit', function(e) {
            e.preventDefault();
            $('#data_table').DataTable().ajax.reload();

        });

        // reset serching data code
        $(document).on('click', '.refresh_button', function(event) {
            event.preventDefault();
            $('.searching_form').trigger('reset');
            $('#data_table').DataTable().ajax.reload();
        });
    });
</script>
@endpush