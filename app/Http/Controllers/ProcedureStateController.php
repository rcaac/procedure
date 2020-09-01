<?php

namespace App\Http\Controllers;

use App\ProcedureState;
use Illuminate\Http\Request;

class ProcedureStateController extends Controller
{
    public function getProcedureState(Request $request)
    {
        $search = $request->search;

        $state = ProcedureState::orderBy('id', 'ASC')
            ->where('state', 'like', '%'.$search.'%')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $state->total(),
                'current_page' => $state->currentPage(),
                'per_page' => $state->perPage(),
                'last_page' => $state->lastPage(),
                'from' => $state->firstItem(),
                'to' => $state->lastItem(),
            ],
            'state' => $state
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'state' => 'required'
            ],
            [
                'state.required' => 'El estado del trámite es obligatorio'
            ]
        );

        $state = new ProcedureState();
        $state->state = $request->state;
        $state->save();

        return  $state;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'state' => 'required'
            ],
            [
                'state.required' => 'El estado del trámite es obligatorio'
            ]
        );

        $state = ProcedureState::findOrFail($request->id);
        $state->state = $request->state;
        $state->save();

        return  $state;
    }

    public function trash($id)
    {
        ProcedureState::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $state = ProcedureState::orderBy('id', 'desc')
            ->where('state', 'like', '%'.$search.'%')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $state->total(),
                'current_page' => $state->currentPage(),
                'per_page' => $state->perPage(),
                'last_page' => $state->lastPage(),
                'from' => $state->firstItem(),
                'to' => $state->lastItem(),
            ],
            'state' => $state
        ];
    }
}
