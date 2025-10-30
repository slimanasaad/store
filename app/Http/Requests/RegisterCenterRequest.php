<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // السماح للجميع بالطلب
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقًا.',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقًا.',
            'password' => 'تأكيد كلمة المرور غير متطابق.',
        ];
    }
}
