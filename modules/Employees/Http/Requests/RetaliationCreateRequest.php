<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetaliationCreateRequest extends FormRequest
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
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],
            'apply_salary_date' => [
                'required',
            ],
            'increment_date' => [
                'required',
            ],
            'previous_salary' => [
                'required',
            ],
            'new_salary' => [
                'required',
            ],
        ];
    }
}
