<?php

namespace App\Http\Controllers;

use App\Document;
use App\DocumentaryProcedure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DocumentArchiveController extends Controller
{
    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function queryArchive()
    {
        return Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
            ->join('persons AS Receiver', 'documentary_procedures.person_reception', '=', 'Receiver.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documents.records',
                'Dependency.description AS Dependency',
                'Destination.description AS Destination',
                'documents.affair',
                'documents.folio',
                'documents.registry',
                'documents.reference',
                'documents.code',
                'documents.shipping_date',
                'documentary_procedures.observations',
                'documentary_procedures.reception_date',
                'documentary_procedures.procedure_state_id AS state',
                'documentary_procedures.procedure_date',
                'documentary_procedures.id AS documentary_procedure_id',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination',
                'provided.provided',
                'Transmitter.dni AS dniT',
                'Receiver.dni AS dniR',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'document_priorities.priority'
            )
            ->orderBy('documents.id', 'DESC');
    }

    public function getRecords(Request $request)
    {
        $search = $request->search;

        if ($request->search == '') {

            switch ($request)
            {
                case($request->pending_change_id == 0 && $request->document_type_id == 0 && $request->date == 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id == 0 && $request->date == 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0 && $request->date == 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id == 0 && $request->date != 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id == 0 && $request->date == 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id != 0 && $request->date == 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id == 0 && $request->date != 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id == 0 && $request->date == 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id != 0 && $request->date != 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id == 0 && $request->date != 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0 && $request->date != 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id == 0 && $request->date != 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0 && $request->date != 0 && $request->procedure_state_id == 4):
                    $query = $this->queryArchive()
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->where('documentary_procedures.procedure_state_id', 4)
                        ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0 && $request->date == 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id != 0 && $request->date == 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;
                case($request->pending_change_id != 0 && $request->document_type_id != 0 && $request->date != 0 && $request->procedure_state_id != 0):
                    $query = $this->queryArchive()
                        ->where('documents.document_origin_id', '=', $request->pending_change_id)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->whereYear('documents.created_at', '=', $request->date)
                        ->where('documentary_procedures.procedure_state_id', '=', $request->procedure_state_id)
                        ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
                        ->paginate(5);
                    break;

            }

        }else {
            $query = $this->queryArchive()
                ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
                ->where('documentary_procedures.procedure_state_id', 4)
                ->where('documents.records', 'like', '%'.$search.'%' )
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
            'archive' => $query
        ];
    }

    public function recordFetched(Request $request)
    {
        if(!isset($request->observation)) {
            $errors = ['errors' => 'Debe de ingresar el motivo'];
            return response()->json($errors, 422);
        }

        $documentary_procedure = DocumentaryProcedure::findOrFail($request->id);
        $documentary_procedure->procedure_state_id = 4;
        $documentary_procedure->procedure_date     = Carbon::now();
        $documentary_procedure->observations       = $request->observation;
        $documentary_procedure->save();

    }

    public function documentUnarchive(Request $request)
    {
        if(!isset($request->observation)) {
            $errors = ['errors' => 'Debe de ingresar el motivo'];
            return response()->json($errors, 422);
        }

        switch ($request)
        {
            case $request->state == 4:
                $documentary_procedure = DocumentaryProcedure::findOrFail($request->id);
                $documentary_procedure->procedure_state_id = 2;
                $documentary_procedure->procedure_date     = null;
                $documentary_procedure->observations       = $request->observation;
                $documentary_procedure->save();
                break;
            case $request->state == 5:
                $documentary_procedure = DocumentaryProcedure::findOrFail($request->id);
                $documentary_procedure->procedure_state_id = 2;
                $documentary_procedure->procedure_date     = null;
                $documentary_procedure->observations       = $request->observation;
                $documentary_procedure->save();
                break;
            case $request->state == 6:
                $documentary_procedure = DocumentaryProcedure::findOrFail($request->id);
                $documentary_procedure->procedure_state_id = 2;
                $documentary_procedure->reception_date     = null;
                $documentary_procedure->procedure_date     = null;
                $documentary_procedure->observations       = $request->observation;
                $documentary_procedure->save();
                break;
        }
    }
}
