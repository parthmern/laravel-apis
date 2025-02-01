<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;

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

    public function getUserDetail($id){
        try{
            $user = User::find($id);
            return response()->json([
                "status" => "true",
                "user"=> $user
            ]);
        }
        catch(Exception $e){
            return response()->json([
                'staus' => "failed",
                'error' => $e
            ]);
        }
    }

    public function updateUser(Request $request, $id)
    {
        // Verifying if user exists
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not available',
            ], 404);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'email' => "required|string|email|unique:users,email,$id",
            'password' => "nullable|string|min:6",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false, 
                'message' => 'Validation error', 
                'error_message' => $validator->errors()
            ], 400);
        }

        try {
            $user->name = $request->name;
            $user->email = $request->email;

            // Only update password if provided
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            return response()->json([
                'status' => true, 
                'message' => 'User updated successfully', 
                'updatedUser' => $user
            ], 200);

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

    public function deleteUser($id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'successs' => "false",
                'message' => "user not existed"
            ], 300);
        }

        $user->delete();

        return response()->json([
            'success' => "true",
            'message' => "user deleted"
        ], 200);

    }

}
