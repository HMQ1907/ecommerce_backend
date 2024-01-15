<?php

namespace Modules\Users\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'allowed_notification',
        'allowed_location',
        'latest_platform',
        'platform_version',
    ];

    protected $casts = [
        'allowed_notification' => 'boolean',
        'allowed_location' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
