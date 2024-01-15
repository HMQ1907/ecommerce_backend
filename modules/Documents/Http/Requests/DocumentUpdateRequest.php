<?php

namespace Modules\Documents\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUpdateRequest extends FormRequest
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
            'category_id' => [
                'required',
                'exists:document_categories,id',
            ],
            'name' => [
                'required',
                'max:50',
            ],
            'document_number' => [
                'unique:documents,document_number,'.$this->id,
                'max:50',
            ],
        ];
    }
}
