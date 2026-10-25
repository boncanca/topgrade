<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property bool $is_admin
 * @property string $role
 * @property bool $is_system_account
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'is_admin', 'role', 'is_system_account', 'email_verified_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_system_account' => 'boolean',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'is_dev_admin',
    ];

    /**
     * Determine if user has developer/super administrator privileges.
     */
    public function isDevAdmin(): bool
    {
        return $this->role === 'dev'
            || (bool) $this->is_system_account
            || $this->email === (string) config('topgrade.dev_admin.email', 'tomc@trupabranding.com');
    }

    /**
     * Accessor for is_dev_admin attribute.
     */
    public function getIsDevAdminAttribute(): bool
    {
        return $this->isDevAdmin();
    }

    /**
     * Scope to exclude protected system accounts for normal admin views.
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system_account', false);
    }

    /**
     * Determine if this account can be managed/modified by the given user.
     */
    public function canBeManagedBy(?User $actor): bool
    {
        if ($this->is_system_account) {
            return $actor !== null && $actor->id === $this->id;
        }

        return true;
    }
}
