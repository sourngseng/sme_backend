<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the Auditable trait for the model.
     */
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->recordAuditLog('created', null, $model->filterAuditAttributes($model->getAttributes()));
        });

        static::updated(function (Model $model) {
            $dirty = $model->getDirty();
            $original = array_intersect_key($model->getOriginal(), $dirty);

            $action = array_key_exists('status', $dirty) && count($dirty) === 1
                ? 'status_updated'
                : 'updated';

            $model->recordAuditLog(
                $action,
                $model->filterAuditAttributes($original),
                $model->filterAuditAttributes($dirty)
            );
        });

        static::deleted(function (Model $model) {
            $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting()
                ? 'force_deleted'
                : 'deleted';

            $model->recordAuditLog($action, $model->filterAuditAttributes($model->getOriginal()), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $model->recordAuditLog('restored', null, $model->filterAuditAttributes($model->getAttributes()));
            });
        }
    }

    /**
     * Record an audit log entry.
     *
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function recordAuditLog(string $action, ?array $oldValues, ?array $newValues): ?AuditLog
    {
        try {
            return AuditLog::create([
                'actor_id' => Auth::id(),
                'action' => $action,
                'auditable_type' => get_class($this),
                'auditable_id' => $this->getKey(),
                'old_values' => $oldValues ?: null,
                'new_values' => $newValues ?: null,
                'ip_address' => request() ? request()->ip() : null,
            ]);
        } catch (\Throwable $e) {
            // Fail safe so audit failure never crashes the primary transaction
            report($e);
            return null;
        }
    }

    /**
     * Filter out sensitive attributes before writing to audit logs.
     *
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    protected function filterAuditAttributes(array $attributes): array
    {
        $hidden = array_merge(
            $this->getHidden(),
            ['password_hash', 'password', 'remember_token', 'token', 'otp_hash']
        );

        return array_diff_key($attributes, array_flip($hidden));
    }
}
