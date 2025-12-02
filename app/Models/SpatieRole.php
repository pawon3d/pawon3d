<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role;

class SpatieRole extends Role
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'max_users',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_users' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('roles')
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $nama = $this->name;

                $terjemahan = [
                    'created' => 'ditambahkan',
                    'updated' => 'diperbarui',
                    'deleted' => 'dihapus',
                    'restored' => 'dipulihkan',
                ];

                return "Peran {$nama} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    /**
     * Check if the role has reached its maximum number of users.
     */
    public function hasReachedMaxUsers(): bool
    {
        if ($this->max_users === null) {
            return false;
        }

        return $this->users()->count() >= $this->max_users;
    }

    /**
     * Get the remaining slots for users.
     */
    public function getRemainingUserSlots(): ?int
    {
        if ($this->max_users === null) {
            return null;
        }

        return max(0, $this->max_users - $this->users()->count());
    }
}
