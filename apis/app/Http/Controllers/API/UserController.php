<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    # function to create user
    public function createUser(Request $request){

        # Log::info('User Data:', $request->all());

        // validation
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'email' => "required|string",
            'password' => "required|string|min:6"
        ]);

        if($validator->fails()){
            return response()->json(array('status'=>false, 'message' => 'validator error', 'error_message'=> $validator->errors()), 400);
        }

        $user = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]
            );

        if($user->id){
            $result = array('status'=>true, 'message' => 'user created', "data"=> $user);
            $resCode = 201;
        }else{
            $result = array('status'=>false, 'message' => 'user not created');
            $resCode = 401;
        }

        return response()->json($result, $resCode);
    }
}
