<?php

namespace Primeskills\ApiCommon\Middleware;

use Primeskills\ApiCommon\Exceptions\PrimeskillsException;
use Closure;

class PrimeskillsRole
{
    private $roles = [
        1 => 'admin',
        2 => 'studio',
        3 => 'lecturer',
        4 => 'student',
        5 => 'superadmin',
        6 => 'organization'
    ];

    public function handle($request, Closure $next, $role, $guard = null)
    {
        try {
            $roleUser = $this->roles[$request->get('user')->role_id];
            $roleAllowed = is_array($role)
                ? $role
                : explode('|', $role);

            if (! in_array($roleUser, $roleAllowed)) {
                throw new PrimeskillsException(403, "Forbidden access for this path for role ");
            }
        } catch (\Exception $exception) {
            throw new PrimeskillsException(403, "Forbidden access for this path for role ");
        }

        return $next($request);
    }
}
