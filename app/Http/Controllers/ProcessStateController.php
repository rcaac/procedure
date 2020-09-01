<?php

namespace App\Http\Controllers;

use App\ProcessState;
use Illuminate\Http\Request;

class ProcessStateController extends Controller
{
    public function getProcessState(Request $request)
    {
        $search = $request->search;

        $state = ProcessState::orderBy('id', 'ASC')
            ->where('condition', 'like', '%'.$search.'%')
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
            'condition' => $state
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'condition' => 'required'
            ],
            [
                'condition.required' => 'El estado del procedimiento es obligatorio'
            ]
        );

        $state = new ProcessState();
        $state->condition = $request->condition;
        $state->save();

        return  $state;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'condition' => 'required'
            ],
            [
                'condition.required' => 'El estado del procedimiento es obligatorio'
            ]
        );

        $state = ProcessState::findOrFail($request->id);
        $state->condition = $request->condition;
        $state->save();

        return  $state;
    }

    public function trash($id)
    {
        ProcessState::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $state = ProcessState::orderBy('id', 'desc')
            ->where('condition', 'like', '%'.$search.'%')
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
            'condition' => $state
        ];
    }
}
