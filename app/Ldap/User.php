<?php

namespace App\Ldap;

use LdapRecord\Models\OpenLDAP\User as BaseUser;

class User extends BaseUser
{
    protected string $guidKey = 'uuid';

    public static array $objectClasses = [
        'top',
        'person',
        'organizationalPerson',
        'inetOrgPerson',
    ];
}