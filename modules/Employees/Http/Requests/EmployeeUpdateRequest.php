<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => [
                'required',
                'max:100',
            ],
            'last_name' => [
                'required',
                'max:100',
            ],
            'avatar' => [
                'max:2048',
            ],
            'address' => [
                'max:200',
            ],
            'gender' => [
                'required',
            ],
            'type' => [
                'required',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function attributes()
    {
        return [
            'user.email' => 'email',
        ];
    }
}
