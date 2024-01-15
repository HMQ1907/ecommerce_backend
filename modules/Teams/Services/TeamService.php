<?php

namespace Modules\Teams\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Employees\Repositories\EmployeeRepository;
use Modules\Teams\Exceptions\UserAlreadyInTeamException;
use Modules\Teams\Models\Team;
use Modules\Teams\Repositories\TeamRepository;
use Modules\Users\Repositories\UserRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TeamService extends BaseService
{
    private $teamRepository;

    private $userRepository;

    private $employeeRepository;

    public function __construct(TeamRepository $teamRepository, UserRepository $userRepository, EmployeeRepository $employeeRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public function getAllTeams(array $params)
    {
        return QueryBuilder::for(Team::allData())
            ->with(['users'])
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->where('name', 'LIKE', "%$q%");
                }),
                AllowedFilter::callback('employee_id', function (Builder $query, $employeeId) {
                    $query->whereHas('users', function (Builder $query) use ($employeeId) {
                        $query->whereHas('employee', function (Builder $query) use ($employeeId) {
                            $query->where('id', $employeeId);
                        });
                    });
                }),
            ])
            ->allowedSorts(['created_at', 'name'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function createTeam(array $attrs)
    {
        try {
            DB::beginTransaction();

            $owner = $this->employeeRepository->find($attrs['owner_id']);

            $team = $owner->user->createOwnedTeam([
                'name' => $attrs['name'],
            ], true);

            $members = $this->employeeRepository->findWhereIn('id', $attrs['members']);

            foreach ($members as $member) {
                $member->user->attachTeam($team);
            }

            DB::commit();

            return $team;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getTeamById($id)
    {
        return $this->teamRepository->find($id);
    }

    public function updateTeam(array $attrs, $id)
    {
        try {
            DB::beginTransaction();

            $owner = $this->employeeRepository->find($attrs['owner_id']);

            $team = $this->teamRepository->update([
                'owner_id' => $attrs['owner_id'],
                'name' => $attrs['name'],
            ], $id);

            $owner->user->attachTeam($team);

            $members = data_get($attrs, 'members', []);

            if (count($members) > 0) {
                $members = $this->employeeRepository->findWhereIn('id', $members);

                foreach ($members as $member) {
                    $member->user->attachTeam($team);
                }
            }

            DB::commit();

            return $team;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteTeam($id)
    {
        return $this->teamRepository->delete($id);
    }

    public function getTeamUsers($teamId, array $params)
    {
        $team = $this->getTeamById($teamId);

        return QueryBuilder::for($team->users())
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    return $query
                        ->where('first_name', 'LIKE', "%$q%")
                        ->where('last_name', 'LIKE', "%$q%");
                }),
            ])
            ->allowedSorts(['created_at'])
            ->defaultSorts('-created_at')
            ->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function addUserToTeam($teamId, $userIds)
    {
        $team = $this->getTeamById($teamId);

        $users = $this->userRepository->findWhereIn('id', $userIds);

        foreach ($users as $user) {
            $existingUser = $team->users()->where('user_id', $user->id)->first();
            if ($existingUser) {
                throw new UserAlreadyInTeamException();
            }
            $user->attachTeam($team);
        }

        return $team;
    }

    public function deleteUserInTeam($teamId, $userId)
    {
        try {
            DB::beginTransaction();

            $team = Team::findOrFail($teamId);

            // if (!auth()->user()->isOwnerOfTeam($team)) {
            //     abort(403);
            // }

            $user = User::findOrFail($userId);

            // if ($user->getKey() === auth()->user()->getKey()) {
            //     abort(403);
            // }

            $user->detachTeam($team);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
