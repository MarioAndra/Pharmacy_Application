<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result,$message){
        $response=[

            'success'=>true,
            'data'=>$result,
            'message'=>$message
        ];
        return response()->json($response,200);

       }
       public function sendError($error,$errorMessage=[],$code=200){
        $response=[

            'success'=>false,
            'data'=>$error
        ];
        if(!empty($errorMessage)){
            $response['data']=$errorMessage;
        }
        return response()->json($response,$code);

       }
       public function sendLog($result,$message){
        $response=[
            'success'=>true,
            'data'=>$result,
            'message'=>$message
        ];
        return response()->json($response,200);
       }

}
