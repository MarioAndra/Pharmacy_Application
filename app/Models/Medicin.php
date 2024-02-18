<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Medicin extends Model
{

    use HasFactory;
    protected $fillable = [
        'Scientific_name',
        'commercial_name',
        'category',
        'quantity',
        'Manufacture_Company',
        'price',
        'Expiry_data',
        'warehouse_id',
    ];
    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }


}
