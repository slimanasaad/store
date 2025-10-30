<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // السماح للجميع بالطلب
    }

        public function rules(): array
        {
            return [
                'phone' => 'required|string',
                'password' => 'required|string',
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
