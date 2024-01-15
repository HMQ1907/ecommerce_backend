<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

abstract class BaseModel extends Model
{
    public function isCreator(): bool
    {
        return auth()->id() == $this->created_by;
    }

    public function isTeamOwner($user): bool
    {
        return auth()->user()->isOwnerOfTeam($user->current_team_id);
    }

    public function isManager(): bool
    {
        $teams = auth()->user()->employee->managedDepartments->pluck('teams')->flatten()->pluck('id');

        if ($teams->count() <= 0) {
            $teams = auth()->user()->ownedTeams->pluck('id');
        }

        $user = $this->employee->user ?? $this->user;

        return $teams->contains($user->current_team_id);
    }

    public function syncMedia(array $mediaArray, string $collectionName = 'default')
    {
        if (count($mediaArray) > 0) {
            $currentMedia = $this->getMedia($collectionName, function (Media $media) use ($mediaArray) {
                return in_array($media->id, $mediaArray);
            });

            $this->attachMedia($mediaArray, $collectionName);

            $this->clearMediaCollectionExcept($collectionName, $currentMedia);
        } else {
            $this->clearMediaCollection($collectionName);
        }
    }

    public function attachMedia(array $mediaArray, string $collectionName = 'default')
    {
        $newMedias = [];

        if (count($mediaArray) > 0) {
            $medias = auth()->user()->getMedia('default', function (Media $media) use ($mediaArray) {
                return in_array($media->id, $mediaArray);
            });

            foreach ($medias as $media) {
                $newMedias[] = $media->move($this, $collectionName);
            }
        }

        return $newMedias;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!app()->runningInConsole()) {
                $model->created_by = auth()->id();
            }
        });
    }

    public function scopeCreatedAtBetween(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('created_at', [Carbon::make($from)->startOfDay(), Carbon::make($to)->endOfDay()]);
    }
}
