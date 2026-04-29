<?php

namespace App\Models;

// PENTING: Gunakan Authenticatable dari MongoDB Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Paksa menggunakan koneksi mongodb
    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'image',
        'is_admin',
    ];

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

    /**
     * {@inheritDoc}
     */
    public function getAuthIdentifier() {}

    /**
     * {@inheritDoc}
     */
    public function getAuthIdentifierName() {}

    /**
     * {@inheritDoc}
     */
    public function getAuthPassword() {}

    /**
     * {@inheritDoc}
     */
    public function getAuthPasswordName() {}

    /**
     * {@inheritDoc}
     */
    public function getRememberToken() {}

    /**
     * {@inheritDoc}
     */
    public function getRememberTokenName() {}

    /**
     * {@inheritDoc}
     */
    public function setRememberToken($value) {}
}
