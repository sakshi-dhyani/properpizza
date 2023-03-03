<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin\Admin;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
    $accessToken =$request->header('accessToken');
        if($accessToken){
            $check=Admin::where('access_token',$accessToken)->first();
            if($check){
            $request['AdminData']=$check;
            return $next($request);    
            }else{
                $response['message']="Invalid access token";
                return response()->json($response,401);
            }
        }else{
            $response['message']="Access Token is required";
            return response()->json($response,403);
            }
        }
}
