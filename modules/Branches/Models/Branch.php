<?php

namespace Modules\Branches\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employees\Models\Employee;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
