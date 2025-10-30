<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'center_id'];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

        
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}
