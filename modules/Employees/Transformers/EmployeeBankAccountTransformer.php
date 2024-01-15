<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;

class EmployeeBankAccountTransformer extends BaseTransformer
{
    /**
     * Transform the EmployeeBankAccount entity.
     *
     * @return array
     */
    public function transform($model)
    {
        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'account_holder_name' => $model->account_holder_name,
            'account_number' => $model->account_number,
            'bank_name' => $model->bank_name,
            'bank_identifier_code' => $model->bank_identifier_code,
            'branch_location' => $model->branch_location,
            'tax_payer_id' => $model->tax_payer_id,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
        ];
    }
}
