<?php
namespace App\Http\Controllers;
use App\Constants\Constant;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Jobs\OTP as JobsOTP;
use App\Models\Otp;
use App\Models\User;
use App\Models\Business_item;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\UserAuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
//use Spatie\Permission\Models\Permission;

class FeedController extends Controller
{

     public function __construct(Request $request)
    {
        $this->model = new Business_item();
        $this->sortableColumns = ['id','item_name','item_price'];
    }
    
  
     public function feed_list(Request $request)
    {
        if ($request->session()->missing('user')) {
           return redirect('');
           }
           
           if ($request->ajax()) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $sortableColumns = $this->sortableColumns;

            $start_date = ($request->get('start_date')) ? date('Y-m-d 00:00:01', strtotime($request->get('start_date'))) : date('Y-m-d 00:00:01', strtotime('-1 year', strtotime(date('Y-m-d'))));
            $end_date = ($request->get('end_date')) ? date('Y-m-d 23:59:59', strtotime($request->get('end_date'))) : date('Y-m-d 23:59:59');
            $value = $request->session()->get('user');
            //print_r($value);dd();
            $totaldata = $this->getData($search, $sortableColumns[$orderby], $order, $value->id);

            $totaldata = $totaldata->count();
            $response = $this->getData($search, $sortableColumns[$orderby], $order,$value->id);
            $response = $response->offset($start)->limit($limit)->orderBy('id', 'desc')->get();
            if (!$response) {
                $data = [];
                $paging = [];
            } else {
                $data = $response;
                $paging = $response;
            }

            $datas = [];
           // print_r($data);die;
            $i = 1;
             //print_r($data);dd();
            foreach ($data as $value) {
                $img='<img src="'.url("uploads/business_image/".$value->profile_img).'"class="image-icon" alt="profile image" >';
                $row['id'] = $start + $i;
                $row['name'] = ucfirst($value->item_name);
                $row['price'] = '$'.ucfirst($value->item_price);        
                if ($value->status == 1) {
                $status = '<button type="button" class="btn btn-success">Active</button>';
                } 
                if($value->status == 0) {
                $status = '<button type="button" class="btn btn-danger">Inactive</button>';
                } 
               

                $row['status']=$status;

                //$row['status'] = statusAction($value->status,$value->id,array(),'statusAction',$this->page . '.status');
                $row['updated_at'] = date('M d Y ', strtotime($value->created_at));

                $edit = '';
               
                    $edit = editButton('feed.form', ['id' => $value->id]);
               

                $view = '';
                //if(Auth::Business()->can('View Email Template')){
                    //$view = viewButton($this->page . '.view', ['id' => $value->id]);
                //}
                 $delete='';
                //$delete=deleteButton($this->page . '.delete', ['id' => $value->id]);

                          // comment for custom button

               //$edit = '<a class="model_open"  type="button" url=""> Edit </a>';
                 //$edit='<a href="javascript:" data-toggle="modal" data-target="#deliveryaddressedit"><i class="zmdi zmdi-hc-fw"></i></a>';

                  $edit='<a href="" id="editCompany" data-toggle="modal" data-target="#feedmodal" data-id="'.$value->id.'"><i class="zmdi zmdi-edit zmdi-hc-fw text-success"></i></a>';

                    //$delete='<a href="" id="" data-toggle="modal" data-target="#delete" data-id="'.$value->id.'">Delete</a>';
                     $delete='<a href="" class="delete_button" data-id="'.$value->id.'"><i class="zmdi zmdi-delete zmdi-hc-fw text-danger""></i></a>';
                    $row['actions'] =$edit.$delete;

                //$row['actions'] = createButton($edit);
                $datas[] = $row;
                $i++;
                unset($u);
            }
            $return = [
                "draw" => intval($draw),
                "recordsFiltered" => intval($totaldata),
                "recordsTotal" => intval($totaldata),
                "data" => $datas,
            ];
            return $return;
        }


        $data = ['title' => ucfirst('Business List')];
       // return view('admin.' . $this->page . '.list', $data);
        
