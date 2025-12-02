<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method BelongsToMany roles()
 * @method bool hasRole(string $role)
 * @method bool hasPermissionTo(string $permission)
 */
trait HasRoles
{
    //
}

// Extend User model for IDE autocompletion
if (false) {
    class User extends \Illuminate\Foundation\Auth\User
    {
        use HasRoles;
    }
}