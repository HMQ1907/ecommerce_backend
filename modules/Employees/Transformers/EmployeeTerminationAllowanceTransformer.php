<?php

namespace Modules\Employees\Transformers;

use App\Transformers\BaseTransformer;

class EmployeeTerminationAllowanceTransformer extends BaseTransformer
{
    /**
     * Include resources without needing it to be requested.
     */
    protected array $defaultIncludes = [
        'employee',
    ];

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
            'subject' => $model->subject,
            'type' => $model->type,
            'notice_date' => $model->notice_date,
            'termination_date' => $model->termination_date,
            'terminated_by' => $model->terminated_by,
            'terminated_by_name' => optional($model->terminatedByName)->full_name,
            'status' => $model->status,
            'description' => $model->description,
            'retirement_fund' => $model->employee->retirement_fund,
            'currency_code' => $model->employee->currency_code,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'remaining_vacation_days' => $model->remaining_vacation_days,
            'vacation_fund' => $model->vacation_fund,
        ];
    }

    public function includeEmployee($model)
    {
        return $this->item($model->employee, new EmployeeTransformer());
    }
}
