<?php

namespace Modules\Overtimes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOvertimeRequest extends FormRequest
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
            'overtime_date' => [
                'required',
            ],
            'details' => [
                'required',
            ],
            'details.*.rates.*' => [
                'required',
                'min:0',
                'max:24',
            ],
        ];
    }
}
