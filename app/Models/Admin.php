<?php

namespace App\Models;

use App\Traits\Uuids;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory, Uuids;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'username',
        'password',
        'profile_image',
    ];

    protected $guarded = ['uuid'];

    protected $guard = 'admin';
    protected $table = 'admins';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
