<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ActiveAccountNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'active_token',
        'active',
        'avatar_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'active_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function product_votes(): HasMany
    {
        return $this->hasMany(ProductVote::class);
    }

    public function productVotes(): HasMany
    {
        return $this->hasMany(ProductVote::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the active account notification.
     *
     * @param string $token
     * @return void
     */
    public function sendActiveAccountNotification($token): void
    {
        $this->notify(new ActiveAccountNotification($token));
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('storage/images/avatars/' . $this->getAttribute('avatar_image'));
    }

//    public function setPasswordAttribute($value): void
//    {
//        $this->attributes['password'] = Hash::make($value);
//    }
}
