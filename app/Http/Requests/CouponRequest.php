<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'Name' => 'required|string',
            'Code' => 'required|string|unique:coupons,Code,' . $id . ',CouponID',
            'DiscountPercentage' => 'required|numeric|min:0|max:100',
            'MinimumOrderValue' => 'required|numeric|min:0',
            'UsageLimit' => 'required|min:0',
            'ExpiresAt' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'Code.exists' => 'Coupon không tồn tại',
            'ExpiresAt.date' => 'Ngày hết hạn không hợp lệ',
            'DiscountPercentage.min' => 'Phần trăm giảm giá không hợp lệ',
            'DiscountPercentage.max' => 'Phần trăm giảm giá không hợp lệ',
            'MinimumOrderValue.min' => 'Giá trị đơn hàng tối thiểu không hợp lệ',
            'UsageLimit.min' => 'Số lượng sử dụng phải lớn hơn 0',
            'Name.required' => 'Tên không được để trống',
            'Name.string' => 'Tên không hợp lệ',
            'Code.required' => 'Mã giảm giá không được để trống',
            'Code.string' => 'Mã giảm giá không hợp lệ',
            'DiscountPercentage.required' => 'Phần trăm giảm giá không được để trống',
            'DiscountPercentage.numeric' => 'Phần trăm giảm giá không hợp lệ',
            'MinimumOrderValue.required' => 'Giá trị đơn hàng tối thiểu không được để trống',
            'MinimumOrderValue.numeric' => 'Giá trị đơn hàng tối thiểu không hợp lệ',
            'UsageLimit.required' => 'Số lượng sử dụng không được để trống',
            'ExpiresAt.required' => 'Ngày hết hạn không được để trống',
        ];
    }

    public function attributes(): array
    {
        return [
            'Code' => 'Mã giảm giá',
            'ExpiresAt' => 'Ngày hết hạn',
            'DiscountPercentage' => 'Phần trăm giảm giá',
            'MinimumOrderValue' => 'Giá trị đơn hàng tối thiểu',
            'UsageLimit' => 'Số lượng sử dụng',
            'Name' => 'Tên',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422));
    }
}
