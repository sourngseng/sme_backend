<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Auditable, HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * The primary key type and incrementing setting.
     *
     * @var string
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'avatar',
        'bio',
        'notification_preferences',
        'password',
        'password_hash',
        'status',
        'last_login_at',
        'role',
        'school_id',
    ];

    protected ?string $pendingRole = null;

    public function setRoleAttribute(?string $value): void
    {
        $this->pendingRole = $value;
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            if ($user->pendingRole) {
                $role = Role::firstOrCreate(['name' => $user->pendingRole], ['guard_name' => 'web', 'display_name' => ucfirst($user->pendingRole)]);
                $user->roles()->syncWithoutDetaching([$role->id]);
                $user->pendingRole = null;
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Accessor for profile image url.
     */
    public function getProfileImageUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            return str_starts_with($this->avatar, 'http') || str_starts_with($this->avatar, '/') 
                ? $this->avatar 
                : asset($this->avatar);
        }

        if ($this->hasRole('restaurant_owner')) {
            return asset('images/khmer_kitchen_avatar.jpg');
        }

        if ($this->hasRole(['restaurant_staff', 'kitchen_staff'])) {
            return asset('images/sokha_ngin.jpg');
        }

        return asset('images/avatar_seng_sourng.jpg');
    }

    /**
     * Return the password for authenticating the user.
     */
    public function getAuthPassword(): ?string
    {
        return $this->password_hash;
    }

    /**
     * Convenience setter for password hashing.
     */
    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['password_hash'] = $value ? Hash::make($value) : null;
    }

    /**
     * The roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withPivot('created_at');
    }

    /**
     * Audit logs created by or attributed to this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    /**
     * Registered device push notification tokens.
     */
    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class, 'user_id');
    }

    /**
     * Assigned school if user is a school administrator or staff.
     */
    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Guardian profile if user is a parent/guardian.
     */
    public function guardian(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Guardian::class, 'user_id');
    }

    /**
     * Driver profile if user is a delivery driver.
     */
    public function driver(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Driver::class, 'user_id');
    }

    /**
     * Restaurants owned by this user.
     */
    public function ownedRestaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class, 'owner_user_id');
    }

    /**
     * Restaurant staff associations.
     */
    public function restaurantStaff(): HasMany
    {
        return $this->hasMany(RestaurantStaff::class, 'user_id');
    }

    /**
     * User delivery addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    /**
     * Platform notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Check if user has a specific role or any of the given roles.
     *
     * @param string|array<int, string> $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    /**
     * Check if user has a specific permission via any of their assigned roles.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return $this->roles->loadMissing('permissions')
            ->flatMap(fn (Role $role) => $role->permissions->pluck('name'))
            ->contains($permission);
    }

    /**
     * Assign a role by name or model instance.
     */
    public function assignRole(string|Role $role): void
    {
        $roleModel = is_string($role) 
            ? Role::firstOrCreate(['name' => $role], ['guard_name' => 'web', 'description' => ucfirst($role)]) 
            : $role;
        $this->roles()->syncWithoutDetaching([$roleModel->id]);
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::where('name', $role)->firstOrFail() : $role;
        $this->roles()->detach($roleModel->id);
    }

    /**
     * Sync roles for the user.
     *
     * @param array<int, string|Role> $roles
     */
    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            if (is_string($role)) {
                $found = Role::where('name', $role)->orWhere('id', $role)->first();
                return $found ? $found->id : Role::firstOrCreate(['name' => $role], ['guard_name' => 'web'])->id;
            }
            return (string) $role;
        })->all();

        $this->roles()->sync($roleIds);
    }
}
