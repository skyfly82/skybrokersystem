<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'user_type',
        'user_id',
        'user_name',
        'user_email',
        'event',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFormattedEventAttribute(): string
    {
        return match ($this->event) {
            'created' => 'Utworzenie',
            'updated' => 'Edycja',
            'deleted' => 'Usunięcie',
            'login' => 'Logowanie',
            'logout' => 'Wylogowanie',
            'transfer_admin' => 'Przeniesienie uprawnień',
            default => ucfirst($this->event)
        };
    }

    public function getChangedFieldsAttribute(): array
    {
        if (! $this->old_values || ! $this->new_values) {
            return [];
        }

        $changes = [];
        foreach ($this->new_values as $field => $newValue) {
            $oldValue = $this->old_values[$field] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    public function getFieldLabelAttribute($field): string
    {
        $labels = [
            'company_name' => 'Nazwa firmy',
            'tax_number' => 'NIP',
            'email' => 'Email',
            'phone' => 'Telefon',
            'address' => 'Adres',
            'cod_return_account' => 'Konto zwrotów COD',
            'settlement_account' => 'Konto rozliczeniowe',
            'first_name' => 'Imię',
            'last_name' => 'Nazwisko',
            'role' => 'Rola',
            'is_active' => 'Status aktywności',
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }
}
