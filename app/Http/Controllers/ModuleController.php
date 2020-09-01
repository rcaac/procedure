<?php

namespace App\Http\Controllers;

use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $module = Module::orderBy('id', 'ASC')
            ->where('description', 'like', '%'.$search.'%')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $module->total(),
                'current_page' => $module->currentPage(),
                'per_page' => $module->perPage(),
                'last_page' => $module->lastPage(),
                'from' => $module->firstItem(),
                'to' => $module->lastItem(),
            ],
            'modules' => $module
        ];
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'description' => 'required'
            ],
            [
                'description.required' => 'El módulo es obligatorio'
            ]
        );

        $module = new Module();
        $module->description = $request->description;
        $module->created_by  = $this->getPersonId();
        $module->save();

        return  $module;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'description' => 'required'
            ],
            [
                'description.required' => 'El módulo es obligatorio'
            ]
        );

        $module = Module::findOrFail($request->id);
        $module->description = $request->description;
        $module->save();

        return  $module;
    }

    public function trash($id)
    {
        Module::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $module = Module::orderBy('id', 'desc')
            ->where('description', 'like', '%'.$search.'%')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $module->total(),
                'current_page' => $module->currentPage(),
                'per_page' => $module->perPage(),
                'last_page' => $module->lastPage(),
                'from' => $module->firstItem(),
                'to' => $module->lastItem(),
            ],
            'modules' => $module
        ];
    }

    public function getSelectedModules()
    {
        $modules = Module::select('id', 'description')->get();

        return [
            'modules' => $modules
        ];
    }
}
