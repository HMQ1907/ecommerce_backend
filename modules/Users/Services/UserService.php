<?php

namespace Modules\Users\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Users\Events\UserCreated;
use Modules\Users\Repositories\UserRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsers(array $params)
    {
        return QueryBuilder::for(User::class)
            ->allowedFilters(
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->where(function ($query) use ($q) {
                        $query->where('name', 'LIKE', "%{$q}%");
                    });
                }, null, ''),
                AllowedFilter::callback('roles', function (Builder $query, $roles) {
                    $query->whereHas('roles', function ($query) use ($roles) {
                        return $query->where('id', $roles);
                    });
                }, null, ''),
            )
            ->allowedSorts('name', 'email', 'created_at', 'updated_at')
            ->defaultSort('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createUser(array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->userRepository->create([
                'branch_id' => $attrs['branch_id'],
                'name' => $attrs['name'],
                'email' => $attrs['email'],
                'password' => bcrypt($attrs['password']),
                'account_type' => data_get($attrs, 'account_type', User::TYPE_EMPLOYEE),
            ]);

            $roles = data_get($attrs, 'roles');
            if (!empty($roles)) {
                if (!is_array($roles)) {
                    $roles = [$attrs['roles']];
                }

                $data->syncRoles($roles);
                $data->syncPermissions($data->getAllPermissions()->pluck('id')->toArray());
            }

            event(new UserCreated($data, data_get($attrs, 'target_id')));

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editUser($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];
            if (isset($attrs['branch_id'])) {
                $values['branch_id'] = $attrs['branch_id'];
            }
            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }
            if (isset($attrs['email'])) {
                $values['email'] = $attrs['email'];
            }
            if (isset($attrs['password'])) {
                $values['password'] = bcrypt($attrs['password']);
            }

            $user = $this->userRepository->update($values, $id);

            $roles = data_get($attrs, 'roles');
            if (!empty($roles)) {
                if (!is_array($roles)) {
                    $roles = [$attrs['roles']];
                }

                $user->syncRoles($roles);
                $user->syncPermissions($user->getAllPermissions()->pluck('id')->toArray());
            }

            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function changeUserPassword($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->update([
                'password' => bcrypt($attrs['password']),
            ], $id);

            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function syncUserRoles($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->userRepository->find($id);

            $values = $attrs['roles'];
            if (!is_array($values)) {
                $values = [$attrs['roles']];
            }

            $data->syncRoles($values);
            $data->syncPermissions($data->getAllPermissions()->pluck('id')->toArray());

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getPermissions($id)
    {
        $data = $this->userRepository->find($id);

        return $data->getDirectPermissions()->pluck('id')->toArray();
    }

    public function syncUserPermissions($id, array $attrs)
    {
        try {
            DB::beginTransaction();

            $data = $this->userRepository->find($id);

            $data->syncPermissions($attrs['permissions']);

            DB::commit();

            return $data->getDirectPermissions()->pluck('id')->toArray();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function toggleStatus($id)
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->find($id);

            if ($user->status == User::STATUS_INACTIVE) {
                $user->status = User::STATUS_ACTIVE;
            } else {
                $user->status = User::STATUS_INACTIVE;
            }

            $user->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function updateUserSetting(array $attrs)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            $values = [];

            if (isset($attrs['allowed_notification'])) {
                $values['allowed_notification'] = $attrs['allowed_notification'];
            }
            if (isset($attrs['allowed_location'])) {
                $values['allowed_location'] = $attrs['allowed_location'];
            }
            if (isset($attrs['latest_platform'])) {
                $values['latest_platform'] = $attrs['latest_platform'];
            }
            if (isset($attrs['platform_version'])) {
                $values['platform_version'] = $attrs['platform_version'];
            }

            $user->setting()->updateOrCreate([
                'user_id' => $user->id,
            ], $values);

            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
