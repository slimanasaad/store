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
use App\Models\Centerimage;
use App\Models\RoleHasPermission;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Productimage;
use Illuminate\Support\Facades\DB;


class ProductimageController extends Controller
{
    public function store(Request $request,$product,$arr){
        if(!$product || !$arr ){
            return;
        }
        $Productimage_arr = array();
        for ($i=0; $i<count($arr); $i++){
	        $Productimage = Productimage::create([
	            "product_id"    => $product->id, 	
	            "url"    => 'http://f30-preview.awardspace.net/institueproject.com/images/product/'.$arr[$i], 	
                "IsMain" => 0

	            ]);
            array_push($Productimage_arr,$Productimage);
        }
        return $Productimage_arr;
     
    } 
}
