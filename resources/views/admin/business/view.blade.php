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
                <td> Business Profile  </td>
                    <?php
                    if(isset($data->profile_img) && !empty($data->profile_img)){
                    ?>

                    <td style="border-bottom-width:0px;" >
                    <div class="table_imgs">
                   <?php 
                      echo '<a href="javascript:" onclick=imageZoom(\'uploads/business_profile\',\'' . $data->profile_img . '\')>';
                   ?>
                     <img src="{{url('uploads/business_profile').'/'.$data->profile_img}}" class="profile-image-icon" alt="profile image"> 
                    </a>
                    </div>
                    <?php } else { ?>
                        <td>No Image</td>
                        <?php     }   ?>
                        </td>
                 </tr>
                    <tr>
                        <td style=" width: 20%; "> Business Name  </td>
                        <td>{{$data->name ?? '-'}}</td>
                    </tr>

                    <tr>
                        <td> Business Category  </td>
                        <td>{{$category->name ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>Email </td>
                        <td>{{$data->email_address ?? '-'}} </td>
                    </tr>

                    <tr>
                        <td>Phone No </td>
                        <td>{!! $data->phone_no ?? '-' !!} </td>
                    </tr>


                    <tr>
                        <td>Status</td>
                        <td>
                            @if($data->status=="1")
                            {{"Approved"}}
                            @elseif($data->status=="2")
                            {{"Rejected"}}
                            @else
                            {{"Pending"}}
                            @endif </td>
                    </tr>

                     <tr>
                        <td> Address  </td>
                        <td>{{$data->address ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td> Location  </td>
                        <td>{{$data->location ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td>License No </td>
                        <td>{{$data->license_no ?? '-'}} </td>
                    </tr>

                    <tr>
                        <td>Registration Date</td>
                        <td>{!! date('M d Y ', strtotime($data->registration_date)) ?? '-' !!} </td>
                    </tr>

                     <tr>
                        <td>Business Description</td>
                        <td>{{$data->description ?? '-'}} </td>
                    </tr>
                      
                 

                    <tr>
                    <td>Photos  </td>
                    <td>
                    <?php 
                    if(count($businessImages) > 0){
                        foreach($businessImages as $val){   ?>

                        <div class="business_image">
                        <?php 
                      echo '<a href="javascript:" onclick=imageZoom(\'uploads/business_image\',\'' . $val->image . '\')>';
                   ?>
                            <img style="width:100%;" src="{{url('uploads/business_image').'/'.$val->image}}" class="image-icon" >
                        </a>
                        </div>    

                        <?php }

                    }else{
                        echo 'No Photos';
                    }
                    ?>
                    </td>

                    
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