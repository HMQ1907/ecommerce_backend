<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;
use Modules\Employees\Models\Retaliation;

class RetaliationEmployeeTransformer extends BaseTransformer
{
    /**
     * Transform the Employee entity.
     *
     * @return array
     */
    public function transform(Retaliation $model)
    {
        $amount = $model->original_amount;
        $months = $model->original_months;
        $amountOfMoney = $model->original_amount_of_money;

        return [
            'id' => (int) $model->id,
            'employee_id' => $model->employee_id,
            'apply_salary_date' => $model->apply_salary_date,
            'increment_date' => $model->increment_date,
            'previous_salary' => (int) $model->previous_salary,
            'new_salary' => (int) $model->new_salary,
            'currency_code' => $model->employee?->currency_code,
            'amount' => $amount,
            'months' => $months,
            'amount_of_money' => $amountOfMoney,
            'created_at' => $model->created_at,
        ];
    }
}
