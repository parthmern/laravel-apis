<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    # function to create user
    public function createUser(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'email' => "required|string|email|unique:users,email",
            'password' => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false, 
                'message' => 'Validation error', 
                'error_message' => $validator->errors()
            ], 400);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            return response()->json([
                'status' => true, 
                'message' => 'User created', 
                "data" => $user
            ], 201);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Database error', 
                'error' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Something went wrong', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(Request $request){
        try{
            $users = User::all();
            return response()->json([
                'status' => "success",
                'noOfUsers' => count($users)."fetched",
                'users' => $users
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'staus' => "failed",
                'error' => $e
            ]);
        }
    }
}
