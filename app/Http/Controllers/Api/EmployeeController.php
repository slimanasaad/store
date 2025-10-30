<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\ModelHasRole;
use App\Models\Center;

class EmployeeController extends Controller
{

    /*
        public function store(Request $request)
    {
        // نفترض أن المستخدم الحالي هو المدير (مسجل الدخول)
        $token = $request->bearerToken();
        $token_info = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $user = $token_info->tokenable;
        return $user;
        $centerId = auth()->user()->center_id;

        // جلب الدور داخل نفس المركز
        $role = Role::where('center_id', $centerId)
                    ->where('name', $request->role_name)
                    ->first();

        if (!$role) {
            throw ValidationException::withMessages([
                'role_name' => ['الدور غير موجود أو لا يتبع نفس المركز.']
            ]);
        }

        // إنشاء الموظف
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'center_id' => $centerId,
            'is_center_admin' => false,
        ]);

        // ربطه بالدور
        $employee->assignRole($role);

        $token = $employee->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $employee,
        ], 201);


        return response()->json([
            'message' => 'تم إنشاء الموظف وربطه بالدور بنجاح.',
            'user' => $employee
        ], 201);
    }

    */
    public function create_account(Request $request)
    {
        // تحقق من صحة البيانات
        /*
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_name' => 'required|string',
        ]);
        */
            try {
                $request->validate([
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|unique:users,phone',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'error' => 1,
                    'message' => 'البيانات غير صحيحة.',
                    'errors' => $e->errors(),
                ], 422);
            }
        // إنشاء الموظف
        $employee = User::create([
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_center_admin' => false,
        ]);
        // إنشاء التوكن
        $token = $employee->createToken('auth_token')->plainTextToken;


        return response()->json([
            'error'=> 0,
            'message' => 'تم إنشاء الحساب بنجاح.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $employee,
        ], 201);
    }

    public function grant_role(Request $request){
        $model_type = 'App\Models\User';
        $ModelHasRole = ModelHasRole::where([['role_id',$request->role_id],['model_id',$request->emp_id],['center_id',$request->center_id]])->get();
        if(count($ModelHasRole) > 0){
            return response()->json([
                'error'=>1,
                'message' => 'هذا المستخدم بالفعل يملك هذا الدور , اختر دور أخر',
            ], 405);
        }
        $role = ModelHasRole::create([
            'role_id' => $request->role_id,
            'model_type' => $model_type,
            'model_id' => $request->emp_id,
            'center_id'=>$request->center_id
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
        ])->latest()->get();
            

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
        public function EmpByCenter(Request $request)
        {
            $centerId = $request->center_id;

            // جلب جميع المستخدمين المرتبطين بالمركز
            $modelIds = ModelHasRole::select('model_id')
                ->where('center_id', $centerId)
                ->where('model_type', User::class)
                ->groupBy('model_id')
                ->pluck('model_id');

            // جلب المستخدمين مع أدوارهم في هذا المركز فقط
            $employees = User::with(['image','roles' => function ($query) use ($centerId) {
                $query->wherePivot('center_id', $centerId);
            }])->whereIn('id', $modelIds)->get();

            return response()->json([
                'error' => 0,
                'message' => 'جميع الموظفين.',
                'employees' => $employees,
            ], 200);
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
