<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Medicin;
use App\Models\User;
use App\Models\Admin;
use App\Models\Warehouse;
use App\Models\Cart;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\AuthController;
use Validator;
use Carbon\Carbon;

class AdminController extends BaseController
{
    public function createWareHouse(Request $request){

        $validat=Validator::make($request->all(),[
            'name'=>'required'
        ]);
        $input=$request->all();

        $wareHouse=Warehouse::create($input);
        return $this->sendResponse('','WareHouse Create');
    }

    public function Adminregester(Request $request){

        $validat = Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required|between:10,10|unique:users,number',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'warehouse_id' => 'nullable',
            'warehouse_name' => 'required'
        ]);

        if($validat->fails()){
            return $this->sendError('', $validat->errors(), 500);
        }

        $input = $request->all();
        $input['role'] = "admin";
        $input['password'] = Hash::make($input['password']);

        $admin = User::create($input);


        $warehouseName = $request->warehouse_name;
        $warehouse = Warehouse::create(['name' => $warehouseName]);
        $warehouse->id=$admin->id;
        $warehouse->update();
        $success['token'] = $admin->createToken('secret')->plainTextToken;

        return $this->sendResponse($success, 'Admin registered successfully');
    }




    public function loginAdmin(Request $request){

        if(Auth::attempt(['number' =>$request->number, 'password' => $request->password])){
            $admin=Auth::user();
            if($admin->role=='admin'){
            $success['token']=$admin->createToken('secret')->plainTextToken;
            return $this->sendResponse($success,'Admin logged successfully');
            }
            else{
                return $this->sendError('',['error'=>'You are not authorized to login as admin'],500);
            }
        }
        else{
            return $this->sendError('check your information',['error'=>'Your number or password not correct'],500);
        }
    }



    public function logoutAdmin(Request $request) {
        $success=[];
        $request->user()->currentAccessToken()->delete();
        return $this->sendLog($success,"Admin logout successfully");
    }



    public function addMedicin(Request $request){
        $admin=Auth::user();
        $wareHouse_id=$admin->id;
        $validat=Validator::make($request->all(),[
            'commercial_name'=>'required',
            'Scientific_name'=>'required',
            'category'=>'required',
            'Expiry_data'=>'required',
            'quantity'=>'required|min:0',
            'price'=>'required|min:0',
            'Manufacture_Company'=>'required',

        ]);


        if($validat->fails()){
            return $this->sendError('',['error'=>'please make sure you fill the information of Medicin']);
        }
        if($admin->role=='admin'){
        $input=$request->all();
        $input['quantity'] = intval($input['quantity']);
        $medicin=Medicin::where('Scientific_name',$request->Scientific_name)->where('warehouse_id',$wareHouse_id)->where('category',$request->category)->get()->first();
        if($medicin){
            $medicin->quantity+=$request->quantity;
            $medicin->price=$request->price;
            $medicin->save();
            return $this->sendError('',['NOTE'=>'The medicin already exists so the quantity and price were modified'],500);
        }

        else{
          $input['quantity'] = intval($input['quantity']);
          $input['price'] = doubleval($input['price']);
          $input['Expiry_data'] = Carbon::parse($request->Expiry_data)->format('Y-m-d');
        $medicin=Medicin::create([
            'commercial_name'=>$request->commercial_name,
            'Scientific_name'=>$request->Scientific_name,
            'category'=>$request->category,
            'Expiry_data'=>$input['Expiry_data'],
            'quantity'=>$input['quantity'],
            'price'=>$input['price'],
            'Manufacture_Company'=>$request->Manufacture_Company,
            'warehouse_id'=>$wareHouse_id,
        ]);
        return $this->sendResponse($medicin,'Medicin added successfully');
        }

    }
    return $this->sendError('',['error'=>'You are not authorized to add medicine']);

}

    public function UpdateOrderStatus($id,Request $request){
        $admin=Auth::user();

        if($admin->role=='admin'){
        try{
            DB::beginTransaction(); /// start tramsaction
           $record = Cart::find($id);
           if(!$record){
            return $this->sendError('', ['error' => 'please try again later']);
           }
           $record->update($request->only(['status']));

           if ($request->status=='Reseved') {
            $record->update(['paymentStatus'=>'payed']);
               $order_medecins=$record->medicins;

               foreach ($order_medecins as $medicin){
                   $new_quantity=$medicin->quantity-$medicin->pivot->quantity;
                   if($new_quantity<0){
                    return $this->sendError('', ['error' => 'One medication is not enough for you have in the warehouse'],500);
                   }
                   $new_medicin=Medicin::find($medicin->id);
                   $new_medicin->update(['quantity' => $new_quantity]);
                   $new_medicin->save();
                }
           }
            DB::commit(); /// sava editings
            return $this->sendResponse([$record],"Done Successfully");
        }catch(\Exception $ex){
            DB::rollBack(); /// go back and return  error message
            return $this->sendError('', ['error' => 'please try again later']);
        }
    }
    return $this->sendError('', ['error' => 'You are not authorized to update of the order']);
 }

 public function ShowOrdersAdmin()
 {

     $admin_id = Auth::id();
    // return $admin_id;

     if (!$admin_id) {
         return $this->sendError('', ['error' => 'please make sure that you have loggedin']);
     }
     $admin=Auth::user();
     $admin_id=$admin->id;
     $repo=Warehouse::find($admin_id);

     $orders = $repo->order();

     return $this->sendResponse($orders, "Done");


 }

}
