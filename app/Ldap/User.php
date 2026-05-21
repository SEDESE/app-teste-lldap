<?php

namespace App\Ldap;

use LdapRecord\Models\Model;

class User extends Model
{
    public static array $objectClasses = [
        'inetOrgPerson',
    ];
}