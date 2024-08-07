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

                    <tr>
                       <td>Updated  </td>
                        <td> {{date('M d Y ', strtotime($data->updated_at))}} </td>  
                        
                    </tr>

                    <tr>
                       <td>Profile Image  </td>
                    <?php 
                    if(isset($data->image) && !empty($data->image)){
                    ?>
                         <td style="border-bottom-width:0px;" >
                         <div class="table_imgs">
                   <?php 
                      echo '<a href="javascript:" onclick=imageZoom(\'uploads/user_profile\',\'' . $data->image . '\')>';
                   ?>
                         
                         <img style=" width: 200px;" src="{{url('uploads/user_profile').'/'.$data->image}}" class="image-icon" alt="profile image">  </td>  

                         </a>
                    </div>
                         <?php } else{
                            echo '<td>No Image</td>';
                         } ?>
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