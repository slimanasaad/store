<?php

namespace App\Http\Controllers\Api\Center;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Center;
use App\Models\User;
use App\Models\Userimage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterCenterRequest;
use App\Http\Requests\LoginRequest;
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
    
                $data['is_center_admin'] = 1;
                $result = $service->register($data);
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
        

        public function login(LoginRequest $request, AuthService $service)
        {
            $result = $service->attemptLogin($request->only('phone', 'password'));
               
                

                
            if (!$result['success']) {
                return response()->json([
                    'error' => 1,
                    'message' => $result['message'],
                ], 401);
            }

			$user = auth()->user();
            $roles = $user->roles()->withPivot('center_id')->get();
			//return $roles;
                $rolesWithCenters = $roles->map(function ($role) {
                    $center = \App\Models\Center::find($role->pivot->center_id);

                    return [
                        'role_name' => $role->name,
                        'center_id' => $role->pivot->center_id,
                        'center' => $center, // ← معلومات المركز
                    ];
                });
                
               
            return response()->json([
                'error' => 0,
                'message' => 'تم تسجيل الدخول بنجاح.',
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'user' => $result['user'][0],
                'roles'=> $rolesWithCenters 
            ]);
        }

    public function logout(Request $request)
    {
        // حذف التوكن الحالي فقط (وليس كل التوكنات)
        //$request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete(); // يحذف كل التوكنات
        return response()->json([
            'error'=>0,
            'message' => 'تم تسجيل الخروج بنجاح.'
        ]);
    }

}
