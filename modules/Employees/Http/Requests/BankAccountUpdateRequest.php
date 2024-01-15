<?php

namespace Modules\Employees\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountUpdateRequest extends FormRequest
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
            'bank_account.*.account_holder_name' => [
                'max:100',
            ],
            'bank_account.*.account_number' => [
                'max:100',
            ],
            'bank_account.*.bank_name' => [
                'max:100',
            ],
            'bank_account.*.bank_identifier_code' => [
                'max:100',
            ],
            'bank_account.*.branch_location' => [
                'max:100',
            ],
            'bank_account.*.tax_payer_id' => [
                'max:100',
            ],
        ];
    }
}
