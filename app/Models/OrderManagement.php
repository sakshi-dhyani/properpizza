<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderManagement extends Model
{
    use HasFactory;
     protected $table = 'order_management';

    public function orderDetails(){
        return $this->hasOne(OrderDetails::class, 'order_id', 'order_id')->select('product_id','small','medium','large','king','order_id');
        }
}
