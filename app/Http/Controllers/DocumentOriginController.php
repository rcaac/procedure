<?php

namespace App\Http\Controllers;

use App\DocumentOrigin;
use Illuminate\Http\Request;

class DocumentOriginController extends Controller
{
    public function getDocumentOrigin(Request $request)
    {
        $search = $request->search;

        $origin = DocumentOrigin::orderBy('id', 'ASC')
            ->where('origin', 'like', '%'.$search.'%')
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $origin->total(),
                'current_page' => $origin->currentPage(),
                'per_page' => $origin->perPage(),
                'last_page' => $origin->lastPage(),
                'from' => $origin->firstItem(),
                'to' => $origin->lastItem(),
            ],
            'origin' => $origin
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'origin' => 'required'
            ],
            [
                'origin.required' => 'El origen del documento es obligatorio'
            ]
        );

        $origin = new DocumentOrigin();
        $origin->origin = $request->origin;
        $origin->save();

        return  $origin;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'origin' => 'required'
            ],
            [
                'origin.required' => 'El origen del documento es obligatorio'
            ]
        );

        $origin = DocumentOrigin::findOrFail($request->id);
        $origin->origin = $request->origin;
        $origin->save();

        return  $origin;
    }

    public function trash($id)
    {
        DocumentOrigin::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $origin = DocumentOrigin::orderBy('id', 'desc')
            ->where('origin', 'like', '%'.$search.'%')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $origin->total(),
                'current_page' => $origin->currentPage(),
                'per_page' => $origin->perPage(),
                'last_page' => $origin->lastPage(),
                'from' => $origin->firstItem(),
                'to' => $origin->lastItem(),
            ],
            'origin' => $origin
        ];
    }
}
