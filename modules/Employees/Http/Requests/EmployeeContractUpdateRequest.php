<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeContractUpdateRequest extends FormRequest
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
            'contract_from' => [
                'date',
                'required',
            ],
            'contract_to' => [
                'date',
                'required',
            ],
            'type' => [
                'required',
            ],
            'number' => [
                'max:100',
                'required',
            ],
        ];
    }
}
