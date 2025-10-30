<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Center;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterCenterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\Center\AuthService;

class AuthController extends Controller
{

    public function register(Request $request)
    {
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
        $user = User::create([
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_center_admin' => true,
        ]);
        // إنشاء التوكن
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'error' => 0,
            'message' => 'تم إنشاء الحساب بنجاح.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
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
                'user' => $result['user'],
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
