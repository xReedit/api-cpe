<?php

namespace App\Models;

use App\Models\System\DataViewer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use DataViewer, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'api_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setUsernameAttribute($value)
    {
        $email_array = explode('@', $value);
        $this->attributes['username'] = $email_array[0];
    }

    public function isRole($role)
    {
        return ($this->role === $role)?true:false;
    }

    public function isNotRole($role)
    {
        return ($this->role !== $role)?true:false;
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }
}