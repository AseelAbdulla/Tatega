<?php

use App\Models\Role;
use Spatie\Permission\DefaultTeamResolver;
use Spatie\Permission\Models\Permission;

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'sanctum',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Models
    |--------------------------------------------------------------------------
    */

    'models' => [

        /*
         * Permission model.
         */
        'permission' => Permission::class,

        /*
         * Custom Role model used by this project.
         *
         * App\Models\Role extends Spatie\Permission\Models\Role.
         */
        'role' => Role::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Table Names
    |--------------------------------------------------------------------------
    */

    'table_names' => [

        /*
         * Roles table.
         */
        'roles' => 'roles',

        /*
         * Permissions table.
         */
        'permissions' => 'permissions',

        /*
         * Direct permissions assigned to models.
         */
        'model_has_permissions' => 'model_has_permissions',

        /*
         * Roles assigned to models.
         */
        'model_has_roles' => 'model_has_roles',

        /*
         * Permissions assigned to roles.
         */
        'role_has_permissions' => 'role_has_permissions',

    ],

    /*
    |--------------------------------------------------------------------------
    | Column Names
    |--------------------------------------------------------------------------
    */

    'column_names' => [

        /*
         * Pivot role key.
         *
         * null = default role_id
         */
        'role_pivot_key' => null,

        /*
         * Pivot permission key.
         *
         * null = default permission_id
         */
        'permission_pivot_key' => null,

        /*
         * Morph key used by model_has_roles
         * and model_has_permissions.
         */
        'model_morph_key' => 'model_id',

        /*
         * Team foreign key.
         *
         * Teams are disabled below.
         */
        'team_foreign_key' => 'team_id',

    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Check
    |--------------------------------------------------------------------------
    */

    'register_permission_check_method' => true,

    /*
    |--------------------------------------------------------------------------
    | Octane Reset Listener
    |--------------------------------------------------------------------------
    */

    'register_octane_reset_listener' => false,

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    'events_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Teams
    |--------------------------------------------------------------------------
    */

    'teams' => false,

    /*
    |--------------------------------------------------------------------------
    | Team Resolver
    |--------------------------------------------------------------------------
    */

    'team_resolver' => DefaultTeamResolver::class,

    /*
    |--------------------------------------------------------------------------
    | Passport
    |--------------------------------------------------------------------------
    */

    'use_passport_client_credentials' => false,

    /*
    |--------------------------------------------------------------------------
    | Exception Messages
    |--------------------------------------------------------------------------
    */

    'display_permission_in_exception' => false,

    'display_role_in_exception' => false,

    /*
    |--------------------------------------------------------------------------
    | Wildcard Permissions
    |--------------------------------------------------------------------------
    */

    'enable_wildcard_permission' => false,

    /*
    |--------------------------------------------------------------------------
    | Wildcard Permission Class
    |--------------------------------------------------------------------------
    */

    // 'wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    'cache' => [

        /*
         * Permissions and roles are cached for 24 hours.
         */
        'expiration_time' => DateInterval::createFromDateString('24 hours'),

        /*
         * Cache key.
         */
        'key' => 'spatie.permission.cache',

        /*
         * Use the default Laravel cache store.
         */
        'store' => 'default',
    ],

];
