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
                        <td> Title  </td>
                        <td>{{$data->title ?? '-'}} {{$data->last_name ?? '-'}} </td>
                    </tr>
                    <tr>
                        <td>Slug </td>
                        <td>{{$data->slug ?? '-'}} </td>
                    </tr>

                    <td>Updated  </td>
                        <td> {{$data->created_at ?? '-'}} </td>

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