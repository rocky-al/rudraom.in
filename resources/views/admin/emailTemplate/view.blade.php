<div class="modal-header">
    <h5 class="modal-title" id="backDropModalTitle"> Email Template </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">



    <div class="row">
        <div class="col-md-12 col-sm-12">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <td> Title  </td>
                        <td>{{$data->title ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>Subject </td>
                        <td>{{$data->subject ?? '-'}} </td>
                    </tr>

                    <td>Updated  </td>
                        <td>{!! date('M d Y ', strtotime($data->created_at)) ?? '-' !!}</td>


                    <tr>
                        <td>Description </td>
                        <td>{!! $data->description ?? '-' !!} </td>
                    </tr>
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