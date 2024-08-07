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
                        <td> Name  </td>
                        <td>{{$data->name ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>Email </td>
                        <td>{{$data->email ?? '-'}} </td>
                    </tr>

                    <tr>
                        <td>Phone No </td>
                        <td>{!! $data->mobile ?? '-' !!} </td>
                    </tr>


                    <tr>
                        <td>Status</td>
                        <td>
                            @if($data->status=="1")
                            {{"Active"}}
                            @else
                            {{"Inactive"}}
                            @endif </td>
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