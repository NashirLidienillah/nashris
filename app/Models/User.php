<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function processedPayrolls(): HasMany
    {
        return $this->hasMany(Payroll::class, 'processed_by');
    }

    public function managerApprovedLeaves(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'manager_approved_by');
    }

    public function hrApprovedLeaves(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'hr_approved_by');
    }
}
