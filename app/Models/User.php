<?php

namespace App\Models;

use App\Traits\Meta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, Meta;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be append the model.
     *
     * @var array
     */
    protected $appends = [ 'permissions' ];

    /**
     * Check User Role
     * @param  string|array  $name user role
     * @return boolean 
     */
    public function isRole($name)
    {
        if( is_array($name) ){
            return in_array($this->role, $name);
        }
        return $this->role == $name;
    }
    
    /**
     * Check User Role Permission
     * @param  string|array  $name
     * @return boolean 
     */
    public function hasPermission($name)
    {
        if( is_array($name) ){
            foreach ($name as $item) {
                if( ! in_array($item, $this->permissions) ){
                    return false;
                }
            }
            return true;
        }
        return in_array($name, $this->permissions);
    }

    /**
     * Get The user Permissions
     * @return array
     */
    public function getPermissionsAttribute()
    {
        $key = "{$this->id}_permissions";
        return Cache::remember($key, 3600, function() {
            return $this->myRole->permissions ?? [];
        });
    }

    /**
     * Relationship with User Role 
     */
    public function myRole()
    {
        return $this->belongsTo(UserRole::class, 'role', 'name');
    }

    /**
     * Relationship with UserMeta 
     */
    public function meta()
    {
        return $this->hasMany(UserMeta::class);
    }
}
