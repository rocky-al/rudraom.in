<?php

namespace App\Http\Controllers\Api;

use App\Constants\Constant;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class CronJobController extends BaseController
{



    // check subscription daily 
    public function subscription_check(Request $request){
        $users =  User::whereHas('roles', function($query)
        {
           $query->where('name', 'user');
        });
        $users = $users->where('subscription_status', Constant::ACTIVE)->select('id', 'end_date')->get();
        $current_date = Date('Y-m-d');

        foreach($users as $key => $item){
            if($item->end_date < $current_date){
                User::where('id', $item->id)->update(['subscription_status' => Constant::IN_ACTIVE]);
            }
        }
 
    }


// notification send 
    public function send_notification(Request $request){
        $users =  User::whereHas('roles', function($query)
        {
           $query->where('name', 'user');
        });

        $users = $users->where('subscription_status', Constant::ACTIVE)->select('id', 'end_date')->get();
        
        foreach($users as $key => $item){
            $current_date = date('Y-m-d');
            $new_date = date('Y-m-d', strtotime($current_date. ' + 30 days') );
            if($item->end_date == $new_date){
                $data = new Notification();
                $data->title = "Subscription Expiration Notification";
                $data->description = "Your is plan is expire afert 30 days Please active your plan";
                $data->sender_id = 1;
                $data->receiver_id = $item->id;
                $data->notification_type = 0;
                $data->save();
            }
        }
 
    }


public function temp_crop() {
     $folder_path = base_path('uploads/temp_crop/');
     $files = glob($folder_path.'/*'); 
    // Deleting all the files in the list
    foreach($files as $file) {
        if(is_file($file)) 
        // Delete the given file
            unlink($file); 
    }

    echo "Temp crop folder cleared.";
      
    }


}
