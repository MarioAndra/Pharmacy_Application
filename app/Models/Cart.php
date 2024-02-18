<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable=['status','paymentStatus','phermesist_id','warehouse_id'];
    public function medicins(){
        return $this->belongsToMany(Medicin::class,'cart_medicins','cart_id','medicin_id')->withPivot('cart_id','id','medicin_id','quantity');
    }
    public function repository(){
        return $this->belongsToMany(Warehouse::class,'warehouse_id')->get();
    }
}
