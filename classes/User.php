<?php

class User
{
    public static function hasRole($user, $role_name)
    {
        if (isset($user) && isset($role_name)) {
            $roles = $user->roles;
            if(in_array($role_name, $roles)) {
                return true;
            }
        }
        return false;
    }

    public static function isAdmin($user)
    {
        return self::hasRole($user, 'administrator');
    }

    public static function isEditor($user)
    {
        return self::hasRole($user, 'editor');
    }

    public static function isAdminOrEditor($user)
    {
        return (self::hasRole($user, 'administrator') || self::hasRole($user, 'editor'));
    }
}

