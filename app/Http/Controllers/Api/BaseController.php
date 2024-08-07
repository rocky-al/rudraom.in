<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{


    public function sendResponse($result, $message){
        $response = [
                'status_code' => 200,
                'status' => true,
                'message' => $message,
                'data' => $result,
            ];
        return response()->json($response);
    }

    
    public function sendError($error, $errorMessages = []){
        $response = [
                'status_code' => 400,
                'status' => false,
                'message' => $error,
                'data' => [],
            ];
        return response()->json($response);
    }


 


}
