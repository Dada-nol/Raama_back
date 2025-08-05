<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'email',
        'password',
        'role',
        'personal_points'
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

    /**
     * @return BelongsToMany<Souvenir, User>
     */
    public function souvenirs(): BelongsToMany
    {
        return $this->belongsToMany(Souvenir::class, 'souvenir_users')
            ->withPivot('pseudo', 'role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Souvenir, User>
     */
    public function createdSouvenirs(): HasMany
    {
        return $this->hasMany(Souvenir::class, 'user_id');
    }

    /**
     * @return HasMany<Entry, User>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->role, 'admin');
    }
}
