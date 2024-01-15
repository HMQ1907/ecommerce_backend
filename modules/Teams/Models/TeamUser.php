<?php

namespace Modules\Teams\Models;

use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    protected $table = 'team_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
