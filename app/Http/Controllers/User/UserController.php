<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\UserAddress;
use App\Models\User\UserCart;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Menu;
use Validator;
use Response;
use DB;
use App\Models\ContactUs;
use App\Models\OrderManagement;
use App\Models\OrderDetails;
class UserController extends Controller
{
    //
    public function Register(Request $request){
        $validations     =  array(            
            'name'         => 'required',
            'country_code' => 'required',
            'mobile'       => 'required|Numeric|unique:users',          
        );
        $validator =Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response   =[
            'message'   => $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
        $otp=1234;
        $data              = new User;
        $data->name        = $request->name;
        $data->country_code= $request->country_code;
        $data->mobile      = $request->mobile;
        $data->access_token= md5(time());
        $data->device_type = $request->device_type;
        $data->device_token= $request->device_token;
        $data->otp         = $otp;
        $data->is_verified = '0';
            if($data ->save()){
                return Response::json(['message'=>'User Registered Successfully', 'data'=>$data],200);
            }else{
                return Response::json(['message'=>'Something Went Wrong'],400);
            }
    }

    public function editProfile(Request $request){
        $validations     =  array(            
            'name'         => 'required',
            'country_code' => 'required',
            'mobile'       => 'required|Numeric|unique:users',          
        );
        $validator =Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response   =[
            'message'   => $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
            User::where('id', $request->UserData->id)->update([
                'name' => $request->name,
                'country_code'  => $request->country_code,
                'mobile' => $request->mobile,
            ]);
            return response()->json(['message'=>'Profile Updated Successfully  '], 200);
    }

    public function sendOtp(Request $request){
            $validations          =     array(
            'country_code'    => 'required_with:mobile',
            'mobile'          =>   'required',
        );
        $validator     = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response  =[
                'message'=> $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
            if(is_numeric($request->mobile)){
            $data = User::where('country_code',$request->country_code)
        ->where('mobile',$request->mobile)->where('is_block',0)->first();
        }else{
            return response()->json(['message'=>'Invalid Mobile No'],400);
            
        }
    if(($data)){
                $otp=1234;
                $data->otp=$otp;
                $data->access_token=md5(time());
                    if($data ->save()){
                    return Response::json(['message'=>'Otp sent Successfully', 'data'=>$data],200);
                }else{
                    return Response::json(['message'=>'Something Went Wrong'],400);
                }
      }else{
                    
                    return Response::json(['message'=>'You are not An Authorized User'],400);
                }
    }

    public function Login(Request $request){
        $otps   =$request->otp;
        $validations    =     array(
            // 'email'        =>   'required_without:mobile',
            'country_code' => 'required_with:mobile',
            'mobile'       => 'required',
            'otp'          =>   'required',
        );
        $validator         = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response = [
                'message'  => $validator->errors($validator)->first(),   
            ];
            return response()->json($response,400);
        }
        
        if(is_numeric($request->mobile)){
            $data = User::where('country_code',$request->country_code)
        ->where('mobile',$request->mobile)->where('is_block',0)->first();
        }else{
            return response()->json(['message'=>'Invalid Mobile No'],400);
            
        }
        if($data){
            if($data->otp == $otps){
                $data->is_verified = 1;
                $data->access_token=md5(time());
                $data->save();
                $response['message'] = "OTP successfully matched:Loged In";
                $response['data'] = $data;
                return response()->json($response,200);
            } else {
                return response()->json(['message'=>'Please Enter Valid OTP'],400);
            }
        } else {
            return response()->json(['message'=>'Sorry ! You are not an authorized user'],400);
        }
    }

    public function GetMenu(Request $request){
        $menu = Menu::where('is_block',0)->select('id','name')->get();
        if($menu){
            return response()->json(['message'=>'Pizza Menia','data'=>$menu],200);
        }else{
            return response()->json(['message'=>'Something Went Wrong'],400);
        }
    }

    public function itemDetail(Request $request){
        $itemDetail=Menu::where('id',$request->itemId)->first();
        return response()->json(['message'=>'Item Details','data'=>$itemDetail],200);
    }

    public function addCart(Request $request){
        $validations    = array(
            'product_id' => 'required',
            'quantity'   => 'required',
            'price'      => 'required',
        );
        $validator         = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response = [
                'message'  => $validator->errors($validator)->first(),   
            ];
            return response()->json($response,400);
        }
        $quantity = explode(',', $request->quantity);
        $addCart            = new UserCart;
        $addCart->user_id   = $request->UserData->id;
        $addCart->product_id= $request->product_id;
        $addCart->small     = $quantity[0];
        $addCart->medium    = $quantity[1];
        $addCart->large     = $quantity[2];
        $addCart->king      = $quantity[3];
        $addCart->price     = $request->price;
        if($addCart ->save()){
            return Response::json(['message'=>'Product Added To Cart', 'data'=>$addCart],200);
        }else{
            return Response::json(['message'=>'Something Went Wrong'],400);
        }
    }

    public function userCart(Request $request){
        $userCart = UserCart::where('user_id',$request->UserData->id)->get();
        if($userCart){
            foreach($userCart as $usercart){
                $userCartTotal = UserCart::where('user_id',$request->UserData->id)->select('price')->sum('price');
            }
            return Response::json(['message'=>'Product Added To Cart', 'data'=>$userCart,'Total Cart'=>$userCartTotal],200);
        }else{
            return Response::json(['message'=>'Something Went Wrong'],400);
        }
    }

    public function delete_cart(Request $request){
        $validations = array(
            'cartId'  => 'required',
        );
        $validator         = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response = [
                'message'  => $validator->errors($validator)->first(),   
            ];
            return response()->json($response,400);
        }
        $getCart = UserCart::where('id',$request->cartId)->get();
            if($getCart){
            $deleteCart = UserCart::where('id',$request->cartId)->delete();
                return Response::json(['message'=>'Cart Deleted Successfully'],200);
            }else{
                return Response::json(['message'=>'Enter A Valid CartID'],200);
            }
    }

