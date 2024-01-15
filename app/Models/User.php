<?php

namespace App\Models;

use App\Notifications\Contracts\CanBeNotifiable;
use App\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Modules\Auth\Models\OAuthProvider;
use Modules\Auth\Notifications\EmailVerification;
use Modules\Auth\Notifications\PasswordReset;
use Modules\Roles\Models\Role;
use Modules\Users\Models\Device;
use Modules\Users\Models\UserSetting;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanBeNotifiable, HasMedia //implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasRoles;
    use InteractsWithMedia;

    const ADMIN = 'admin';

    const USER = 'user';

    const TYPE_CUSTOMER = 'customer';

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'password',
        'account_type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAvatarUrlAttribute()
    {
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return !empty($this->employee->avatar) ? Storage::url($this->employee->avatar) : null;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerification());
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->devices()->pluck('token')->all();
    }

    /**
     * Return the notification icon as creator.
     *
     * @return string
     */
    public function getNotificationIcon(Notification $notification)
    {
        return $this->avatar_url;
    }

    public function isAdmin()
    {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Get the oauth providers.
     *
     * @return HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function setting()
    {
        return $this->hasOne(UserSetting::class);
    }
}
