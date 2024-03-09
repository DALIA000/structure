<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as Model;
use App\Traits\Localizable;

class Permission extends Model
{
    use HasFactory;
    use Localizable;

    protected $casts = [
        'required_permissions_slugs' => 'array',
    ];

    protected $guarded = [];

    protected $hidden = ['pivot'];

    // cascade delete
    protected $cascadeDeletes = ['locales'];

    // prevent if any exists
    public static $cascade = ['roles'];

    public function route_permissions()
    {
        return $this->hasMany(RoutePermission::class, 'permission_slug', 'name');
    }
}
