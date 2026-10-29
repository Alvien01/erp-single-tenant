<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'allowed_menus'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'allowed_menus',
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
            'allowed_menus' => 'array',
        ];
    }

    /**
     * Check if user is allowed to access a given menu route.
     */
    public function canAccessMenu(string $routeName): bool
    {
        // Admin always has unrestricted full access
        if ($this->role === 'admin') {
            return true;
        }

        // If allowed_menus is null, allow by default (unrestricted/legacy)
        if ($this->allowed_menus === null) {
            return true;
        }

        return in_array($routeName, $this->allowed_menus);
    }
}
