<?php

namespace App\Http\Controllers\Api\Center;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\ModelHasRole;
use App\Models\Center;
use App\Models\Centerimage;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SaleController extends Controller
{
	public function TodaySales(Request $request){
        $id = $request->id;
        $formattedDate = Carbon::today();
        $dailySales = Order::whereHas('items', function ($query) use ($id, $formattedDate) {
                $query->where('center_id', $id)
                      ->whereDate('created_at', $formattedDate);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($id, $formattedDate) {
                    $query->where('center_id', $id)
                          ->whereDate('created_at', $formattedDate)
                          ->with(['product', 'center']);
                }
            ])
            ->get();                     
        return response()->json([
            'error'=>0,
            'message' => 'مبيعات اليوم',
            'dailySales' => $dailySales,
        ], 201);        
    }

	public function DailySales(Request $request){
        $formattedDate = Carbon::parse($request->date)->startOfDay()->toISOString();
        $id = $request->id;    
        $dailySales = Order::whereHas('items', function ($query) use ($id, $formattedDate) {
                $query->where('center_id', $id)
                      ->whereDate('created_at', $formattedDate);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($id, $formattedDate) {
                    $query->where('center_id', $id)
                          ->whereDate('created_at', $formattedDate)
                          ->with(['product', 'center']);
                }
            ])
            ->get();             

        return response()->json([
            'error'=>0,
            'message' => 'مبيعات ' . $request->date,
            //'total sale' => $total,
            'todaySales' => $dailySales,
            
        ], 201);            
    }
        
        
	public function MonthSales(Request $request){
        $id = $request->id;
            
            
        $monthlySales = Order::whereHas('items', function ($query) use ($id) {
                $query->where('center_id', $id)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', Carbon::now()->month);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($id) {
                    $query->where('center_id', $id)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->whereMonth('created_at', Carbon::now()->month)
                          ->with(['product', 'center']);
                }
            ])
            ->get();               
        return response()->json([
            'error'=>0,
            'message' => 'مبيعات الشهر',
            'dailySales' => $monthlySales,
        ], 201);            
    }
        
        
	public function MonthlySales(Request $request){
        //$formattedDate = Carbon::parse($request->date)->month;
        $id = $request->id;
        $date = $request->date;
        $MonthlySales = Order::whereHas('items', function ($query) use ($id , $date) {
                $query->where('center_id', $id)
                        ->whereYear('created_at', Carbon::parse($date)->year)
                        ->whereMonth('created_at', Carbon::parse($date)->month);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($id , $date) {
                    $query->where('center_id', $id)
                        ->whereYear('created_at', Carbon::parse($date)->year)
                        ->whereMonth('created_at', Carbon::parse($date)->month)
                          ->with(['product', 'center']);
                }
            ])
            ->get();             

        return response()->json([
            'error'=>0,
            'message' => 'مبيعات ' . Carbon::parse($request->date)->year.'/'.Carbon::parse($request->date)->month,
            //'total sale' => $total,
            'todaySales' => $MonthlySales,
            
        ], 201);            
    }
              
        
        
	public function YearSales(Request $request){
        $id = $request->id;
            
        $yearlySales = Order::whereHas('items', function ($query) use ($id) {
                $query->where('center_id', $id)
                        ->whereYear('created_at', Carbon::now()->year);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($id) {
                    $query->where('center_id', $id)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->with(['product', 'center']);
                }
            ])
            ->get();             
            
                    
            
        return response()->json([
            'error'=>0,
            'message' => 'مبيعات السنة',
            'dailySales' => $yearlySales,
        ], 201);  
    }

        
	public function YearlySales(Request $request){
        //$formattedDate = Carbon::parse($request->date)->startOfDay()->toISOString();
        $id = $request->id;
            
        $date = $request->date;
        $YearlySales = Order::whereHas('items', function ($query) use ($id , $date) {
                $query->where('center_id', $id)
                        ->whereYear('created_at', Carbon::parse($date)->year);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($id , $date) {
                    $query->where('center_id', $id)
                        ->whereYear('created_at', Carbon::parse($date)->year)
                          ->with(['product', 'center']);
                }
            ])
            ->get();             
            
           
        return response()->json([
            'error'=>0,
            'message' => 'مبيعات ' . Carbon::parse($request->date)->year,
            //'total sale' => $total,
            'todaySales' => $YearlySales,
            
        ], 201);            
    }
        
	public function AllDailySales(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
        $center = Center::where('owner_id',$user->id)->get();
        if(!$center){                
                return response()->json([
                    'error'=>1,
                    'message' => 'المستخدم لا يملك مركز',

                ], 201);                 
        }
        
        $arr = array();           
        foreach ($center as $c){
                array_push($arr,$c->id);
        }      
        $formattedDate = Carbon::parse($request->date)->startOfDay()->toISOString();
        $dailySales = Order::whereHas('items', function ($query) use ($arr, $formattedDate) {
                $query->whereIn('center_id', $arr)
                      ->whereDate('created_at', $formattedDate);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($arr, $formattedDate) {
                    $query->whereIn('center_id', $arr)
                          ->whereDate('created_at', $formattedDate)
                          ->with(['product', 'center']);
                }
            ])
            ->get();            

        return response()->json([
            'error'=>0,
            'message' => 'مبيعات ' . $request->date,
            //'total sale' => $total,
            'todaySales' => $dailySales,
            
        ], 201);            
    }        
        
        
	public function AllMonthlySales(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
        $center = Center::where('owner_id',$user->id)->get();
        if(!$center){                
                return response()->json([
                    'error'=>1,
                    'message' => 'المستخدم لا يملك مركز',

                ], 201);                 
        }
        
        $arr = array();           
        foreach ($center as $c){
                array_push($arr,$c->id);
        }             
        $date = Carbon::parse($request->date)->startOfDay()->toISOString();
        $MonthlySales = Order::whereHas('items', function ($query) use ($arr , $date) {
                $query->whereIn('center_id', $arr)
                        ->whereYear('created_at', Carbon::parse($date)->year)
                        ->whereMonth('created_at', Carbon::parse($date)->month);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($arr , $date) {
                    $query->whereIn('center_id', $arr)
                        ->whereYear('created_at', Carbon::parse($date)->year)
                        ->whereMonth('created_at', Carbon::parse($date)->month)
                          ->with(['product', 'center']);
                }
            ])
            ->get();             

        return response()->json([
            'error'=>0,
            'message' => 'مبيعات ' . Carbon::parse($request->date)->year.'/'.Carbon::parse($request->date)->month,
            //'total sale' => $total,
            'todaySales' => $MonthlySales,
            
        ], 201);            
    }        
       
        

        
	public function AllYearlySales(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
        $center = Center::where('owner_id',$user->id)->get();
        if(!$center){                
                return response()->json([
                    'error'=>1,
                    'message' => 'المستخدم لا يملك مركز',

                ], 201);                 
        }
        
        $arr = array();           
        foreach ($center as $c){
                array_push($arr,$c->id);
        }             
        $date = Carbon::parse($request->date)->startOfDay()->toISOString();

        $YearlySales = Order::whereHas('items', function ($query) use ($arr , $date) {
                $query->whereIn('center_id', $arr)
                        ->whereYear('created_at', Carbon::parse($date)->year);
            })
            ->with([
                'customer',
                'items' => function ($query) use ($arr , $date) {
                    $query->whereIn('center_id', $arr)
                        ->whereYear('created_at', Carbon::parse($date)->year)
                          ->with(['product', 'center']);
                }
            ])
            ->get();             
            
           
        return response()->json([
            'error'=>0,
            'message' => 'مبيعات ' . Carbon::parse($request->date)->year,
            //'total sale' => $total,
            'todaySales' => $YearlySales,
            
        ], 201);            
    }
        
	public function addSale(Request $request){
        /*
            order 
                'customer_id',
                'total_price',
        */
        /*
            orderItem 
                'order_id',
                'product_id',
                'center_id',
                'quantity',
                'unit_price',
        */       

        $order = new Order();
        $order->customer_id = $request->customer_id;
        $order->total_price = $request->total_price;
        $order->save();
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $request->product_id;
        $orderItem->center_id = $request->center_id;
        $orderItem->unit_price = $request->unit_price;
        $orderItem->quantity = $request->quantity;
        $order->save();
        return response()->json([
            'error'=>0,
            'message' => 'تمت الاضافة',
            //'total sale' => $total,
            'order' => $order,
            'orderItem' => $orderItem,         
        ], 201);   

    }
        
}
