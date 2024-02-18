<?php

namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Medicin;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends BaseController
{
    public function regester(Request $request){

        $validat=Validator::make($request->all(),[
            'name'=>'required',
            'number'=>'required|between:10,10|unique:users,number',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8',
            'role'=>'user',
            'warehouse_id'=>'nullable',
            'warehouse_name'=>'nullable'
        ]);
        if($validat->fails()){
            return $this->sendError('please make sure you fill the information',$validat->errors(),500);
        }
        $input=$request->all();
        $input['password']=Hash::make($input['password']);
        $user=User::create($input);
        $success['token']=$user->createToken('secret')->plainTextToken;


        return $this->sendResponse($success,'User registered successfully');
    }
    public function login(Request $request){
        if(Auth::attempt(['number' =>$request->number, 'password' => $request->password])){
            $user=Auth::user();
            $success['token']=$user->createToken('secret')->plainTextToken;
            return $this->sendResponse($success,'User logged successfully');
        }
        else{
            return $this->sendError('check your information',['error'=>'Your number or password not correct'],500);
        }
    }

    public function logout(Request $request) {
        $success=[];
        $request->user()->currentAccessToken()->delete();
        return $this->sendLog($success,"logout successfully");
    }
}
