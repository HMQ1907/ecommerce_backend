<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeTransferCreateRequest extends FormRequest
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
                // Rule::exists('employees', 'id')->where(function ($query) {
                //     $query->where('branch_id', $this->input('from_branch_id'));
                //     $query->where('designation_id', $this->input('from_designation_id'));
                // }),
            ],
            'to_branch_id' => [
                'required',
            ],
            'to_department_id' => [
                'required',
                // Rule::exists('departments', 'id')->where(function ($query) {
                //     $query->where('branch_id', $this->input('to_branch_id'));
                // }),
            ],
            'to_designation_id' => [
                'required',
            ],
            'description' => [
                'max:250',
            ],
            'transfer_date' => [
                'date',
                'required',
            ],
            'notice_date' => [
                'date',
                'required',
            ],
        ];
    }
}
