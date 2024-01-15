<?php

namespace Modules\Teams\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

trait UsedByTeams
{
    /**
     * Boot the global scope.
     */
    protected static function bootUsedByTeams()
    {
        static::addGlobalScope('team', function (Builder $builder) {
            // only currentTeam
            // if (auth()->user()->currentTeam) {
            //     $builder->where($builder->getQuery()->from.'.team_id', auth()->user()->currentTeam->getKey());
            // }
            // all belonging to teams
            // $builder->where(function ($query) {
            //     $query->orWhereIn('team_id', auth()->user()->teams->pluck('id'));
            // });
        });

        static::saving(function (Model $model) {
            if (!app()->runningInConsole()) {
                if (!isset($model->team_id)) {
                    if (auth()->user()->currentTeam) {
                        $model->team_id = auth()->user()->currentTeam->getKey();
                    }
                }
            }
        });
    }

    /**
     * @return Builder
     */
    public function scopeAllTeams(Builder $query)
    {
        return $query->withoutGlobalScope('team');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Config::get('teamwork.team_model'));
    }
}
