<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $roleQuery = Role::query();

        // get single data
        if ($id) {
            $role = $roleQuery->find($id);

            if ($role) {
                return ResponseFormatter::success($role, 'Role found');
            }

            return ResponseFormatter::error('Role not found', 404);
        }

        // get multiple data
        $roles = $roleQuery->where('company_id', $request->company_id);

        // powerhuman.com/api/role?name=kunde
        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        //Role::with(['users'])->where('name', 'like', '%kunde%')->paginate(10);
        return ResponseFormatter::success(
            $roles->paginate($limit),
            'roles found'
        );
    }

    public function create(CreateRoleRequest $request)
    {
        try {

            // create role
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if (!$role) {
                throw new Exception('Role not created');
            }

            return ResponseFormatter::success($role, 'Role created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            //get role
            $role = Role::find($id);

            //check if role exist
            if (!$role) {
                throw new Exception('Role not Found');
            }

            // update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success($role, 'Role updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            // get role
            $role = Role::find($id);

            // check if role exists
            if (!$role) {
                throw new Exception('Role not found');
            }

            // delete role
            $role->delete();

            return ResponseFormatter::success('Role deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
