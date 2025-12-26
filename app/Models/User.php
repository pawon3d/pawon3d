<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserInvitationNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'users';

    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'invitation_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'invitation_sent_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('users')
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

                return "{$nama} {$terjemahan[$eventName]}";
            })
            ->dontSubmitEmptyLogs();
    }

    public function workers()
    {
        return $this->hasMany(ProductionWorker::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Generate invitation token dan kirim email undangan.
     */
    public function sendInvitation(): void
    {
        $this->invitation_token = Str::random(64);
        $this->invitation_sent_at = now();
        $this->save();

        $storeProfile = StoreProfile::query()->first();

        if ($storeProfile->name != '' && $storeProfile->name != null) {
            $storeName = $storeProfile->name;
        } else {
            $storeName = 'Pawon3D';
        }

        $this->notify(new UserInvitationNotification($storeName));
    }

    /**
     * Aktivasi akun dengan password.
     */
    public function activateWithPassword(string $password): void
    {
        $this->password = bcrypt($password);
        $this->is_active = true;
        $this->activated_at = now();
        $this->invitation_token = null;
        $this->save();
    }

    /**
     * Cek apakah invitation token masih valid (7 hari).
     */
    public function hasValidInvitationToken(): bool
    {
        if (! $this->invitation_token || ! $this->invitation_sent_at) {
            return false;
        }

        return $this->invitation_sent_at->addDays(7)->isFuture();
    }

    /**
     * Cek apakah akun sudah diaktivasi.
     */
    public function isActivated(): bool
    {
        return $this->activated_at !== null;
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleActive(): void
    {
        $this->is_active = ! $this->is_active;
        $this->save();
    }

    /**
     * Scope untuk user aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk user yang belum diaktivasi.
     */
    public function scopePending($query)
    {
        return $query->whereNull('activated_at');
    }
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
