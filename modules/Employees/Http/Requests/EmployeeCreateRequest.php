<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeCreateRequest extends FormRequest
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
            'phone' => [
                'nullable',
                'min:10',
                'max:15',
                'unique:employees',
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
            'user.email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            'bank_accounts.*.account_holder_name' => [
                'nullable',
                'max:100',
                'required_with:bank_accounts.*.account_number,bank_accounts.*.bank_name',
            ],
            'bank_accounts.*.account_number' => [
                'nullable',
                'max:100',
                'required_with:bank_accounts.*.account_holder_name,bank_accounts.*.bank_name',
            ],
            'bank_accounts.*.bank_name' => [
                'nullable',
                'max:100',
                'required_with:bank_accounts.*.account_holder_name,bank_accounts.*.account_number',
            ],
            'contracts.*.contract_from' => [
                'nullable',
                'date',
                'required_with:contracts.*.number',
            ],
            'contracts.*.contract_to' => [
                'nullable',
                'date',
                'required_with:contracts.*.number',
            ],
            'contracts.*.type' => [
                'nullable',
                'max:100',
                'required_with:contracts.*.number',
            ],
            'contracts.*.number' => [
                'nullable',
                'max:100',
                'required_with:contracts.*.contract_from,contracts.*.contract_to',
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
            'bank_accounts.*.account_holder_name' => trans('employees::common.bank_accounts.*.account_holder_name'),
            'bank_accounts.*.account_number' => trans('employees::common.bank_accounts.*.account_number'),
            'bank_accounts.*.bank_name' => trans('employees::common.bank_accounts.*.bank_name'),
            'contracts.*.number' => trans('employees::common.contracts.*.number'),
            'contracts.*.contract_from' => trans('employees::common.contracts.*.contract_from'),
            'contracts.*.contract_to' => trans('employees::common.contracts.*.contract_to'),
            'contracts.*.type' => trans('employees::common.contracts.*.type'),
        ];
    }

    public function messages()
    {
        return [
            'bank_accounts.*.account_holder_name.required_with' => 'The account holder name field is required when account number and bank name are present.',
            'bank_accounts.*.account_number.required_with' => 'The account number field is required when account holder name and bank name are present.',
            'bank_accounts.*.bank_name.required_with' => 'The bank name field is required when account holder name and account number are present.',
            'contracts.*.contract_from.required_with' => 'The contract from field is required when contract number is present.',
            'contracts.*.contract_to.required_with' => 'The contract to field is required when contract number is present.',
            'contracts.*.type.required_with' => 'The contract type field is required when contract number is present.',
        ];
    }
}
