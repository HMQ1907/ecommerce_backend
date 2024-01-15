<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            'designation_id' => [
                'nullable',
            ],
            'branch_id' => [
                'required',
            ],
            'employee_code' => [
                'nullable',
                'max:100',
            ],
            'date_to_company' => [
                'nullable',
                'date',
            ],
            'type' => [
                'required',
            ],
            'allowance' => [
                'nullable',
                'between:0,9999.99',
            ],
            'indicator' => [
                'nullable',
                'max:255',
            ],
            'date_to_job' => [
                'nullable',
                'date',
            ],
            'date_of_engagement' => [
                'nullable',
                'date',
            ],
            'job' => [
                'nullable',
                'integer',
            ],
            'jg' => [
                'nullable',
                'integer',
            ],
            'service' => [
                'nullable',
                'numeric',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'user.roles' => trans('employees::common.user.roles'),
        ];
    }
}
