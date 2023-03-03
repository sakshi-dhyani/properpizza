<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Cancelation;
use App\Models\OrderManagement;
use Illuminate\Http\Request;
use App\Models\Admin\Admin;
use App\Models\OrderDetails;
use App\Models\User;
use Validator;
use Response;
use DB;
use Hash;
use App\Models\ContactUs;
class AdminController extends Controller
{
    //
    public function Login(request $request){
        $validations          =     array(
            'email'           =>   'required',
            'password'        =>   'required',
        );
        $validator     = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response  =[
                'message'=> $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
            $data = Admin::where('email',$request->email)->first();
        if(($data)){
                    if($data->is_verified == 1){
                          if(Hash::check($request->password, $data->password)){
                            $data->access_token  = md5(time());
                            $data->save();
                            return response()->json(['message'=>'Logged In', 'data'=>$data], 200);
                            }else{
                                return response()->json(['message'=>'Invalid Credential'], 400);
                            }  
                    }else{
                    return response()->json(['message'=>'Not Verified'], 400);
                }
        }else{
                return response()->json(['message'=>'No Data Found'], 400);
            }
    }
 
    public function loginStatus(request $request){
        $serviceProviderId = $request->AdminData->id;
        $Details = Admin::where('id',$request->AdminData->id)->select('status')->first();
        if($Details->status==0){
            Admin::where([
                'id' => $serviceProviderId,
            ])->update(['status' => 1]);
        $Detailss = Admin::where('id',$request->AdminData->id)->select('status')->first();
        return response()->json(['status' => 'success', 'message' => 'Status updated successfully:Online.','data'=>$Detailss], 200);
        }elseif($Details->status==1){
            Admin::where([
                'id' => $serviceProviderId,
            ])->update(['status' => 0]);
        $Detailss = Admin::where('id',$request->AdminData->id)->select('status')->first();
        return response()->json(['status' => 'success', 'message' => 'Status updated successfully:Ofline.','data'=>$Detailss], 200);
        }
    }

    public function previousOrder(request $request){
                $previousOrder = OrderManagement::where('order_status',3)->select('order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->with('userAddress')->get();
         return Response::json(['message'=>'Past Order', 'data'=>$previousOrder],200);
    }

    public function cancelledOrder(request $request){
                $cancelledOrder = OrderManagement::where('order_status',4)->select('order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->with('userAddress')->get();
         return Response::json(['message'=>'Cancelled Order', 'data'=>$cancelledOrder],200);
    }

    public function ongoingOrder(request $request){
        $ongoingOrder = OrderManagement::where('order_status',2)->select('order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->with('userAddress')->get();
        return Response::json(['message'=>'Ongoing Order', 'data'=>$ongoingOrder],200);
    }

    public function upcomingOrder(request $request){
        $upcomingOrder = OrderManagement::where('order_status',1)->select('user_id','user_name','order_id','shippping_address','payment_method','total_amount','created_at')->with('orderDetails')->with('userAddress')->get();
        return Response::json(['message'=>'Upcomimg Order', 'data'=>$upcomingOrder],200);
    }

    public function cancelationReason(request $request){
        $cancelationReason = Cancelation::all();
        return Response::json(['message'=>'cancelationReason', 'data'=>$cancelationReason],200);
    }

    public function cancelOrder(request $request){
            $validations          =     array(
            'order_id'            =>'required',
            'cancellation_reason' =>'required',
            'cancellation_comment'=>'required',
        );
        $validator     = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response  =[
                'message'=> $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
        $cancelOrder = OrderManagement::where('order_id',$request->order_id)->first();
        if($cancelOrder){
            OrderManagement::where([
                'order_id' => $request->order_id,
            ])->update(['order_status' => 4,
                        'cancellation_reason'=>$request->cancellation_reason,
                        'cancellation_comment'=>$request->cancellation_reason]);
         $cancelOrders = OrderManagement::where('order_id',$request->order_id)->first();
        return response()->json(['status' => 'success', 'message' => 'Status updated successfully:Cancelled.','data'=>$cancelOrders], 200);
        }else{
             return response()->json(['message'=>'Enter A Valid OrderId'], 404);
        }
    }

    public function acceptOrder(request $request){
            $validations          =     array(
            'order_id'            =>'required',
        );
        $validator     = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response  =[
                'message'=> $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
        $acceptOrder = OrderManagement::where('order_id',$request->order_id)->first();
        if($acceptOrder){
            OrderManagement::where([
                'order_id' => $request->order_id,
            ])->update(['order_status' => 5]);
         $acceptOrders = OrderManagement::where('order_id',$request->order_id)->first();
        return response()->json(['status' => 'success', 'message' => 'Status updated successfully:Perparing.','data'=>$acceptOrders], 200);
        }else{
             return response()->json(['message'=>'Enter A Valid OrderId'], 404);
        }
    }


    public function outDelivery(request $request){
            $validations          =     array(
            'order_id'            =>'required',
        );
        $validator     = Validator::make($request->all(),$validations);
        if($validator->fails()){
            $response  =[
                'message'=> $validator->errors($validator)->first(),
            ];
            return response()->json($response,400);
        }
        $outDelivery = OrderManagement::where('order_id',$request->order_id)->first();
        if($outDelivery){
            OrderManagement::where([
                'order_id' => $request->order_id,
            ])->update(['order_status' => 2]);
         $outDeliverys = OrderManagement::where('order_id',$request->order_id)->first();
        return response()->json(['status' => 'success', 'message' => 'Status updated successfully:outforDelivery.','data'=>$outDeliverys], 200);
        }else{
             return response()->json(['message'=>'Enter A Valid OrderId'], 404);
        }
    }

    public function contactUs(request $request){
        $contactUs=ContactUs::all();
        return response()->json(['message'=>'contactUs', 'data'=>$contactUs], 200);
    }
}