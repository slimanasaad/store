<?php

namespace App\Http\Controllers\Api;

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
use App\Services\FCMHttpV1Service;

class NotifyController extends Controller
{


public function sendTestNotification(Request $request)
{
    $request->validate([
        'device_token' => 'required|string'
    ]);

    $fcm = new FCMHttpV1Service();
    $result = $fcm->sendMessage(
        $request->device_token,
        'مرحبا',
        'هذه رسالة اختبار من Laravel!'
    );

    return response()->json($result);
}
}