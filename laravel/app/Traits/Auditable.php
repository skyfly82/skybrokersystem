<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->auditLog('created');
        });

        static::updated(function ($model) {
            $model->auditLog('updated', $model->getOriginal());
        });

        static::deleted(function ($model) {
            $model->auditLog('deleted');
        });
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function auditLog(string $event, ?array $oldValues = null, ?string $description = null): void
    {
        $user = $this->getCurrentAuditUser();

        if (! $user) {
            return; // Skip if no authenticated user
        }

        $newValues = null;

        if ($event === 'updated') {
            $newValues = $this->getDirty();
            // Only log if there are actual changes
            if (empty($newValues)) {
                return;
            }
        } elseif ($event === 'created') {
            $newValues = $this->getAttributes();
        }

        AuditLog::create([
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'user_type' => $user['type'],
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'description' => $description,
        ]);
    }

    public function logCustomEvent(string $event, ?string $description = null, ?array $data = null): void
    {
        $this->auditLog($event, null, $description);
    }

    protected function getCurrentAuditUser(): ?array
    {
        // Check for system user first
        if (auth('system_user')->check()) {
            $user = auth('system_user')->user();

            return [
                'type' => 'system_user',
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        // Check for customer user
        if (auth('customer_user')->check()) {
            $user = auth('customer_user')->user();

            return [
                'type' => 'customer_user',
                'id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
            ];
        }

        return null;
    }
}
