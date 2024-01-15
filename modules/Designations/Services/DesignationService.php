<?php

namespace Modules\Designations\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Modules\Designations\Exceptions\PositionAssignedEmployeeException;
use Modules\Designations\Exceptions\PositionEmployeeTransferException;
use Modules\Designations\Models\Designation;
use Modules\Designations\Repositories\DesignationRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DesignationService extends BaseService
{
    protected $designationRepository;

    public function __construct(DesignationRepository $designationRepository)
    {
        $this->designationRepository = $designationRepository;
    }

    public function getDesignations(array $params)
    {
        return QueryBuilder::for(Designation::allData())
            ->allowedFilters([
                AllowedFilter::callback('q', function ($query, $q) {
                    return $query->where('name', 'LIKE', "%$q%");
                }, null, ''),
            ])
            ->allowedSorts(['created_at', 'name'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createDesignation(array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];

            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }
            if (isset($attrs['code'])) {
                $values['code'] = $attrs['code'];
            }
            if (isset($attrs['description'])) {
                $values['description'] = $attrs['description'];
            }

            $data = $this->designationRepository->create($values);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getDesignation($id)
    {
        return $this->designationRepository->find($id);
    }

    public function updateDesignation($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];

            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }
            if (isset($attrs['code'])) {
                $values['code'] = $attrs['code'];
            }
            if (isset($attrs['description'])) {
                $values['description'] = $attrs['description'];
            }

            $data = $this->designationRepository->update($values, $id);

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteDesignation($id)
    {
        try {
            DB::beginTransaction();

            $designation = $this->designationRepository->find($id);
            $employees = $designation->employees;

            if ($employees->count() > 0) {
                throw new PositionAssignedEmployeeException($designation);
            }

            if ($designation->employeeTransfersFrom->count() > 0 || $designation->employeeTransfersTo->count() > 0) {
                throw new PositionEmployeeTransferException($designation);
            }

            $this->designationRepository->delete($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteDesignations(array $ids)
    {
        try {
            DB::beginTransaction();

            $designations = $this->designationRepository->findWhere([
                ['id', 'IN', $ids],
            ]);

            foreach ($designations as $designation) {
                $employees = $designation->employees;

                if ($employees->count() > 0) {
                    throw new PositionAssignedEmployeeException($designation);
                }

                if ($designation->employeeTransfersFrom->count() > 0 || $designation->employeeTransfersTo->count() > 0) {
                    throw new PositionEmployeeTransferException($designation);
                }
            }

            $this->designationRepository->deleteWhere([
                ['id', 'IN', $ids],
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
