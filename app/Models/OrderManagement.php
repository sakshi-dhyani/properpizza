<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\UserAddress;
class OrderManagement extends Model
{
    use HasFactory;
     protected $table = 'order_management';

    public function orderDetails(){
        return $this->hasOne(OrderDetails::class, 'order_id', 'order_id')->select('product_id','product_name','small','medium','large','king','order_id');
        }

    public function userAddress(){
        return $this->hasOne(UserAddress::class, 'user_id', 'user_id')->where('address_type',1)->select('user_id','street_address','house_no','zip_code');
        }
}
