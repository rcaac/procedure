<?php

namespace App\Http\Controllers;

use App\Provided;
use Illuminate\Http\Request;

class ProvidedController extends Controller
{
    public function getProvided(Request $request)
    {
        $search = $request->search;

        $action = Provided::orderBy('id', 'ASC')
            ->where('provided', 'like', '%'.$search.'%')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $action->total(),
                'current_page' => $action->currentPage(),
                'per_page' => $action->perPage(),
                'last_page' => $action->lastPage(),
                'from' => $action->firstItem(),
                'to' => $action->lastItem(),
            ],
            'action' => $action
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
                'provided' => 'required'
            ],
            [
                'provided.required' => 'El tipo de acción del documento es obligatorio'
            ]
        );

        $action = new Provided();
        $action->provided   = $request->provided;
        $action->created_by = $this->getPersonId();
        $action->save();

        return  $action;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'provided' => 'required'
            ],
            [
                'provided.required' => 'El tipo de accion del documento es obligatorio'
            ]
        );

        $action = Provided::findOrFail($request->id);
        $action->provided = $request->action;
        $action->save();

        return  $action;
    }

    public function trash($id)
    {
        Provided::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $action = Provided::orderBy('id', 'desc')
            ->where('provided', 'like', '%'.$search.'%')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $action->total(),
                'current_page' => $action->currentPage(),
                'per_page' => $action->perPage(),
                'last_page' => $action->lastPage(),
                'from' => $action->firstItem(),
                'to' => $action->lastItem(),
            ],
            'action' => $action
        ];
    }

}