       return view('frontend/feedlist',$data);
    }

        public function update($id)
    {
        $category = Business_item::find($id);
        $item_images= DB::table('business_item_images')->select('image','id')->where('item_id',$id)->get();
       
        return response()->json([
          'data' => $category,
          'item_images'=>$item_images
        ]);
    }

        public function delete($id)
    {
       $data = Business_item::where('id', $id)->first();
       $data->is_delete = '1';
       $data->save();
        return response()->json(['status' => true, 'message' => ' Record has been deleted successfully']);
    }

         public function removeimage($id)
    {
       $data = DB::table('business_item_images')->where('id', $id)->delete();
      
        return response()->json(['status' => true, 'message' => ' Image has been deleted successfully','id'=>$id]);
    }

        public function manage(Request $request)
    {
        
        $value = $request->session()->get('user');
        $id = $request->get('id');
        if(isset($id) && !empty($id)){
            $data = Business_item::where('id', $request->id)->first();
            $item_images=DB::table('business_item_images')->where('item_id', $request->id)->count();
            $photos_count=0;
             if($request->hasFile('photos')){
            $photos = $request->file('photos');
            $photos_count = count($photos);
        }
            $total=$photos_count+$item_images;
             if($total==0){
                 return response()->json(['status' =>false, 'message' => 'Product photos field is required']);
            }

             if($total >3){
                 return response()->json(['status' =>false, 'message' => 'Maximum three product photos are allowed']);
            }

             $validator = Validator::make($request->all(), [
                'photos.*' => 'max:10200',
               ]);
             if ($validator->fails()) { 
                  return response()->json(['status' => false, 'message' =>'Maximum 10 MB size of Product Photo are allowed'], 200);
              }

        }
        else{
            $photos = $request->file('photos');
            $photos_count = count($photos);
            if($photos_count >3){
                 return response()->json(['status' =>false, 'message' => 'Maximum three product photos are allowed']);
            }
             $validator = Validator::make($request->all(), [
                'photos.*' => 'max:10200',
               ]);
             if ($validator->fails()) { 
                  return response()->json(['status' => false, 'message' =>'Maximum 10 MB size of Product Photo are allowed'], 200);
              }

            $data = new Business_item();
        }
        $data->item_name = $request->get('name');
        $data->item_price = $request->get('price');
        $data->item_description = $request->get('description');
        $data->status = $request->get('status');
        $data->business_id = $value->id;
        $data->created_at=date("Y-m-d H:i:s"); 
        $data->save();
        $itemId = DB::getPdo()->lastInsertId();

        if(isset($id) && !empty($id)){
             if($request->hasFile('photos'))
              {
                $files = $request->file('photos');
                  $width=400;   // large image width
                  $height=400;  // large image height
                  $width1=512;  // medium image width
                  $height1=512; // medium image height
                  $width2=120;   // small image width
                  $height2=120;  // small image height
                  $path="uploads/item_image/";     // large image folder 
                  $path1="uploads/item_image_medium/"; // medium image folder 
                  $path2="uploads/item_image_small/";  // small image folder

                foreach($files as $file){
                  
               // imageResize($file, 'uploads/item_image_small/',60,60);
               // imageResize($file, 'uploads/item_image_medium/',150,150);
                $image_path =imageResize($file,$width,$height,$width1,$height1,$width2,$height2,$path,$path1,$path2);

                //echo $image_path1.'--'.$image_path2.'--'.$image_path; dd();
                DB::table('business_item_images')->insert(
                         array(
                                'item_id'=>$request->id, 
                                'image'   => $image_path
                         )
                    );
                }
            }
        }
            else{

        if($request->hasFile('photos'))
              {
                $files = $request->file('photos');
                    $width=400;
                    $height=400;
                    $width1=512;
                    $height1=512;
                    $width2=120;
                    $height2=120;
                    $path="uploads/item_image/";
                    $path1="uploads/item_image_medium/";
                    $path2="uploads/item_image_small/";
               // $path1 = 'uploads/item_image/';
                foreach($files as $file){
               // $image_path =  uploadImage($file, $path1);
             $image_path =imageResize($file,$width,$height,$width1,$height1,$width2,$height2,$path,$path1,$path2);
                DB::table('business_item_images')->insert(
                         array(
                                'item_id'=>$itemId, 
                                'image'   => $image_path
                         )
                    );
                }
            }
        }

         

         $results = DB::table('business_wishlist')
            ->leftJoin('users', 'users.id', '=', 'business_wishlist.user_id')
            ->select('business_wishlist.user_id', 'users.fcm_token','business_wishlist.id' )
            ->where('business_wishlist.business_id',$value->id)
            ->get();

            foreach($results as $feed_noti){
                 send_notification_FCM('New Feeds on Favorite','Woohoo!! Checkout some new pictures on your favorite business.', $token=$feed_noti->fcm_token, $redirection =$itemId,'business');

            $notify_data = new Notification();
            $notify_data->user_id = $feed_noti->user_id;
            $notify_data->title = 'New Feeds on Favorite';
            $notify_data->message = 'Woohoo!! Checkout some new pictures on your favorite business';
            $notify_data->notification_type = '1';
            $notify_data->is_read = '0';
            $notify_data->redirect_id =$itemId;
            $notify_data->notification_status = 'feed';
            $notify_data->created_at = date('Y-m-d H:i:s');
            $notify_data->save();

            }

        return response()->json(['status' => true, 'message' => ' Data has been updated successfully']);
    }



    public function getData($search = null  ,$orderby = null, $order = null,$id='', $request = null)
    {
        $q = Business_item::where('business_id','=',$id)->where('is_delete','=','0');
        $orderby = $orderby ? $orderby : 'created_at';
        $order = $order ? $order : 'desc';

        if ($search && !empty($search)) {
            $q->where(function ($query) use ($search) {
                $query->where('item_name', 'LIKE', '%' . $search . '%');            
               // ->orwhere('email_address', 'LIKE', '%' . $search . '%');
                
                //->orwhere('description', $search);
            });
        }
        $response = $q->orderBy($orderby, $order);
        return $response;
    }



    public function form($id = null)
    {
        $data = Business_item::where('id', $id)->first();
          //echo "<pre>";
          //print_r($data);dd();
        if(isset($data) && !empty($data)){
        $result = ['title' => ucwords('Updatedetails') ,'data' => $data];
         }
        else {
            $result = ['title' => ucwords('Add Details'),  'data' => $data];
                    }
       // return view('admin.' . $this->page . '.form', $result);
        return view('frontend/form',$result);
    }

   


   

}
