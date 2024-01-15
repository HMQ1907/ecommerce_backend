<?php

namespace Modules\Employees\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employees\Models\EmployeeTerminationAllowance;

class EmployeeTerminationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EmployeeTerminationAllowance $employeeTerminationAllowance)
    {
        if ($user->can('employee_terminations.view')) {
            return $user->employee->id == $employeeTerminationAllowance->employee_id
                || $employeeTerminationAllowance->isCreator();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->can('employee_terminations.create')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EmployeeTerminationAllowance $employeeTerminationAllowance)
    {
        if ($user->can('employee_terminations.edit')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EmployeeTerminationAllowance $employeeTerminationAllowance)
    {
        if ($user->can('employee_terminations.delete')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EmployeeTerminationAllowance $employeeTerminationAllowance)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EmployeeTerminationAllowance $employeeTerminationAllowance)
    {
        //
    }
}