    public function addAddress(Request $request){
        $validations = array(
            'street_address'  => 'required',
            'house_no'  => 'required',
            'zip_code'  => 'required',
            'address_type'  => 'required',
        );
        $validator         = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response = [
                'message'  => $validator->errors($validator)->first(),   
            ];
            return response()->json($response,400);
        }
        $addAddress    = new UserAddress;
        $addAddress->user_id = $request->UserData->id;
        $addAddress->street_address = $request->street_address;
        $addAddress->house_no = $request->house_no;
        $addAddress->zip_code = $request->zip_code;
        $addAddress->address_type = $request->address_type;
        if($addAddress ->save()){
            return Response::json(['message'=>'Address Saved Successfully', 'data'=>$addAddress],200);
        }else{
            return Response::json(['message'=>'Something Went Wrong'],400);
        }
    }

    public function userAddress(Request $request){
        $getAddress = UserAddress::where('user_id',$request->UserData->id)->get();
        return Response::json(['message'=>'User Address', 'data'=>$getAddress],200);
    }

    public function editAddress(Request $request){
        $data = UserAddress::where('id',$request->id)->first();
        if($data){
            UserAddress::where('id', $request->id)->update([
                'street_address' => $request->street_address,
                'house_no'  => $request->house_no,
                'zip_code' => $request->zip_code,
                'address_type'     => $request->address_type,
            ]);
            return response()->json(['message'=>'Address Updated Successfully  '], 200);
        }else{
            return response()->json(['message'=>'Something Went Wrong'], 404);
        }
    }


    public function order(Request $request){
        $validations = array(
            'shippping_address'  => 'required',
            'payment_method'  => 'required',
            'transaction_id'  => 'required_if:payment_method,1',

        );
        $validator         = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response = [
                'message'  => $validator->errors($validator)->first(),   
            ];
            return response()->json($response,400);
        }  
        $checkCart = UserCart::where('user_id',$request->UserData->id)->sum('id');
        if(!empty($checkCart)){
        $userAddress = UserAddress::where('id',$request->shippping_address)->select("*", DB::raw("CONCAT(user_address.street_address,' ',user_address.house_no) as full_name"))->pluck('full_name');
        $address =$userAddress;
        $userCartTotal = UserCart::where('user_id',$request->UserData->id)->select('price')->sum('price');
        $orderManagement                   =  new OrderManagement;
        $orderManagement->user_id          = $request->UserData->id;
        $orderManagement->user_name          = $request->UserData->name;
        $orderManagement->order_id         = 'ORD'.rand(1000,9999);
        $orderManagement->shippping_address= $address;
        $orderManagement->total_amount     = $userCartTotal;
        $orderManagement->payment_method   = $request->payment_method;
        $orderManagement->transaction_id   = $request->transaction_id;
        $orderManagement->order_status     = 1;
        $orderManagement->payment_status   = 1;
        if($orderManagement->save()){
            $getCart = UserCart::where('user_id',$request->UserData->id)->get();
        // dd($getCart);
            foreach($getCart as $filldetails){
                $orderDetail                   =  new OrderDetails;
                $orderDetail->user_id          = $orderManagement->user_id;
                $orderDetail->order_id         = $orderManagement->order_id;
                $orderDetail->product_id       = $filldetails->product_id;
                $orderDetail->product_name     = $filldetails->product_name;
                $orderDetail->small            = $filldetails->small;
                $orderDetail->medium           = $filldetails->medium;
                $orderDetail->large            = $filldetails->large;
                $orderDetail->king             = $filldetails->king;
                $orderDetail->amount           = $filldetails->price;
                $orderDetail->cart_id          = $filldetails->id;
            }
                if($orderDetail->save()){
                 UserCart::where('user_id',$request->UserData->id)->delete();
                  return response()->json(['message'=>'order Successfull'], 200);
                }else{
                return response()->json(['message'=>'Something Went Wrong'], 404);
            }
            }else{
                return response()->json(['message'=>'Something Went Wrong'], 404);
            }
        }else{
             return response()->json(['message'=>'Your Cart Is Empty'], 404);
        }
    }

    public function pastOrder(Request $request){
        $pastOrder = OrderManagement::where(['user_id'=>$request->UserData->id,'order_status'=>3])->select('order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->get();
         return Response::json(['message'=>'Past Order', 'data'=>$pastOrder],200);
    }

    public function ongoingOrder(Request $request){
        $pastOrder = OrderManagement::where(['user_id'=>$request->UserData->id,'order_status'=>1])->orWhere('order_status',2)->select('order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->get();
         return Response::json(['message'=>'Past Order', 'data'=>$pastOrder],200);
    }

    public function orderDetail(Request $request){
        $orderDetail = OrderManagement::where(['user_id'=>$request->UserData->id,'order_id'=>$request->order_id])->select('order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->get();
         return Response::json(['message'=>'Past Order', 'data'=>$orderDetail],200);
    }

    public function contactUs(Request $request){
        $validations = array(
            'name'  => 'required',
            'email'  => 'required',
            'message'  => 'required',
        );
        $validator         = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response = [
                'message'  => $validator->errors($validator)->first(),   
            ];
            return response()->json($response,400);
        }
        $contactUs    = new ContactUs;
        $contactUs->user_id = $request->UserData->id;
        $contactUs->name    = $request->name;
        $contactUs->email   = $request->email;
        $contactUs->country_code   = $request->UserData->country_code;
        $contactUs->mobile   = $request->UserData->mobile;
        $contactUs->message = $request->message;
        if($contactUs ->save()){
            return Response::json(['message'=>'Thankyou. You Will Hear From Us Shortly'],200);
        }else{
            return Response::json(['message'=>'Something Went Wrong'],400);
        }
    }

    public function logOut(Request $request){
        $token='';
       User::where('id',$request->UserData->id)->update(['access_token'=>$token]);
       return response()->json(['msg'=>'Logout Successfully'],200);
    }
}
