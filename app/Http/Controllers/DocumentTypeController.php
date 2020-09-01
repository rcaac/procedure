<?php

namespace App\Http\Controllers;

use App\DetailDocumentType;
use App\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function getDocumentType(Request $request)
    {
        $code = $request->search;

        $type = DocumentType::orderBy('id', 'ASC')
            ->where('type', 'like', '%'.$code.'%')
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
            'type' => $type
        ];
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'type'                    => 'required',
                'detail_document_type_id' => 'required|integer|not_in:0'
            ],
            [
                'type.required'                  => 'El tipo de documento es obligatorio',
                'detail_document_type_id.not_in' => 'Debe de elegir un tipo de documento',
            ]
        );

        $type = new DocumentType();
        $type->type                    = $request->type;
        $type->detail_document_type_id = $request->detail_document_type_id;
        $type->save();

        return  $type;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'type'                    => 'required',
                'detail_document_type_id' => 'required|integer|not_in:0'
            ],
            [
                'type.required'                  => 'El tipo de documento es obligatorio',
                'detail_document_type_id.not_in' => 'Debe de elegir un tipo de documento',
            ]
        );

        $type = DocumentType::findOrFail($request->id);
        $type->type                    = $request->type;
        $type->detail_document_type_id = $request->detail_document_type_id;
        $type->save();

        return  $type;
    }

    public function trash($id)
    {
        DocumentType::findOrFail($id)->delete();
    }

    public function trashed(Request $request)
    {
        $code = $request->search;

        $type = DocumentType::orderBy('id', 'desc')
            ->where('type', 'like', '%'.$code.'%')
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
            'type' => $type
        ];
    }

    public function getDetailDocumentType()
    {
        $details = DetailDocumentType::select('id', 'detail')->get();

        return [
            'detail' => $details
        ];
    }
}
