<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;
use \Symfony\Component\Console\Output\ConsoleOutput;
class UserController extends Controller
{

    //login validation

    public function login(Request $request){

    

        $validator = Validator::make($request -> all(),[
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if($validator -> fails()){
            return response() -> json([
                'message'=> "Fill details properly",
                'success'=> false,
            ],400);
        }

        if(!$token = auth() ->attempt($validator -> validated())){
            return response() -> json(['error'=>'UnAuthorized']);
        }
        
        return $this -> responseWithToken($token);


    }

    protected function responseWithToken($token){
        return response() -> json([
            'access_token'=> $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()*60
        ]);
    }

    //register user after validation
    public function register(Request $request){

        try{
        $validator = Validator::make($request -> all(),[
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|string|max:100|unique:users'
        ]);

        if($validator -> fails()){
            return response() -> json($validator->errors(),400);
        }

        $user = User::create([
            'name' => $request -> name,
            'email' => $request -> email,
            'password' => Hash::make($request -> password)
        ]);

        
        if($user){

        return response() -> json([
            'message' => 'User Registered successfully',
            'user' => $user,
            'success'=> true
        ]);
    }else{
        return response() -> json([
            'message' => "something went wrong",
            'success' => false
        ]);
    }
    }catch(error $er){
        return response() -> json([
            'message' => "something went wrong",
            'success' => false
        ]);
    }

    }
}
