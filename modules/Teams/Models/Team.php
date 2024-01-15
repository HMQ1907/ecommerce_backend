<?php

namespace Modules\Teams\Models;

use Illuminate\Database\Eloquent\Builder;
use Modules\Attendances\Models\Attendance;
use Modules\Departments\Models\Department;
use Modules\Roles\Models\Role;
use Mpociot\Teamwork\TeamworkTeam as Model;

class Team extends Model
{
    public function scopeAllData(Builder $builder)
    {
        if (auth()->user()->hasRole(Role::ADMIN)) {
            return $builder;
        }

        return $builder
            ->where('owner_id', auth()->id())
            ->orWhereRelation('users', 'id', auth()->id());
    }

    public function department()
    {
        return $this->belongsToMany(Department::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all of the team's users including its owner.
     *
     * @return \Illuminate\Support\Collection
     */
    public function allUsers()
    {
        return $this->users->merge([$this->owner]);
    }

    /**
     * Determine if the given email address belongs to a user on the team.
     *
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Determine if the given user has the given permission on the team.
     *
     * @param  \App\Models\User  $user
     * @param  string  $permission
     * @return bool
     */
    public function userHasPermission($user, $permission)
    {
        return $user->hasTeamPermission($this, $permission);
    }

    /**
     * Remove the given user from the team.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function removeUser($user)
    {
        if ($user->current_team_id === $this->id) {
            $user->forceFill([
                'current_team_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Purge all of the team's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->owner()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->users()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->users()->detach();

        $this->delete();
    }
}
