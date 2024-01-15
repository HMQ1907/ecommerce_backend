<?php

namespace Modules\Employees\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Exceptions\RetaliationExistsException;
use Modules\Employees\Models\Retaliation;
use Modules\Employees\Repositories\RetaliationRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RetaliationService extends BaseService
{
    protected $retaliationRepository;

    public function __construct(RetaliationRepository $retaliationRepository)
    {
        $this->retaliationRepository = $retaliationRepository;
    }

    public function getRetaliations(array $params)
    {
        return QueryBuilder::for(Retaliation::class)
            ->with('employee')
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->whereHas('employee', function (Builder $query) use ($q) {
                        $query->searchName($q);
                    });
                }, null, ''),
                AllowedFilter::callback('apply_salary_month', function (Builder $query, $month) {
                    $query->where(function ($query) use ($month) {
                        $query->whereMonth('apply_salary_date', Carbon::parse($month)->month)
                            ->whereYear('apply_salary_date', Carbon::parse($month)->year);
                    });
                }),
                AllowedFilter::callback('increment_month', function (Builder $query, $month) {
                    $query->where(function ($query) use ($month) {
                        $query->whereMonth('increment_date', Carbon::parse($month)->month)
                            ->whereYear('increment_date', Carbon::parse($month)->year);
                    });
                }),
                AllowedFilter::exact('employee_id'),
            ])
            ->defaultSort('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getRetaliation($id)
    {
        return $this->retaliationRepository->find($id);
    }

    public function createRetaliation(array $attrs)
    {
        try {
            DB::beginTransaction();

            if ($this->retaliationExistInMonth($attrs['employee_id'], $attrs['increment_date'])) {
                $incrementMonth = Carbon::parse($attrs['increment_date'])->format('m');
                throw new RetaliationExistsException($incrementMonth);
            }

            $retaliation = $this->retaliationRepository->create([
                'employee_id' => $attrs['employee_id'],
                'apply_salary_date' => $attrs['apply_salary_date'],
                'previous_salary' => $attrs['previous_salary'],
                'increment_date' => $attrs['increment_date'],
                'new_salary' => $attrs['new_salary'],
            ]);

            DB::commit();

            return $retaliation;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function retaliationExistInMonth($employeeId, $incrementDate)
    {
        return Retaliation::query()
            ->where('employee_id', $employeeId)
            ->whereMonth('increment_date', '=', date('m', strtotime($incrementDate)))
            ->whereYear('increment_date', '=', date('Y', strtotime($incrementDate)))
            ->count() > 0;
    }

    public function editRetaliation($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];
            if (isset($attrs['employee_id'])) {
                $values['employee_id'] = $attrs['employee_id'];
            }
            if (isset($attrs['apply_salary_date'])) {
                $values['apply_salary_date'] = $attrs['apply_salary_date'];
            }
            if (isset($attrs['increment_date'])) {
                $values['increment_date'] = $attrs['increment_date'];
            }
            if (isset($attrs['previous_salary'])) {
                $values['previous_salary'] = $attrs['previous_salary'];
            }
            if (isset($attrs['new_salary'])) {
                $values['new_salary'] = $attrs['new_salary'];
            }

            $data = $this->retaliationRepository->update($values, $id);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteRetaliation($id)
    {
        try {
            DB::beginTransaction();

            $this->retaliationRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteRetaliations(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->retaliationRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
