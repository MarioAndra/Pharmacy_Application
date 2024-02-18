<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
       'name',
    ];

    public function order(){
        return $this->hasMany(Cart::class,'warehouse_id')->select('id')->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as create_at')->get()->all();


    }

}
