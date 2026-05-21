<?php

namespace App\Ldap;

use LdapRecord\Models\Model;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;

class User extends Model implements LdapAuthenticatable
{
    use AuthenticatesWithLdap;

    public static array $objectClasses = [
        'inetOrgPerson',
    ];
}