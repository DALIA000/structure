<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes, Notifiable;
    use HasRoles;

    protected $fillable = ['name', 'email', 'token', 'username', 'password', 'pin_code'];

    public function role(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roles?->first()
        );
    }

    public function permissionsArr(): Attribute
    {
        $permissions = Permission::all();
        $adminPermissions = $this->role?->permissions()?->get();
        $permissionsArr = [];

        foreach ($permissions as $permission) {
            $permissionExists = $adminPermissions?->contains($permission);
            if ($permissionExists) {
                $permissionsArr[explode('.', $permission['name'])[0]][explode('.', $permission['name'])[1]] = true;
            } else {
                $permissionsArr[explode('.', $permission['name'])[0]][explode('.', $permission['name'])[1]] = false;
            }
        }

        return Attribute::make(
            get: fn () => $permissionsArr,
        );
    }
}
