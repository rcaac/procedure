<?php

namespace App\Http\Controllers;

use App\Document;
use App\DocumentProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentTracingController extends Controller
{
    public function getTracing(Request $request)
    {
        $tracing = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
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
                'documentary_procedures.reception_date',
                'documentary_procedures.procedure_state_id AS state',
                'documentary_procedures.procedure_date',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination',
                'provided.provided',
                'Transmitter.dni AS dniT',
                'Receiver.dni AS dniR',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'documentary_procedures.observations'
            )
            ->where('documents.records', $request->search)
            ->where('documentary_procedures.procedure_state_id', '!=', 5)
            ->orderBy('documents.id', 'ASC')
            ->distinct()
            ->get();

        $requirements = DocumentProcedure::join('documents', 'documents.id', '=', 'document_procedures.document_id')
            ->join('procedure_requirements', 'document_procedures.procedure_requirement_id', '=', 'procedure_requirements.id')
            ->join('requirements', 'requirements.id', '=', 'procedure_requirements.requirement_id')
            ->select(
                'requirements.description',
                'document_procedures.observation',
                'procedure_requirements.cost',
                'document_procedures.process_state_id AS state'
            )
            ->where('document_procedures.records', $request->search)
            ->get();

        return [
            'tracing' => $tracing,
            'requirements' => $requirements
        ];
    }

    public function getTracingRequirement($id)
    {
        $requirements = DocumentProcedure::join('procedure_requirements', 'procedure_requirements.id', '=', 'document_procedures.procedure_requirement_id')
            ->join('requirements', 'requirements.id', '=', 'procedure_requirements.requirement_id')
            ->select(
                'document_procedures.document_id',
                'procedure_requirements.id',
                'procedure_requirements.cost',
                'requirements.description',
                'document_procedures.id AS document_procedure_id',
                'document_procedures.observation',
                'document_procedures.date',
                'document_procedures.process_state_id AS state',
                'document_procedures.file'
            )
            ->where('document_procedures.records', $id)
            ->get();

        return [
            'requirements' => $requirements
        ];
    }
}
