<div class="modal-header">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-8 col-sm-12">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <td> User </td>
                        <td>{{$data->user->first_name ?? '-'}} {{$data->user->last_name ?? '-'}}({{$data->user->referral_id ?? ''}}) </td>
                    </tr>
                    <tr>
                        <td>Total Amount  </td>
                        <td>{{$data->total_amount ?? '-'}} </td>
                    </tr>


                    <tr>
                        <td>Discount(%) </td>
                        <td>{{$data->discount ?? '-'}} </td>
                    </tr>

                    <tr>
                        <td>Discount Amount </td>
                        <td>{{$data->discount_amount ?? '-'}} </td>
                    </tr>

                  <tr>
                        <td>Remaining Amount </td>
                        <td>{{$data->amount ?? '-'}} </td>
                    </tr>


                    <tr>
                        <td>created </td>
                        <td>{{$data->created_at ?? '-'}} </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close
    </button>
</div>