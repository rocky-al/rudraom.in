<?php

namespace App\Http\Controllers\Api;

use App\Constants\Constant;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function user_list(Request $request){
        $data = User::paginate(Constant::PAGINATION_LENGTH);
        return $this->sendResponse($data, 'User get successfully');
    }






}
