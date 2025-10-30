<?php

namespace App\Http\Controllers\Api\Employee;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\ModelHasRole;
use App\Models\Center;
use App\Models\Userimage;

use App\Services\Center\AuthService;

class AuthController extends Controller
{

    public function register(Request $request, AuthService $service)
    {

            try {
                $data = $request->validate([
                    'name' => 'required|string|min:3|max:100',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|string|unique:users,phone',
                    'password' => 'required|string|min:6',
                ]);
		    	$data = $request->only(['name', 'email', 'phone', 'password']);
   				$data = $request->all();
                    
                $data['image_id'] = 1;

                $image = $request->file('image');

                if ($image = $request->file('image')  ) {
                    $destinationPath = '../../institueproject.com/images/profile/';
                    $profileImage = date('YmdHis').".".$image->getClientOriginalExtension();
                    $image->move($destinationPath, $profileImage);
                    $Userimage = Userimage::create([
                        "url"    => 'http://f30-preview.awardspace.net/institueproject.com/images/profile/'.$profileImage, 	
                    ]);
                    $data['image_id'] = $Userimage->id;
                }                      
                    
                $data['is_center_admin'] = 0;
                $result = $service->register($data);
                return $result['user'][0];
                return response()->json([
                    'error' => 0,
                    'message' => 'تم إنشاء الحساب بنجاح.',
                    'access_token' => $result['token'],
                    'token_type' => 'Bearer',
                    'user' => $result['user'][0],
                ], 201);                    
                    
                    
            } catch (ValidationException $e) {
                return response()->json([
                    'error' => 1,
                    'message' => 'البيانات غير صحيحة.',
                    'errors' => $e->errors(),
                ], 422);
            }

    }


    public function grant_role(Request $request){
        $model_type = 'App\Models\User';
        $role = ModelHasRole::create([
            'role_id' => $request->role_id,
            'model_type' => $model_type,
            'model_id' => $request->emp_id,
        ]);

        return response()->json([
            'error'=>0,
            'message' => 'تم منح الدور بنجاح.',
            'role' => $role,
        ], 201);
    }

    public function show_roles(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable; 
        if($user->is_center_admin != 1){
            return response()->json([
                'error'=>1,
                'message' => 'لا يمكنك ذلك.',
            ], 405);
        }
        $roles = Role::with([
            'permissions',
        ])->where('center_id',$request->center_id)->latest()->get();
            

        //$roles = Role::where('center_id',$request->center_id)->get();
        //$permissions = $roles->permissions;
        return response()->json([
            'error'=>0,
            'message' => 'الأدوار الموجودة في هذا المركز.',
            'roles' => $roles,
        ], 201);
    }
        
        
        
        
    public function show_employees(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;    
        if($user->is_center_admin != 1){
            return response()->json([
                 'error'=>1,
                'message' => 'لا يمكنك ذلك.',
            ], 405);
        }
            
            
        $employees = User::with([
            'roles',
            'centers',
            'image'
        ])->where('is_center_admin','0')->latest()->get();
                  
        //$employees = User::where('is_center_admin','0')->get(); 
            
        return response()->json([
            'error'=>0,
            'message' => 'جميع الموظفين.',
            'employees' => $employees,
        ], 201);
    }
        
        
/*
    public function show_employees(Request $request){
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;    
        if($user->is_center_admin != 1){
            return response()->json([
                 'error'=>1,
                'message' => 'لا يمكنك ذلك.',
            ], 405);
        }
        $arr = array();           
        $employees = User::where('is_center_admin','0')->get();
        foreach ($employees as $employee) {
                $hasAnyRole = $employee->roles()->exists();
                if(!$hasAnyRole){
		            array_push($arr,$employee);
                
                }
        }    
            
            
        return response()->json([
            'error'=>0,
            'message' => 'جميع الموظفين.',
            'employees' => $arr,
        ], 201);
        
        //        $roles = $user->roles;

        return $arr;
    }
*/
}
