<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\ModelHasRole;
use App\Models\Center;
use App\Models\RoleHasPermission;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Productimage;
use Illuminate\Support\Facades\File;



class ProductController extends Controller
{
        public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'center_id' => 'required'
            ]);
            $product = new Product();
            $product->name = $validated['name'];
            $product->description = $validated['description'] ?? null;
            $product->price = $validated['price'];
            $product->center_id = $validated['center_id'];
            if($request->quantity){
                    $product->quantity = $request->quantity;
            }
            $product->save();
                

                $image = $request->file('image');
                if ($image = $request->file('image')  ) {
                        $destinationPath = '../../institueproject.com/images/product/';
                        $arr = array();
                        $j = 0;
                        for ($i=0; $i<count($request->image); $i++){
                                $j++;
                                $profileImage = date('YmdHis')+$j.".".$image[$i]->getClientOriginalExtension();
                                array_push($arr,$profileImage);
                                $image[$i]->move($destinationPath, $profileImage);
                        }
                        $Productimage = app('App\Http\Controllers\Api\ProductimageController')->store($request,$product,$arr);
                }

            $productWithImage = Product::with([
	            'center',
    	        'product_image',
        	])->where('id', $product->id)->get();            
            


            return response()->json([
                'error' =>0,
                'message' => 'تم إنشاء المنتج بنجاح',
                'product' => $productWithImage,
            ], 201);
        }
        
    public function update(Request $request, Product $product)
    {
            // Validate only the provided fields
            
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'product_id' => 'sometimes|required|integer|exists:products,id',
                'description' => 'sometimes|nullable|string',
                'price' => 'sometimes|required|numeric',
                'quantity' => 'sometimes|required|integer',
            ]);
            
            // Find the product
            $product = Product::findOrFail($request->product_id);

            // Update only the fields provided in the request
            $product->update($validated);
            
            $image = $request->file('image');
            if ($image = $request->file('image')  ) {
                    $destinationPath = '../../institueproject.com/images/product/';
                    $arr = array();
                    $j = 0;
                    for ($i=0; $i<count($request->image); $i++){
                            $j++;
                            $profileImage = date('YmdHis')+$j.".".$image[$i]->getClientOriginalExtension();
                            array_push($arr,$profileImage);
                            $image[$i]->move($destinationPath, $profileImage);
                     }
                     $Productimage = app('App\Http\Controllers\Api\ProductimageController')->store($request,$product,$arr);
             }

            $productWithImage = Product::with([
	            'center',
    	        'product_image',
        	])->where('id', $product->id)->first();            
            
            
            
            
            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $productWithImage,
            ]);
            

    }
        
    public function delete(Request $request){
    	
           $validated = $request->validate([
                'product_id' => 'sometimes|required|integer|exists:products,id',
            ]);
            
            // Find the product
            $product = Product::findOrFail($request->product_id);

            $product->delete();
            
            
            return response()->json([
                'message' => 'Product deleted successfully',

            ]);
    }    
        
        
        
    public function all_products(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
            
        $products = Product::with([
            'center',
            'product_image',
        ])->where('center_id', $request->center_id)->latest()->get();            
            
            
        
        return response()->json([
            'error'=>0,
            'message' => 'المنتجات الخاصة بك.',
            'products' => $products,
        ], 201);
            
    }        
        
        
    public function product_by_id(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
            
            
        $products = Product::with([
            'center',
            'product_image',
        ])->where('id', $request->product_id)->latest()->get();            
                     
        
        return response()->json([
            'error'=>0,
            'message' => 'المنتجات الخاصة بك.',
            'products' => $products,
        ], 201);
            
    }   
        
    public function deleteImg(Request $request){
  
        $Productimage = Productimage::find($request->id);
        if (!$Productimage) {
        	return response()->json([
                    'error'=>1,
                    'message' => 'Image not found'
            ], 404);
    	}
            // استخراج اسم الملف فقط من URL
    	$filename = basename($Productimage->url); 
                // المسار المطلق الصحيح للمجلد الذي يحتوي الصور
	    $absoluteImagesPath = '/srv/disk9/4515479/www/institueproject.com/images/product/';  
                // بناء المسار الكامل للصورة
        $fullPath = $absoluteImagesPath . $filename;  
                // حذف الملف من السيرفر إن وجد
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }                                
        $Productimage->delete();  
        return response()->json([
                'error'=>0,
                'message' => 'Image deleted successfully',

            ]);            

    }
                
              
        
}
