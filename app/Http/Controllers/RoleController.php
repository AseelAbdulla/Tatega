<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;


class RoleController extends Controller
{

    protected $roleService;



    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }






    /**
     * Display all roles
     */
    public function index()
    {

        $roles = $this->roleService->getAllRoles();



        return response()->json([

            'status' => true,

            'data' => $roles

        ]);

    }









    /**
     * Store new role
     */
    public function store(StoreRoleRequest $request)
    {


        $role = $this->roleService->createRole(

            $request->validated()

        );



        return response()->json([

            'message' => 'Role created successfully',

            'data' => $role

        ],201);

    }









    /**
     * Display specific role
     */
    public function show(string $id)
    {

        $role = $this->roleService->getRoleById($id);



        if(!$role)
        {

            return response()->json([

                'message'=>'Role not found'

            ],404);

        }




        return response()->json([

            'status'=>true,

            'data'=>$role

        ]);

    }









    /**
     * Update role
     */
    public function update(UpdateRoleRequest $request,string $id)
    {


        $role = $this->roleService->updateRole(

            $id,

            $request->validated()

        );





        if(!$role)
        {

            return response()->json([

                'message'=>'Role not found'

            ],404);

        }





        return response()->json([

            'message'=>'Role updated successfully',

            'data'=>$role

        ]);

    }









    /**
     * Delete role
     */
    public function destroy(string $id)
    {

        $deleted = $this->roleService->deleteRole($id);





        if(!$deleted)
        {

            return response()->json([

                'message'=>'Role not found'

            ],404);

        }





        return response()->json([

            'message'=>'Role deleted successfully'

        ]);

    }


}
