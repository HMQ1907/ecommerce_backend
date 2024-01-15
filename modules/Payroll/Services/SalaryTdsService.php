<?php

namespace Modules\Payroll\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\Models\SalaryTds;
use Modules\Payroll\Repositories\SalaryTdsRepository;
use Modules\Users\Services\UserService;
use Spatie\QueryBuilder\QueryBuilder;

class SalaryTdsService extends BaseService
{
    protected $salaryTdsRepository;

    protected $userService;

    public function __construct(SalaryTdsRepository $salaryTdsRepository, UserService $userService)
    {
        $this->salaryTdsRepository = $salaryTdsRepository;
        $this->userService = $userService;
    }

    public function getSalaryTDSs(array $params)
    {
        return QueryBuilder::for(SalaryTds::class)
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createSalaryTDS(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->salaryTdsRepository->create([
                'salary_from' => $attrs['salary_from'],
                'salary_to' => $attrs['salary_to'],
                'salary_percent' => $attrs['salary_percent'],
            ]);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editSalaryTDS($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];
            if (isset($attrs['salary_from'])) {
                $values['salary_from'] = $attrs['salary_from'];
            }
            if (isset($attrs['salary_to'])) {
                $values['salary_to'] = $attrs['salary_to'];
            }
            if (isset($attrs['salary_percent'])) {
                $values['salary_percent'] = $attrs['salary_percent'];
            }

            $data = $this->salaryTdsRepository->update($values, $id);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteSalaryTDS($id)
    {
        try {
            DB::beginTransaction();

            $this->salaryTdsRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteSalaryTDSs(array $ids)
    {
        try {
            DB::beginTransaction();

            $this->salaryTdsRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
