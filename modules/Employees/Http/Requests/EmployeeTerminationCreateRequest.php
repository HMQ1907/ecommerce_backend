<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeTerminationCreateRequest extends FormRequest
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
            ],
            'subject' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'notice_date' => [
                'required',
            ],
            'termination_date' => [
                'required',
            ],
        ];
    }
}
