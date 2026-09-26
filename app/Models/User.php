<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_STAFF = 'staff';
    const ROLE_AUDITOR = 'auditor';

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN => 'System Administrator',
            self::ROLE_MANAGER => 'Warehouse Manager',
            self::ROLE_STAFF => 'Inventory Staff',
            self::ROLE_AUDITOR => 'Auditor / Viewer',
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isAuditor(): bool
    {
        return $this->role === self::ROLE_AUDITOR;
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canManageProducts(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    public function canDelete(): bool
    {
        return $this->isAdmin();
    }

    public function canRecordMovements(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_STAFF]);
    }

    public function canAdjustStock(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roles()[$this->role] ?? ucfirst($this->role);
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'badge-danger',
            self::ROLE_MANAGER => 'badge-info',
            self::ROLE_STAFF => 'badge-success',
            self::ROLE_AUDITOR => 'badge-warning',
            default => 'badge-info',
        };
    }
}
