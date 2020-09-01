<?php

namespace App\Http\Controllers;

use App\ChargeAssignment;
use App\Document;
use App\Dependency;
use App\DocumentaryProcedure;
use App\DocumentProcedure;
use App\DocumentType;
use App\Entity;
use App\Person;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DocumentReportController extends Controller
{
    private function getEntityId()
    {
        return Dependency::where('id', $this->getDependencyId())->value('entity_id');
    }

    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    private function queryReport()
    {
        return Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
            ->join('persons AS Receiver', 'documentary_procedures.person_reception', '=', 'Receiver.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documentary_procedures.provided_id',
                'documentary_procedures.dependency_shipping_id',
                'documentary_procedures.dependency_reception_id',
                'documents.records',
                'Dependency.description AS Dependency',
                'Destination.description AS Destination',
                'documents.affair',
                'documents.number',
                'documents.folio',
                'documents.tupa',
                'documents.registry',
                'documents.reference',
                'document_types.type',
                'documents.description',
                'documents.annexes',
                'documents.code',
                'documents.shipping_date',
                'documents.document_priority_id',
                'documents.document_origin_id',
                'documents.type_reception_id',
                'documents.created_at',
                'documents.created_by',
                DB::raw('DATE_FORMAT(documents.shipping_date,"%Y-%m-%d") AS shipping_show'),
                'documents.document_type_id',
                'documents.shipping_date',
                'documentary_procedures.id AS documentary_procedure_id',
                'documentary_procedures.reception_date',
                'documentary_procedures.procedure_state_id AS state',
                'documentary_procedures.procedure_date',
                'documentary_procedures.person_id',
                'documentary_procedures.person_reception',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination',
                'provided.provided',
                'Transmitter.dni AS dniT',
                'Receiver.dni AS dniR',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'document_priorities.priority'
            )
            ->where('EntityDependency.id', $this->getEntityId())
            ->where('Dependency.description', '!=', 'USUARIO EXTERNO')
            ->orderBy('documents.id', 'DESC');
    }

    public function getTableDocumentReport(Request $request)
    {
        $filters = [
            'pending_change_id' => $request->pending_change_id,
            'transparency'      => $request->transparency,
            'date'              => $request->date,
            'initial_date'      => $request->initial_date,
            'final_date'        => $request->final_date,
            'document_type'     => $request->document_type,
            'dependency'        => $request->dependency,
            'search'            => $request->search,
            'typeDependency'    => $request->typeDependency
        ];

        if (ChargeAssignment::charge() == 2) {
            $query = $this->queryReport()
                ->where(function ($q) use ($filters) {
                if ($filters['pending_change_id']) {
                    $q->where('documentary_procedures.procedure_state_id', $filters['pending_change_id']);
                }
                if ($filters['transparency']) {
                    $q->where('documents.transparency', 1);
                }
                if ($filters['date']) {
                    $q->whereYear('documents.created_at', $filters['date']);
                }
                if ($filters['search']) {
                    $q->where('documents.records', 'like', '%'.$filters['search'].'%' );
                }
                /*if ($filters['initial_date']) {
                    $q->whereDate(DB::raw('DATE(documents.created_at)'), $filters['initial_date']);
                }*/
                if ($filters['final_date']) {
                    $q->whereBetween(DB::raw('DATE(documents.created_at)'), [$filters['initial_date'], $filters['final_date']]);
                }
                if ($filters['document_type']) {
                    $q->where('documents.document_type_id', $filters['document_type']);
                }
                if ($filters['dependency']) {
                    if ($filters['typeDependency']) {
                        $q->where('documentary_procedures.dependency_reception_id', $filters['dependency']);
                    }else {
                        $q->where('documentary_procedures.dependency_shipping_id', $filters['dependency']);
                    }
                }

            })->paginate(5);
        }else {
            $query = $this->queryReport()
                ->where('Dependency.id', $this->getDependencyId())
                ->where(function ($q) use ($filters) {
                if ($filters['pending_change_id']) {
                    $q->where('documentary_procedures.procedure_state_id', $filters['pending_change_id']);
                }
                if ($filters['transparency']) {
                    $q->where('documents.transparency', 1);
                }
                if ($filters['date']) {
                    $q->whereYear('documents.created_at', $filters['date']);
                }
                if ($filters['search']) {
                    $q->where('documents.records', 'like', '%'.$filters['search'].'%' );
                }
                /*if ($filters['initial_date']) {
                    $q->whereDate(DB::raw('DATE(documents.created_at)'), $filters['initial_date']);
                }*/
                if ($filters['final_date']) {
                    $q->whereBetween(DB::raw('DATE(documents.created_at)'), [$filters['initial_date'], $filters['final_date']]);
                }
                if ($filters['document_type']) {
                    $q->where('documents.document_type_id', $filters['document_type']);
                }
                if ($filters['dependency']) {
                    if ($filters['typeDependency']) {
                        $q->where('documentary_procedures.dependency_reception_id', $filters['dependency']);
                    }else {
                        $q->where('documentary_procedures.dependency_shipping_id', $filters['dependency']);
                    }
                }

            })->paginate(5);
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
            'queryReport' => $query
        ];
    }

    private function queryReportInternal()
    {
        return Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
            ->join('persons AS Receiver', 'documentary_procedures.person_reception', '=', 'Receiver.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documentary_procedures.provided_id',
                'documentary_procedures.dependency_shipping_id',
                'documentary_procedures.dependency_reception_id',
                'documents.records',
                'Dependency.description AS Dependency',
                'Destination.description AS Destination',
                'documents.affair',
                'documents.number',
                'documents.folio',
                'documents.tupa',
                'documents.registry',
                'documents.reference',
                'document_types.type',
                'documents.description',
                'documents.annexes',
                'documents.code',
                'documents.shipping_date',
                'documents.document_priority_id',
                'documents.document_origin_id',
                'documents.type_reception_id',
                'documents.created_at',
                'documents.created_by',
                DB::raw('DATE_FORMAT(documents.shipping_date,"%Y-%m-%d") AS shipping_show'),
                'documents.document_type_id',
                'documents.shipping_date',
                'documentary_procedures.id AS documentary_procedure_id',
                'documentary_procedures.reception_date',
                'documentary_procedures.procedure_state_id AS state',
                'documentary_procedures.procedure_date',
                'documentary_procedures.person_id',
                'documentary_procedures.person_reception',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination',
                'provided.provided',
                'Transmitter.dni AS dniT',
                'Receiver.dni AS dniR',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'document_priorities.priority'
            )
            ->where('EntityDependency.id', $this->getEntityId())
            ->where('Dependency.description', '!=', 'USUARIO EXTERNO')
            ->orderBy('documents.id', 'DESC');
    }

    public function getTableDocumentReportInternal(Request $request)
    {
        $filters = [
            'pending_change_id' => $request->pending_change_id,
            'transparency'      => $request->transparency,
            'date'              => $request->date,
            'initial_date'      => $request->initial_date,
            'final_date'        => $request->final_date,
            'document_type'     => $request->document_type,
            'dependency'        => $request->dependency,
            'search'            => $request->search,
            'typeDependency'    => $request->typeDependency
        ];

        $query = $this->queryReportInternal()->where(function ($q) use ($filters) {
            if ($filters['pending_change_id']) {
                $q->where('documentary_procedures.procedure_state_id', $filters['pending_change_id']);
            }
            if ($filters['transparency']) {
                $q->where('documents.transparency', 1);
            }
            if ($filters['date']) {
                $q->whereYear('documents.created_at', $filters['date']);
            }
            if ($filters['search']) {
                $q->where('documents.records', 'like', '%'.$filters['search'].'%' );
            }
            /*if ($filters['initial_date']) {
                $q->whereDate(DB::raw('DATE(documents.created_at)'), $filters['initial_date']);
            }*/
            if ($filters['final_date']) {
                $q->whereBetween(DB::raw('DATE(documents.created_at)'), [$filters['initial_date'], $filters['final_date']]);
            }
            if ($filters['document_type']) {
                $q->where('documents.document_type_id', $filters['document_type']);
            }
            if ($filters['dependency']) {
                if ($filters['typeDependency']) {
                    $q->where('documentary_procedures.dependency_reception_id', $filters['dependency']);
                }else {
                    $q->where('documentary_procedures.dependency_shipping_id', $filters['dependency']);
                }
            }

        })->paginate(5);

        return [
            'pagination' => [
                'total'        => $query->total(),
                'current_page' => $query->currentPage(),
                'per_page'     => $query->perPage(),
                'last_page'    => $query->lastPage(),
                'from'         => $query->firstItem(),
                'to'           => $query->lastItem(),
            ],
            'queryReport' => $query
        ];
    }

    public function getTableReport(Request $request)
    {
        $entity = Entity::where('id', $this->getEntityId())->value('description');
        $dependency = Dependency::where('id', $this->getDependencyId())->value('description');
        $person = Person::where('id', $this->getPersonId())->value(DB::raw('CONCAT(firstName, " ", lastName)'));

        $filters = [
            'pending_change_id' => $request->pending_change_id,
            'transparency'      => $request->transparency,
            'date'              => $request->date,
            'initial_date'      => $request->initial_date,
            'final_date'        => $request->final_date,
            'document_type'     => $request->document_type,
            'dependency'        => $request->dependency,
            'typeDependency'    => $request->typeDependency
        ];

        $query = $this->queryReport()->where(function ($q) use ($filters) {
            if ($filters['pending_change_id']) {
                $q->where('documentary_procedures.procedure_state_id', $filters['pending_change_id']);
            }
            if ($filters['transparency']) {
                $q->where('documents.transparency', 1);
            }
            if ($filters['date']) {
                $q->whereYear('documents.created_at', $filters['date']);
            }
            /*if ($filters['initial_date']) {
                $q->whereDate(DB::raw('DATE(documents.created_at)'), $filters['initial_date']);
            }*/
            if ($filters['initial_date'] && $filters['final_date']) {
                $q->whereBetween(DB::raw('DATE(documents.created_at)'), [$filters['initial_date'], $filters['final_date']]);
            }
            if ($filters['document_type']) {
                $q->where('documents.document_type_id', $filters['document_type']);
            }
            if ($filters['dependency']) {
                if ($filters['typeDependency']) {
                    $q->where('documentary_procedures.dependency_reception_id', $filters['dependency']);
                }else {
                    $q->where('documentary_procedures.dependency_shipping_id', $filters['dependency']);
                }
            }

        })->get();

        return [
            'tableReport' => $query,
            'count'       => count($query),
            'entity'      => $entity,
            'dependency'  => $dependency,
            'person'      => $person
        ];
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'document_origin_id'      => 'required|integer|not_in:0',
            'dependency_shipping_id'  => 'required|integer|not_in:0',
            'detail_document_type_id' => 'required|integer|not_in:0',
            'document_type_id'        => 'required|integer|not_in:0',
            'affair'                  => 'required',
            'folio'                   => 'required',
        ], array(
            'document_origin_id.not_in'      => 'Elige orígen del documento',
            'dependency_shipping_id.not_in'  => 'Elige dependencia',
            'detail_document_type_id.not_in' => 'Elije Tipo de documento',
            'document_type_id.not_in'        => 'Elije documento',
            'affair.required'                => 'El asunto es obligatorio',
            'folio.required'                 => 'Ingresa número de folios',
        ));

        $states = [];
        $requirements = [];

        $states = $request->states;
        $requirements = $request->requirements;

        if (isset($request->number)) {

            $now = new DateTime();
            $count = $request->number;
            $type = DocumentType::where('id', $request->document_type_id)->value('type');

            $shipping = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->select(
                    DB::raw('CONCAT("'.$type.'", "-", "'.$count.'", "-", "'.$now->format('Y').'", "-", entities.abbreviation, "-", dependencies.abbreviation) AS detail')
                )
                ->where('dependencies.id', '=', $request->dependency_shipping_id)
                ->first();
        }

        try{
            DB::beginTransaction();

            $document = Document::findOrFail($request->document_id);
            $document->affair               = $request->affair;
            $document->description          = $request->description;
            $document->annexes              = $request->annexes;
            $document->shipping_date        = Carbon::now();
            if(isset($request->number)) {
                $document->code             = $shipping->detail;
            }
            if (isset($request->number)) {
                $document->number           = $request->number;
            }
            $document->file                 = 'Archive';
            $document->folio                = $request->folio;
            $document->tupa                 = $request->tupa;
            $document->document_origin_id   = $request->document_origin_id;
            $document->document_priority_id = $request->document_priority_id;
            $document->document_type_id     = $request->document_type_id;
            $document->type_reception_id    = $request->type_reception_id;
            $document->created_by           = $this->getPersonId();
            $document->created_at           = $request->created_at;
            $document->updated_at           = Carbon::now();
            $document->save();

            $procedure = DocumentaryProcedure::findOrFail($request->documentary_procedure_id);
            $procedure->document_id             = $document->id;
            $procedure->procedure_state_id      = 1;
            $procedure->person_id               = $request->person_id;
            $procedure->dependency_shipping_id  = $request->dependency_shipping_id;
            $procedure->dependency_reception_id = $request->dependency_reception_id;
            $procedure->provided_id             = $request->provided_id;
            $procedure->person_reception        = $request->person_reception;
            switch($request) {
                case ($request->state_id == 1):
                    $procedure->procedure_state_id = $request->state_id;
                    $procedure->reception_date     = null;
                    $procedure->procedure_date     = null;
                break;
                case ($request->state_id == 2):
                    $procedure->procedure_state_id = $request->state_id;
                    $procedure->reception_date     = Carbon::now();
                    $procedure->procedure_date     = null;
                break;
                case ($request->state_id == 3):
                    $procedure->procedure_state_id = $request->state_id;
                    $procedure->reception_date     = Carbon::now();
                    $procedure->procedure_date     = Carbon::now();
                break;
                case ($request->state_id == 4):
                    $procedure->procedure_state_id = $request->state_id;
                    $procedure->reception_date     = Carbon::now();
                    $procedure->procedure_date     = Carbon::now();
                break;
                case ($request->state_id == 6):
                    $procedure->procedure_state_id = $request->state_id;
                    $procedure->reception_date     = Carbon::now();
                    $procedure->procedure_date     = Carbon::now();
                break;
            }
            $procedure->created_at              = $request->created_at;
            $procedure->updated_at              = Carbon::now();
            $procedure->save();

            if (isset($request->requirements)) {

                $document_id = Document::where('records', $request->records)->where('tupa', 1)->value('id');

                $ids = DocumentProcedure::select('id')->where('document_id', $document_id)->get();

                for ($i = 0; $i < count($requirements); $i++)
                {
                    $document_procedure = DocumentProcedure::findOrFail($ids[$i]['id']);
                    $document_procedure->date                     = Carbon::parse($request->date)->setTimeFromTimeString(date("H:i:s"));
                    $document_procedure->document_id              = $document_id;
                    $document_procedure->procedure_requirement_id = $requirements[$i]['id'];
                    $document_procedure->process_state_id         = 2;
                    $document_procedure->created_at               = $request->created_at;
                    $document_procedure->updated_at               = Carbon::now();
                    $document_procedure->save();
                }

                for ($i = 0; $i < count($requirements); $i++)
                {
                    $procedure = DocumentProcedure::findOrFail($ids[$i]['id']);
                    for ($j = 0; $j < count($states); $j++)
                    {
                        if ($states[$j] == $requirements[$i]['id']) {
                            $procedure->process_state_id = 1;
                            $procedure->save();
                        }
                    }
                }
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            return $e;
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            $documentary_procedure_ids = DocumentaryProcedure::select('id')->where('document_id', $request->document_id)->get();

            if (count($documentary_procedure_ids) > 1) {
                DocumentaryProcedure::findOrFail($request->documentary_procedure_id)->forceDelete();
            }else {
                DocumentaryProcedure::findOrFail($request->documentary_procedure_id)->forceDelete();

                $document_procedure_ids = DocumentProcedure::select('id')->where('document_id', $request->document_id)->get();

                if (count($document_procedure_ids) > 0)
                {
                    foreach($document_procedure_ids as $document_procedure_id)
                    {
                        DocumentProcedure::findOrFail($document_procedure_id->id)->forceDelete();
                    }
                }

                Document::findOrFail($request->document_id)->forceDelete();
            }

            DB::commit();

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
}
