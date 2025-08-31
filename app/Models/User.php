<?php

namespace App\Models;

use App\Traits\Uuids;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Uuids;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'username',
        'password',
        'profile_image',
        'email_verified_at',
    ];

    protected $guarded = ['uuid'];

    protected $guard = 'user';
    protected $table = 'users';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
