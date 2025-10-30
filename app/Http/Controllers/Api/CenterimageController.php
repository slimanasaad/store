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

use Illuminate\Support\Facades\DB;


class CenterimageController extends Controller
{
    public function store(Request $request,$center,$arr,$MainImage){
        if(!$center || !$arr || !$MainImage){
            return;
        }
            
        $CenterMainImage = Centerimage::create([
                "center_id"    => $center->id, 	
                "url"    => 'http://f30-preview.awardspace.net/institueproject.com/images/center/'.$MainImage, 	
                "IsMain" => 1
        ]);
        $Centerimage_arr = array();
        array_push($Centerimage_arr,$MainImage);
        for ($i=0; $i<count($arr); $i++){
	        $Centerimage = Centerimage::create([
	            "center_id"    => $center->id, 	
	            "url"    => 'http://f30-preview.awardspace.net/institueproject.com/images/center/'.$arr[$i], 	
                "IsMain" => 0

	            ]);
            array_push($Centerimage_arr,$Centerimage);
        }
        return $Centerimage_arr;
     
    } 
}
