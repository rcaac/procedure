<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $search= $request->buscar;
        $criterion = $request->criterion;

        if ($search==''){
            $roles = Role::orderBy('id', 'desc')->paginate(3);
        }
        else{
            $roles = Role::where($criterion, 'like', '%'. $search. '%')->orderBy('id', 'desc')->paginate(3);
        }


        return [
            'pagination' => [
                'total'        => $roles->total(),
                'current_page' => $roles->currentPage(),
                'per_page'     => $roles->perPage(),
                'last_page'    => $roles->lastPage(),
                'from'         => $roles->firstItem(),
                'to'           => $roles->lastItem(),
            ],
            'roles' => $roles
        ];
    }
    public function selectRole()
    {
        $role_internal = Role::select('id','description')->where('id', '!=', 1)->where('id', '!=', 4)->get();
        $role_external = Role::select('id','description')->where('id', 4)->get();
        $role_citizen = Role::select('id','description')->where('id', 4)->get();
        $role = Role::select('id','description')->where('id','!=', 1)->get();
        return [
            'role_internal' => $role_internal,
            'role_external' => $role_external,
            'role_citizen'  => $role_citizen,
            'role'          => $role
        ];
    }
}
