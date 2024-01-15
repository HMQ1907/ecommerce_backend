<?php

namespace Modules\Employees\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Models\EmployeeContract;
use Modules\Employees\Repositories\EmployeeContractRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeContractService extends BaseService
{
    public function __construct(EmployeeContractRepository $employeeContractRepository)
    {
        $this->employeeContractRepository = $employeeContractRepository;
    }

    public function getEmployeeContracts(array $params)
    {
        return QueryBuilder::for(EmployeeContract::class)
            ->allowedFilters([
                AllowedFilter::callback('date', function (Builder $query, $date) {
                    $query->whereDate('contract_from', '<=', $date)
                        ->whereDate('contract_to', '>=', $date);
                }),
            ])
            ->defaultSort('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getEmployeeContractByType($type)
    {
        return QueryBuilder::for(EmployeeContract::class)
            ->with('employee')
            ->whereHas('employee', function (Builder $query) {
                $query->where('branch_id', auth()->user()->branch_id);
            })
            ->where('type', $type)
            ->get();
    }

    public function createEmployeeContract(array $attrs)
    {
        try {
            DB::beginTransaction();

            $value = [
                'employee_id' => $attrs['employee_id'],
                'type' => $attrs['type'],
                'number' => $attrs['number'],
                'contract_from' => $attrs['contract_from'],
                'contract_to' => $attrs['contract_to'],
                'created_at' => Carbon::now(),
            ];

            $employeeContract = $this->employeeContractRepository->create($value);

            $contractFileIds = data_get($attrs, 'contract_file', '');
            if ($contractFileIds) {
                if (!is_array($contractFileIds)) {
                    $contractFileIds = explode(',', $contractFileIds);
                }
            } else {
                $contractFileIds = [];
            }

            $employeeContract->syncMedia($contractFileIds, 'contract');

            DB::commit();

            return $employeeContract;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getEmployeeContract($id)
    {
        return $this->employeeContractRepository->find($id);
    }

    public function editEmployeeContract($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $employeeContract = $this->employeeContractRepository->find($id);

            $value = [
                'type' => $attrs['type'],
                'number' => $attrs['number'],
                'contract_from' => $attrs['contract_from'],
                'contract_to' => $attrs['contract_to'],
                'updated_at' => Carbon::now(),
            ];

            $employeeContract = $this->employeeContractRepository->update($value, $id);

            $contractFileIds = data_get($attrs, 'contract_file', []);
            if ($contractFileIds) {
                if (!is_array($contractFileIds)) {
                    $contractFileIds = explode(',', $contractFileIds);
                }

                // Add new media without removing old media
                foreach ($contractFileIds as $mediaId) {
                    $media = Media::find($mediaId);
                    $media->model_id = $employeeContract->id;
                    $media->model_type = EmployeeContract::class;
                    $media->collection_name = 'contract';
                    $media->save();
                }
            }

            DB::commit();

            return $employeeContract;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteEmployeeContract($id)
    {
        try {
            DB::beginTransaction();

            $this->employeeContractRepository->deleteEmployeeContract($id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getEmployeeContractByEmployeeId($employeeId)
    {
        return $this->employeeContractRepository->findByField('employee_id', $employeeId)->last();
    }

    public function getFiles($id, array $params)
    {
        $employeeContract = $this->employeeContractRepository->findByField('employee_id', $id)->last();

        if (empty($employeeContract)) {
            return [];
        }

        return QueryBuilder::for(Media::class)
            ->where('model_id', $employeeContract->id)
            ->where('model_type', EmployeeContract::class)
            ->where('collection_name', 'contract')
            ->allowedSorts(['created_at'])
            ->defaultSorts(['-created_at'])
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function deleteFile($id, $fileId)
    {
        $employeeContract = $this->employeeContractRepository->findByField('employee_id', $id)->last();

        $media = Media::find($fileId);
        $media->delete();

        return $employeeContract;
    }
}
