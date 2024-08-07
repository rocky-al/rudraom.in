<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiAuthenticate
{

    public function handle(Request $request, Closure $next)
    {

         
          
        if (!auth()->guard('api')->check()) {
            abort(response()->json(
                [
                    'status' => 'false',
                    'message' => 'You are login from other device!',
                ],
                401
            ));
        }else{
            $user = User::where('id',auth()->guard('api')->user()->id)->first();
            if($user->deleted_at != NULL){
                abort(response()->json(
                    [
                        'status' => 'false',
                        'message' => 'Your account is deleted by admin or your account is deactivated by admin. please contact with admin',
                    ],
                    401
                ));
            }
            if($user->status=='0'){
                abort(response()->json(
                    [
                        'status' => 'false',
                        'message' => 'Your account is deleted by admin or your account is deactivated by admin. please contact with admin',
                    ],
                    401
                ));
            }
        }
        return $next($request);
    }
}