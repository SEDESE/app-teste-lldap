<?php

namespace App\Ldap;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use LdapRecord\Models\Model;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected ?string $connection = 'default';
}