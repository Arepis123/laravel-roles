<?php

namespace App\Models;

use App\Models\Booking;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'font_size',
        'status',
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
            'status' => 'string',
        ];
    }

    // Boot method to set default role for new users
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            // Assign 'User' role by default if no roles are assigned
            if ($user->roles()->count() === 0) {
                $user->assignRole('User');
            }
        });
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'booked_by');
    }

    // Helper method to check if user is active
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Helper method to toggle status
    public function toggleStatus(): void
    {
        $this->status = $this->status === 'active' ? 'inactive' : 'active';
        $this->save();
    }
}