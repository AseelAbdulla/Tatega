<?php

namespace App\Services;

use App\Models\Role;


class RoleService
{


    /**
     * Get all roles
     */
    public function getAllRoles()
    {

        return Role::with('users:id,name,email')
            ->get();

    }






    /**
     * Create role
     */
    public function createRole(array $data)
    {

        return Role::create([

            'name'=>$data['name'],

            'display_name'=>$data['display_name']

        ]);

    }







    /**
     * Get role by id
     */
    public function getRoleById($id)
    {

        return Role::with('users:id,name,email')
            ->find($id);

    }








    /**
     * Update role
     */
    public function updateRole($id,array $data)
    {

        $role = Role::find($id);



        if(!$role)
        {
            return null;
        }



        $role->update([

            'name'=>$data['name'] ?? $role->name,

            'display_name'=>$data['display_name'] ?? $role->display_name

        ]);



        return $role;

    }








    /**
     * Delete role
     */
    public function deleteRole($id)
    {

        $role = Role::find($id);



        if(!$role)
        {
            return false;
        }



        // حذف العلاقات من جدول role_user
        $role->users()->detach();



        // حذف role
        $role->delete();



        return true;

    }


}
