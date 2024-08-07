<div class="modal-header">
    <h5 class="modal-title" id="backDropModalTitle"> {{__($title ?? '-')}} </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">



    <div class="row">
        <div class="col-md-12 col-sm-12">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <td> Order ID</td>
                        <td>{{$data->order_id ?? '-'}}</td>
                    </tr>

                    <tr>
                        <td> User Name  </td>
                        <td>{{$data->name ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>Item Name </td>
                        <td>{{$data->item_name ?? '-'}} </td>
                    </tr>

                    <tr>
                        <td> Message</td>
                        <td>{!! $data->message ?? '-' !!} </td>
                    </tr>

                    <td>Updated  </td>
                        <td> {{$data->updated_at ?? '-'}} </td>

                    
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