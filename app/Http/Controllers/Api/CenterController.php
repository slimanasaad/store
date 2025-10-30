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


class CenterController extends Controller
{
    public function store(Request $request){                        
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
            // إنشاء مركز جديد

        $center = Center::create([
            'name' => $request->center_name,
            'location' => $request->center_location,
            'owner_id' => $user->id,
        ]);
            
        $image = $request->file('image');
            
        if ($image = $request->file('image')  ) {
            $destinationPath = '../../institueproject.com/images/center/';
            $profileImage = date('YmdHis').".".$image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $CenterImage = Centerimage::create([
                "center_id"    => $center->id, 	
                "url"    => 'http://f30-preview.awardspace.net/institueproject.com/images/center/'.$profileImage, 	
        	]);
        }
        /*        
        DB::table('permissions')->insert([
                [
                    'name' => 'view_inventory',
                    'guard_name' => 'web',
                    'center_id' => $center->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'edit_inventory',
                    'guard_name' => 'web',
                    'center_id' => $center->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'view_sales',
                    'guard_name' => 'web',
                    'center_id' => $center->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'manage_employees',
                    'guard_name' => 'web',
                    'center_id' => $center->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
    	]);
        */
        /*
        DB::table('roles')->insert([
        			'name' => 'center_admin',
            		'guard_name' => 'web',
            		'center_id' => $center->id,
                    'created_at' => now(),
                    'updated_at' => now(),
        ]);
        */
        /*    
        $role = Role::where('center_id',$center->id)->first();
        $permissions = Permission::where('center_id',$center->id)->get();
        $insertedIds = [];
        foreach ($permissions as $permission) {
        	$insertedIds[] = $permission->id;
        }
        foreach ($insertedIds as $permissionId) {
            RoleHasPermission::create([
                'permission_id' => $permissionId,
            	'role_id' => $role->id
        	]);
    	}
        */
        $role = Role::where('name','center_admin')->first();            
		DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
         		'center_id' => $center->id,

        ]);
            
        
        return response()->json([
            'error' =>0,
            'message' => 'تم إنشاء المركز بنجاح.',
            'center' => $center,
        ], 201);
            
    }
        
    public function centers(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
            
        $center = Center::with([
            'owner',
            'center_image',
        ])->where('owner_id', $user->id)->latest()->get();            
            
        //$center = Center::where('owner_id', $user->id)->get();
            
        
        return response()->json([
            'error'=>0,
            'message' => 'المراكز الخاصة بك.',
            'center' => $center,
        ], 201);
            
    }        
        
        
    public function info(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;  
            
        $center = Center::with([
            'owner',
            'center_image',
        ])->where('id', $request->center_id)->latest()->get();            
                        
        
        return response()->json([
            'error'=>0,
            'message' => 'المراكز الخاصة بك.',
            'center' => $center,
        ], 201);
            
    }             
        
}
