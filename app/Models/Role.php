<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The users assigned this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
            ->withPivot('created_at');
    }

    /**
     * The permissions granted to this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Grant a permission to the role.
     */
    public function givePermission(string|Permission $permission): void
    {
        $permModel = is_string($permission)
            ? Permission::firstOrCreate(['name' => $permission])
            : $permission;

        $this->permissions()->syncWithoutDetaching([$permModel->id]);
    }

    /**
     * Revoke a permission from the role.
     */
    public function revokePermission(string|Permission $permission): void
    {
        $permModel = is_string($permission)
            ? Permission::where('name', $permission)->first()
            : $permission;

        if ($permModel) {
            $this->permissions()->detach($permModel->id);
        }
    }

    /**
     * Sync permissions for the role.
     *
     * @param array<int, string|Permission> $permissions
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function ($perm) {
            if (is_string($perm)) {
                return Permission::firstOrCreate(['name' => $perm])->id;
            }
            return $perm->id;
        })->all();

        $this->permissions()->sync($permissionIds);
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->pluck('name')->contains($permission);
    }
}
