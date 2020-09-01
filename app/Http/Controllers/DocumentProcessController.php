<?php

namespace App\Http\Controllers;

use App\Annexe;
use App\Document;
use App\DocumentaryProcedure;
use App\DocumentProcedure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DocumentProcessController extends Controller
{
    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    private function queryProcess()
    {
        return Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons', 'documentary_procedures.person_id', '=', 'persons.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('entities', 'Dependency.entity_id', '=', 'entities.id')
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('detail_document_types', 'document_types.detail_document_type_id', '=', 'detail_document_types.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documentary_procedures.id AS documentary_procedure_id',
                'Dependency.id AS dependency_id',
                'documents.records',
                'documents.registry',
                'documents.reference',
                'documents.code',
                'documents.tupa',
                'provided.provided',
                'document_types.type',
                'document_types.id AS document_type_id',
                'detail_document_types.id AS detail_document_type_id',
                'document_priorities.priority',
                'entities.description',
                'Dependency.description AS Dependency',
                'documents.affair',
                'documents.annexes',
                'documents.folio',
                'documents.shipping_date',
                'documents.document_origin_id',
                'documentary_procedures.reception_date',
                'persons.dni',
                DB::raw('CONCAT(firstName, " ", lastName) AS fullName')
            )
            ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
            ->where('documentary_procedures.procedure_state_id', 2)
            ->orderBy('documents.id', 'DESC');
    }

    public function getProcess(Request $request)
    {
        $search = $request->search;

        if ($request->search == '') {
            switch ($request)
            {
                case($request->pending_change_id == 0 && $request->document_type_id == 0):
                    $query = $this->queryProcess()
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 1 && $request->document_type_id == 0):
                    $query = $this->queryProcess()
                        ->where('documents.document_origin_id', '=', 1)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 2 && $request->document_type_id == 0):
                    $query = $this->queryProcess()
                        ->where('documents.document_origin_id', '!=', 1)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0):
                    $query = $this->queryProcess()
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 1 && $request->document_type_id != 0):
                    $query = $this->queryProcess()
                        ->where('documents.document_origin_id', '=', 1)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 2 && $request->document_type_id != 0):
                    $query = $this->queryProcess()
                        ->where('documents.document_origin_id', '!=', 1)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
            }
        }else {
            $query = $this->queryProcess()
                ->where(function ($q) use ($search) {
                    $q->where('documents.registry', 'like', '%'.$search.'%' )
                        ->orWhere('documents.records', 'like', '%'.$search.'%' );
                })
                ->paginate(5);
        }

        return [
            'pagination' => [
                'total'        => $query->total(),
                'current_page' => $query->currentPage(),
                'per_page'     => $query->perPage(),
                'last_page'    => $query->lastPage(),
                'from'         => $query->firstItem(),
                'to'           => $query->lastItem(),
            ],
            'listProcess' => $query
        ];
    }

    public function setDerive(Request $request)
    {
        $this->validate($request, [
            'document_origin_id'      => 'required|integer|not_in:0',
            'dependency_type_id'      => 'required|integer|not_in:0',
            'dependency_shipping_id'  => 'required|integer|not_in:0',
            'detail_document_type_id' => 'required|integer|not_in:0',
            'document_type_id'        => 'required|integer|not_in:0',
            'affair'                  => 'required',
            'folio'                   => 'required',
            'destinations'            => 'required',
        ], array(
            'document_origin_id.not_in'      => 'Elige orígen del documento',
            'dependency_type_id.not_in'      => 'Elige tipo de dependencia',
            'dependency_shipping_id.not_in'  => 'Elige dependencia',
            'detail_document_type_id.not_in' => 'Elije Tipo de documento',
            'document_type_id.not_in'        => 'Elije documento',
            'affair.required'                => 'El asunto es obligatorio',
            'folio.required'                 => 'Folios',
            'destinations.required'          => 'Debe de asignar alguna dependencia para el documento',
        ));

        $destinations = json_decode($request->destinations, true);
        $actions      = json_decode($request->actions, true);
        $shipping     = json_decode($request->shipping, true);
        $annexes      = json_decode($request->annexes, true);
        $imageAnnexes = $request->images;

        try{
            DB::beginTransaction();

            $countNumber = Document::leftJoin('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
                ->leftJoin('dependencies', 'dependencies.id', '=', 'documentary_procedures.dependency_shipping_id')
                ->where('documents.document_type_id','=', $request->document_type_id)
                ->where('documentary_procedures.dependency_shipping_id', '=', $request->dependency_shipping_id)
                ->where(DB::raw('YEAR(documents.created_at)'), Carbon::now()->year)
                ->value(DB::raw('MAX(number)'));

            $document = new Document();
            $document->registry             = str_pad($countNumber+1, 1, "0", STR_PAD_LEFT);
            $document->reference            = $request->registry;
            $document->affair               = $request->affair;
            $document->description          = $request->description;
            ($annexes['annexes'][0]['archive'] == -1 && $request->annexe == 0) ? $document->annexes = 0 : $document->annexes = 1;
            $document->shipping_date        = Carbon::parse($request->date)->setTimeFromTimeString(date("H:i:s"));
            $document->code                 = $shipping['shipping'][0]['detail'];
            $document->records              = $request->records;
            $document->number               = str_pad($countNumber+1, 6, "0", STR_PAD_LEFT);
            $document->file                 = 'Archive';
            $document->folio                = $request->folio;
            $request->tupa == null ? $document->tupa = 0 : $document->tupa = $request->tupa;
            $document->document_origin_id   = $request->document_origin_id;
            $document->document_priority_id = $request->document_priority_id;
            $document->document_type_id     = $request->document_type_id;
            $document->type_reception_id    = $request->type_reception_id;
            $document->created_by           = $this->getPersonId();
            $document->transparency         = $request->transparency;
            $document->created_at           = Carbon::now();
            $document->updated_at           = Carbon::now();
            $document->save();

            $document_update_registry = Document::findOrFail($document->id);
            $document_update_registry->registry  = str_pad($document->id, 6, "0", STR_PAD_LEFT);
            $document_update_registry->save();

            if (isset($imageAnnexes)) {
                for($i=0; $i<count($annexes['annexes']); $i++)
                {
                    $annexeDocuments = new Annexe();
                    $annexeDocuments->document_id = $document->id;
                    $annexeDocuments->records = $request->records;
                    if ($annexes['annexes'][$i]['annexe'] != '') {
                        $annexeDocuments->annexe = $annexes['annexes'][$i]['annexe'];
                    }else {
                        $annexeDocuments->annexe = 'Descripción de anexo vacío';
                    }
                    $annexeDocuments->file = Storage::disk('s3')->put('files', $imageAnnexes[$i]);
                    $annexeDocuments->save();
                }
            }

            for ( $i=0; $i<count($destinations['destinations']); $i++)
            {
                $procedure = new DocumentaryProcedure();
                $procedure->reception_date          = null;
                $procedure->procedure_date          = null;
                $procedure->document_id             = $document->id;
                $procedure->procedure_state_id      = 1;
                $procedure->person_id               = $this->getPersonId();
                $procedure->dependency_shipping_id  = $request->dependency_shipping_id;
                $procedure->person_reception        = $destinations['destinations'][$i]['id'];
                $procedure->dependency_reception_id = $destinations['destinations'][$i]['dependency_reception_id'];
                $procedure->provided_id             = $actions['actions'][$i]['provided_id'];
                $procedure->created_at              = Carbon::now();
                $procedure->updated_at              = Carbon::now();
                $procedure->save();
            }

            $documentary_procedure = DocumentaryProcedure::findOrFail($request->document_id);
            $documentary_procedure->procedure_state_id = 3;
            $documentary_procedure->procedure_date = Carbon::now();
            $documentary_procedure->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            return $e;
        }

        Session::forget('destinations');
        Session::forget('provided');
        Session::forget('shipping');

        $show_document = Document::where('id',$document->id)->select('records', 'registry')->first();

        return [
            'records'  => $show_document->records,
            'registry' => $show_document->registry,
            'id'       => $document->id
        ];
    }

    public function documentReturn(Request $request)
    {
        $documentary_procedure = DocumentaryProcedure::findOrFail($request->id);
        $documentary_procedure->procedure_state_id = 1;
        $documentary_procedure->reception_date     = null;
        $documentary_procedure->observations       = null;
        $documentary_procedure->save();
    }
}
