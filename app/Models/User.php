<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Utils\Action;
use App\Http\Utils\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static function privileges(Action ...$actions): bool
    {
        $roles = [
            Role::ADMIN => [Action::DELETE_INSTRUCTOR, Action::DELETE_STUDENT],
            Role::INSTRUCTOR => [Action::DELETE_STUDENT],
            Role::STUDENT => [],
        ];

        foreach ($actions as $action) {
            if (in_array($action, $roles[auth()->user()->role])) {
                return true;
                break;
            }
        }

        return false;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}