<?php

namespace App\Http\Controllers;

use App\AttentionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttentionTypeController extends Controller
{
    public function listAttentionTypes($id)
    {
        $resourceDetail = AttentionType::where('id', $id)->value('resource_detail');

        return[
            'resourceDetail' => $resourceDetail
        ];
    }

    public function index(Request $request)  
    {
        $search = $request->search;

        $types = AttentionType::orderBy('id', 'ASC')
            ->where('description', 'like', '%'.$search.'%')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $types->total(),
                'current_page' => $types->currentPage(),
                'per_page' => $types->perPage(),
                'last_page' => $types->lastPage(),
                'from' => $types->firstItem(),
                'to' => $types->lastItem(),
            ],
            'types' => $types
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'description' => 'required'
            ],
            [
                'description.required' => 'El recurso es obligatorio'
            ]
        );

        $type = new AttentionType();
        $type->description = $request->description;
        $type->resource_detail = $request->resource_detail;
        $type->created_by  = $this->getPersonId();
        $type->save();

        return  $type;
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'description' => 'required'
            ],
            [
                'description.required' => 'El recurso es obligatorio'
            ]
        );

        $type = AttentionType::findOrFail($request->id);
        $type->description = $request->description;
        $type->resource_detail = $request->resource_detail;
        $type->created_by  = $this->getPersonId();
        $type->save();

        return  $type;
    }

    public function trash($id)
    {
        AttentionType::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $type = AttentionType::orderBy('id', 'desc')
            ->where('description', 'like', '%'.$search.'%')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $type->total(),
                'current_page' => $type->currentPage(),
                'per_page' => $type->perPage(),
                'last_page' => $type->lastPage(),
                'from' => $type->firstItem(),
                'to' => $type->lastItem(),
            ],
            'types' => $type
        ];
    }
}
