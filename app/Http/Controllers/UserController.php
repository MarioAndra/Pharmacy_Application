<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Medicin;
use App\Models\User;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Warehouse;
use App\Models\Cart_medicin;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\AuthController;
use Validator;
class UserController extends BaseController
{
    public function BrowseMedicinByCategory(Medicin $r,$warehouse_id){

            $categories = Medicin::select('category')->distinct()->get();
            $result = [];
            foreach ($categories as  $category){
                $medicines = Medicin::where('category', $category->category)
                                    ->whereDate('Expiry_data', '>=', '2024-01-01')
                                    ->where('warehouse_id',$warehouse_id)
                                    ->get(['Scientific_name', 'price', 'Expiry_data', 'quantity', 'Manufacture_Company'])->all();
                $result[$category->category] = $medicines;
            }
            return $this->sendResponse($result, 'Done Successfully');
        }



    public function serchMedicin(Request $request)
    {
        $medicineName = $request->input('Scientific_name');
        $medicine = Medicin::where('Scientific_name', $medicineName)->first();
        if ($medicine) {
            return $this->sendResponse($medicine,'Done Successfully');
        }
        else {
            return $this->sendError('',['error'=>'Not Found'],500);
        }
    }

    public function order($warehouse_id,Request $request)
    {
        $user_id = Auth::id();

        if (!$user_id) {
            return $this->sendError('', ['error' => 'please make sure that you have loggedin'],401);
        }
        $medicin=[];
        $cart = Cart::query()->create([
            'phermesist_id' => $user_id,
            'warehouse_id'=> $warehouse_id,
            'status' => 'Preparing',
            'paymentStatus' => 'notPayed',
        ]);
        $items=$request->items;
        foreach ($items as $item) {
         $medicine=Medicin::find($item['id']);
         if($medicine&&$medicine->quantity>=$item['quantity']){
            $cart->medicins()->attach($medicine->id,['quantity'=>$item['quantity']]);
            $medicin[]=$medicine;
         }
        else{
            $cart->delete();
            return $this->sendError('', ['error' => 'Medicine is not availlable'],500);
        }

        }
        return $this->sendResponse($medicin, "Done");

    }


    public function ShowOrders()
    {

        $user_id = Auth::id();
        if (!$user_id) {
            return $this->sendError('', ['error' => 'please make sure that you have loggedin'],401);
        }
        $user=User::find($user_id);
        $orders = $user->orders()->orderBy("created_at","DESC")->select('id','status','paymentStatus')->get();
        return $this->sendResponse($orders, "Done");

    }




    public function NameWareHouse(){

        $name=Warehouse::get()->all();
        return $this->sendResponse($name, "Done");
    }

}






