<?php

namespace App\Services\Center;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * إنشاء حساب لصاحب مركز جديد.
     *
     * @param array $data
     * @return array
     */
    /*public function register(array $data): array
    {
        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'password'        => Hash::make($data['password']),
            'is_center_admin' => true, // حسب منطقك في التمييز
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }*/
        
        
    public function register(array $data): array
    {
        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'password'        => Hash::make($data['password']),
            'is_center_admin' => $data['is_center_admin'], 
            'image_id' => $data['image_id'],     
        ]);
        
		$profile = User::with([
                'image'
        ])->where('id',$user->id)->get();
            
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $profile,
            'token' => $token,
        ];
    }        
        
        
    public function attemptLogin(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ];
        }

        $user = Auth::user();
        
         
		$profile = User::with([
                'image'
        ])->where('id',$user->id)->get();           
           
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'user' => $profile,
            'token' => $token,
        ];
    }        
}
