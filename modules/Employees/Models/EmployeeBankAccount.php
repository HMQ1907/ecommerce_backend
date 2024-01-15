<?php

namespace Modules\Employees\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_identifier_code',
        'branch_location',
        'tax_payer_id',
    ];

    /**
     * Get the employee that owns the bank account.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
