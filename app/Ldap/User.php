<?php

namespace App\Ldap;

use LdapRecord\Models\OpenLDAP\User as BaseUser;
use Illuminate\Contracts\Auth\Authenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;

class User extends BaseUser implements Authenticatable
{
    use AuthenticatesWithLdap;

    /**
     * The object classes of the LDAP model.
     */
    public static array $objectClasses = [
        'top',
        'person',
        'organizationalPerson',
        'inetOrgPerson',
    ];
}