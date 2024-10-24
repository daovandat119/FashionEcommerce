<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
//
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'Username' => 'required|string|max:255',
            'Email' => 'required|email|unique:users,email',
            'Password' => 'required|min:6',
        ];
    }
    public function messages()
    {
        return [
            'Username.required' => 'Tên là bắt buộc.',
            'Email.required' => 'Email là bắt buộc.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.unique' => 'Email đã tồn tại.',
            'Password.required' => 'Mật khẩu là bắt buộc.',
            'Password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',

        ];
    }

    public function attributes()
    {
        return [
            'Username' => 'Tên',
            'Email' => 'Email',
            'Password' => 'Mật khẩu',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }


}
