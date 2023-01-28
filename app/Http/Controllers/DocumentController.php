<?php

namespace App\Http\Controllers;

use App\Annexe;
use App\ChargeAssignment;
use App\Dependency;
use App\DependencyType;
use App\DetailDocumentType;
use App\Document;
use App\DocumentaryProcedure;
use App\DocumentOrigin;
use App\DocumentPriority;
use App\DocumentProcedure;
use App\DocumentType;
use App\Entity;
use App\Person;
use App\Procedure;
use App\ProcedureRequirement;
use App\Provided;
use App\Requirement;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Date\Date;

class DocumentController extends Controller
{
    public function index()
    {
        $date = Carbon::now()->format('Y-m-d');

        $origins = DocumentOrigin::select('id', 'origin')->get();

        $dependency_type = DependencyType::all();

        $dependency_type_id = DB::table('charge_assignments')->join('dependencies', 'dependencies.id', '=', 'charge_assignments.dependency_id')
            ->where('charge_assignments.person_id', auth()->user()->person->id)
            ->value('dependencies.dependency_type_id');

        $type_reception = DB::table('type_receptions')->select('id', 'reception')->get();

        $dependency_process_id = Dependency::where('start_procedure', 1)->value('id');

        $person_id = ChargeAssignment::where('dependency_id', $dependency_process_id)->value('person_id');

        $details = DetailDocumentType::select('id', 'detail')->get();

        $priorities = DocumentPriority::select('id', 'priority')->get();

        if ($this->getRole() == 1) {

            $entities = Entity::select('id', 'description AS label')->get();

            $entity_external = Entity::select('id', 'description AS label')->get();

        } else {

            $entities = Entity::select('id', 'description AS label')
                ->where('id', '!=', 1)
                ->get();
            $entity_external = Entity::select('id', 'description AS label')
                ->where('id', '!=', 1)
                ->where('id', '!=', $this->getEntityId())
                ->get();
        }

        $provided = Provided::select('id', 'provided AS label')->get();

        $auth = $this->getRole();

        $dependency = $this->getDependencyId();

        $entity = $this->getEntityId();

        return [
            'origin'                => $origins,
            'type_reception'        => $type_reception,
            'date'                  => $date,
            'entity'                => $entity,
            'dependency_type'       => $dependency_type,
            'dependency_type_id'    => $dependency_type_id,
            'dependency_process_id' => $dependency_process_id,
            'person_id'             => $person_id,
            'details'               => $details,
            'priorities'            => $priorities,
            'entities'              => $entities,
            'provided'              => $provided,
            'dependency'            => $dependency,
            'auth'                  => $auth,
            'entity_external'       => $entity_external,
        ];
    }

    public function getDocumentToSent()
    {
        $provided = Provided::select('id', 'provided AS label')->get();

        $priorities = DocumentPriority::select('id', 'priority')->get();

        $document_type = DB::table('document_types')->select('id', 'type AS label')->orderBy('type', 'asc')->get();

        $sent_document_dependencies = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->select('dependencies.id', 'dependencies.description AS label', 'persons.id AS person_id')
            ->where('charge_assignments.charge_state_id', '=', 1)
            ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
            ->where('dependencies.entity_id', $this->getEntityId())
            ->get();

        return [
            'provided' => $provided,
            'priorities' => $priorities,
            'sent_document_dependencies' => $sent_document_dependencies,
            'document_type' => $document_type
        ];
    }

    public function getDocumentModalInternal($id)
    {
        $documentModal = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons', 'documentary_procedures.person_reception', '=', 'persons.id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities', 'Destination.entity_id', '=', 'entities.id')
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documentary_procedures.id AS documentary_procedure_id',
                'documents.records',
                'documents.code',
                'documents.type_reception_id',
                'documentary_procedures.dependency_shipping_id',
                'documents.document_type_id',
                'documents.document_origin_id',
                'documents.registry',
                'documents.reference',
                'document_types.type',
                'entities.description AS entity',
                'documents.document_priority_id',
                'document_priorities.priority',
                'Destination.description AS Destination',
                'documentary_procedures.dependency_reception_id',
                'documentary_procedures.provided_id',
                'documentary_procedures.person_id',
                'documentary_procedures.person_reception',
                'provided.provided',
                'documents.affair',
                'documents.description',
                'documents.annexes',
                'documents.folio',
                'documents.tupa',
                'documents.shipping_date',
                'documents.created_by',
                'persons.dni',
                DB::raw('DATE_FORMAT(documents.shipping_date,"%Y-%m-%d") AS shipping_show'),
                'documentary_procedures.reception_date',
                DB::raw('CONCAT(firstName, " ", lastName) AS fullName'),
                'Destination.entity_id'
            )
            ->where('documentary_procedures.dependency_shipping_id', $this->getDependencyId())
            ->where('documentary_procedures.person_id', $this->getPersonId())
            ->where('documents.id', $id)
            ->first();

        return [
            'documentModal' => $documentModal
        ];
    }

    public function getDocumentModalExternal($id, $dependency_id, $person_id)
    {
        $documentModal = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons', 'documentary_procedures.person_reception', '=', 'persons.id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities', 'Destination.entity_id', '=', 'entities.id')
            ->join('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documentary_procedures.id AS documentary_procedure_id',
                'documents.records',
                'documents.code',
                'documents.type_reception_id',
                'documentary_procedures.dependency_shipping_id',
                'documents.document_type_id',
                'documents.document_origin_id',
                'documents.registry',
                'documents.reference',
                'document_types.type',
                'entities.description AS entity',
                'documents.document_priority_id',
                'document_priorities.priority',
                'Destination.description AS Destination',
                'documentary_procedures.dependency_reception_id',
                'documentary_procedures.provided_id',
                'documentary_procedures.person_id',
                'documentary_procedures.person_reception',
                'documentary_procedures.procedure_state_id',
                'provided.provided',
                'documents.affair',
                'documents.description',
                'documents.annexes',
                'documents.folio',
                'documents.tupa',
                'documents.shipping_date',
                'documents.created_by',
                'documents.number',
                'persons.dni',
                DB::raw('DATE_FORMAT(documents.shipping_date,"%Y-%m-%d") AS shipping_show'),
                'documentary_procedures.reception_date',
                DB::raw('CONCAT(firstName, " ", lastName) AS fullName'),
                'Destination.entity_id'
            )
            ->where('documentary_procedures.dependency_shipping_id', $dependency_id)
            ->where('documentary_procedures.person_id', $person_id)
            ->where('documents.id', $id)
            ->first();

        return [
            'documentModal' => $documentModal
        ];
    }

    public function getPersonReception(Request $request)
    {
        $person_id = ChargeAssignment::where('dependency_id', $request->id)->value('person_id');

        return [
            'person_id' => $person_id
        ];
    }

    private function getRole()
    {
        return ChargeAssignment::charge();
    }

    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function getEntityId()
    {
        return Dependency::where('id', $this->getDependencyId())->value('entity_id');
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    public function getDependencyCharge($id)
    {
        $dependency_charge = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->select('charge_assignments.charge', DB::raw('CONCAT(firstName, " ", lastName) AS fullName'), 'persons.id')
            ->where('dependencies.id', '=', $id)
            ->where('charge_assignments.charge_state_id', '=', 1)
            ->first();

        return [
            'dependency_charge' => $dependency_charge
        ];
    }

    public function getDependencyBringType($id)
    {
        if ($this->getRole() == 1 || $this->getRole() == 2) {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('charge_assignments.deleted_at', '=', null)
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.dependency_type_id', $id)
                ->where('dependencies.entity_id', $this->getEntityId())
                ->where('dependencies.id','!=', $this->getDependencyId())
                ->groupBy('charge_assignments.dependency_id')
                ->get();
        }else {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('charge_assignments.deleted_at', '=', null)
                ->where('charge_assignments.role_id', $this->getRole())
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.dependency_type_id', $id)
                ->where('dependencies.entity_id', $this->getEntityId())
                ->get();
        }

        return [
            'dependencies'      => $dependencies
        ];
    }

    public function getPersonCharge(Request $request)
    {
        $search = $request->search;

        if ($search != '')
        {
            $person_charge = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'dependencies.entity_id', '=', 'entities.id')
                ->select(
                    'entities.description as entity',
                    'dependencies.description as dependency',
                    'charge_assignments.charge',
                    DB::raw('CONCAT(firstName, " ", lastName) AS fullName')
                )
                ->where('persons.dni', 'like', $search.'%')
                ->orWhere('persons.lastName', 'like', $search.'%')
                ->first();

            return [
                'person_charge' => $person_charge
            ];
        }else{
            return ['person_charge' => ''];
        }
    }

    public function getPersonQuery(Request $request)
    {
        $newQuery = $request->newQuery;
        $documentOrigin = $request->documentOrigin;

        if (isset($newQuery) && $documentOrigin != 3) {

            $person_charge = Entity::join('dependencies','dependencies.entity_id','=','entities.id')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('persons', 'persons.id', '=', 'charge_assignments.person_id')
                ->select(
                    'dependencies.id',
                    'entities.description as entity',
                    'dependencies.description as dependency',
                    DB::raw('CONCAT(persons.firstName, " ", persons.lastName) AS fullName'),
                    'persons.dni',
                    'persons.firstName',
                    'persons.lastName',
                    'persons.id AS person_id'
                )
                ->where('charge_assignments.charge', 'Ciudadano')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('dependencies.entity_id', $this->getEntityId())
                ->where(function ($query) use ($newQuery){
                    $query->where('persons.dni', 'like', '%'.$newQuery.'%');
                })
                ->get();
        }else if (isset($newQuery) && $documentOrigin = 3) {
            $person_charge = Person::select('dni', 'firstName', 'lastName', 'id AS person_id', DB::raw('CONCAT(firstName, " ", lastName) AS fullName'))
                ->where(function ($query) use ($newQuery){
                    $query->where('dni', 'like', '%'.$newQuery.'%');
                })
                ->get();
        }else {
            $person_charge = [];
        }

        return [
            'person_charge' => $person_charge
        ];
    }

    public function getDocumentEntity()
    {
        if ($this->getRole() == 1) {

            $entities = Entity::select('id', 'description AS label')->get();

        }else if($this->getRole() == 2){

            $entities = Entity::select('id', 'description AS label')
                ->where('id', '!=', 1)
                ->where('id', '!=', $this->getEntityId())
                ->get();
        }

        return [
            'entities' => $entities
        ];
    }

    public function getDocumentDependency(Request $request)
    {
        if ($this->getRole() == 1 || $this->getRole() == 2) {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('charge_assignments.deleted_at', '=', null)
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.entity_id', $request->id)
                ->groupBy('charge_assignments.dependency_id')
                ->get();
        }else {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('charge_assignments.deleted_at', '=', null)
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.entity_id', $request->id)
                ->get();
        }

        return [
            'dependencies' => $dependencies
        ];
    }

    public function getDocumentDependencyCharge($id)
    {
        $dependency_charge = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->select('charge_assignments.charge', DB::raw('CONCAT(firstName, " ", lastName) AS fullName'), 'persons.id')
            ->where('dependencies.id', '=', $id)
            ->where('charge_assignments.charge_state_id', '=', 1)
            ->first();
        return [
            'dependency_charge' => $dependency_charge
        ];
    }

    public function getDocumentProcedureSearchExternal(Request $request)
    {
        $newQuery = $request->newQuery;

        $document_id = DocumentProcedure::where('records', $request->records)->value('document_id');

        $procedure_requirement_id = DocumentProcedure::where('records', $request->records)->value('procedure_requirement_id');

        $procedure_id = ProcedureRequirement::where('id', $procedure_requirement_id)->value('procedure_id');

        $procedure_like_id = Procedure::where('procedures.description', 'like', $newQuery.'%')->value('id');

        $procedures = Procedure::select('procedures.id', 'procedures.description', 'dependencies.id AS dependency_id')
            ->join('attention_procedures', 'attention_procedures.procedure_id', '=', 'procedures.id')
            ->join('dependencies', 'attention_procedures.dependency_id', '=', 'dependencies.id')
            ->join('attention_types', 'attention_types.id', '=', 'attention_procedures.attention_type_id')
            ->where('attention_procedures.deleted_at', null)
            ->where('attention_types.id', '=', 2)
            ->where('procedures.description', 'like', '%'.$newQuery.'%')
            ->get();
        return [
            'procedures'              => $procedures,
            'requirement_document_id' => $document_id,
            'procedure_id'            => $procedure_id,
            'procedure_like_id'       => $procedure_like_id
        ];
    }

    public function getDocumentProcedureSearch(Request $request)
    {
        $newQuery = $request->newQuery;

        $procedures = Procedure::select('procedures.id', 'procedures.description', 'dependencies.id AS dependency_id')
            ->join('attention_procedures', 'attention_procedures.procedure_id', '=', 'procedures.id')
            ->join('dependencies', 'attention_procedures.dependency_id', '=', 'dependencies.id')
            ->where('attention_procedures.deleted_at', null)
            ->where('procedures.description', 'like', '%'.$newQuery.'%')
            ->distinct()
            ->get();

        return [
            'procedures' => $procedures
        ];
    }

    public function getDocumentProcedureQuery($id)
    {
        $requirements = Requirement::join('procedure_requirements', 'procedure_requirements.requirement_id', 'requirements.id')
            ->select('procedure_requirements.id', 'requirements.description', 'procedure_requirements.cost')
            ->where('procedure_requirements.procedure_id', $id)
            ->get();

        return [
            'requirements' => $requirements
        ];
    }

    public function getDocumentProcedureProcess($id)
    {
        $requirements = DocumentProcedure::join('procedure_requirements', 'procedure_requirements.id', 'document_procedures.procedure_requirement_id')
            ->join('requirements', 'requirements.id', 'procedure_requirements.requirement_id')
            ->select('procedure_requirements.id', 'requirements.description', 'procedure_requirements.cost', 'document_procedures.process_state_id')
            ->where('document_procedures.records', $id)
            ->get();

        return [
            'requirements' => $requirements
        ];
    }

    public function getDocumentProcedureQueryState($id)
    {
        $requirement_states = DocumentProcedure::join('procedure_requirements', 'procedure_requirements.id', 'document_procedures.procedure_requirement_id')
            ->join('requirements', 'requirements.id', 'procedure_requirements.requirement_id')
            ->select(
                'procedure_requirements.id',
                'requirements.description',
                'procedure_requirements.cost',
                'document_procedures.process_state_id',
                'document_procedures.file'
                )
            ->where('document_procedures.document_id', $id)
            ->get();

        return [
            'requirement_states' => $requirement_states
        ];
    }

    public function getDocumentDependencyDestination(Request $request)
    {
        $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
            ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->whereNull('charge_assignments.deleted_at')
            ->where('dependencies.id','!=', $request->selected_id)
            ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
            ->where('dependencies.entity_id', $request->id)
            ->where('charge_assignments.charge_state_id', 1)
            ->get();

        return [
            'dependencies' => $dependencies,
        ];
    }

    public function getDocumentDependencyReport()
    {
        if (ChargeAssignment::charge() == 2) {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.entity_id', $this->getEntityId())
                ->get();
        } else {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description AS label')
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.entity_id', $this->getEntityId())
                ->where('dependencies.id', $this->getDependencyId())
                ->get();
        }

        return [
            'dependencies' => $dependencies
        ];
    }

    public function getDocumentDependencyReception(Request $request)
    {
        $reception =  ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->select('charge_assignments.charge', DB::raw('CONCAT(firstName, " ", lastName) AS fullName'), 'persons.id')
            ->where('dependencies.id', '=', $request->id)
            ->first();

        return [
            'reception' => $reception
        ];
    }

    private function checkDestinations($destinations, $dependency)
    {
        for ($i = 0; $i < count($destinations);$i++)
        {
            if ($destinations[$i]['dependency_reception_id'] == $dependency) {
                return true;
            }
        }
        return false;
    }

    public  function setDocumentDestination(Request $request)
    {
        if ($request->number != '') {
            $this->validate($request, [
                'dependency_id'           => 'required|integer|not_in:0',
                'provided_id'             => 'required|integer|not_in:0',
                'detail_document_type_id' => 'required|integer|not_in:0',
                'document_type_id'        => 'required|integer|not_in:0',
                'number'                  => 'required|integer',
            ], array(
                'dependency_id.not_in'           => 'Elige una dependencia',
                'provided_id.not_in'             => 'Elige un proveído',
                'detail_document_type_id.not_in' => 'Elije Tipo de documento',
                'document_type_id.not_in'        => 'Elije documento',
                'number.required'                => 'Ingresa el número',
                'number.integer'                 => 'Debe ser número entero',
            ));
        }
        $this->validate($request, [
            'dependency_id'           => 'required|integer|not_in:0',
            'provided_id'             => 'required|integer|not_in:0',
            'detail_document_type_id' => 'required|integer|not_in:0',
            'document_type_id'        => 'required|integer|not_in:0'
        ], array(
            'dependency_id.not_in'           => 'Elige una dependencia',
            'provided_id.not_in'             => 'Elige un proveído',
            'detail_document_type_id.not_in' => 'Elije Tipo de documento',
            'document_type_id.not_in'        => 'Elije documento'
        ));


        if (Session::get('destinations') && Session::get('provided') && Session::get('shipping') && $request->countDocument == 0) {

            Session::forget('destinations');
            Session::forget('provided');
            Session::forget('shipping');

            Session::put('destinations', array());
            Session::put('provided', array());
            Session::put('shipping', array());

        }

        if (!(Session::get('destinations') && Session::get('provided') && Session::get('shipping'))) {

            Session::put('destinations', array());
            Session::put('provided', array());
            Session::put('shipping', array());
        }

        $destinations = Session::get('destinations');
        $provided = Session::get('provided');
        $shipping = Session::get('shipping');

        $now = new DateTime();

        $countSession = count(Session::get('destinations'));

        $countNumber = Document::leftJoin('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->where('documents.document_type_id','=', $request->document_type_id)
            ->where('documentary_procedures.dependency_shipping_id', '=', $request->dependency_shipping_id)
            ->where(DB::raw('YEAR(documents.created_at)'), Carbon::now()->year)
            ->value(DB::raw('MAX(number)'));

        $type = DocumentType::where('id', $request->document_type_id)->value('type');

        if ($countSession == 0)
        {
            if($countNumber == 0)
            {
                $count = str_pad(1, 6, "0", STR_PAD_LEFT);
            }else {
                $count = str_pad($countNumber+1, 6, "0", STR_PAD_LEFT);
            }

        }else {
            $count = str_pad($countNumber+1, 6, "0", STR_PAD_LEFT);
        }

        if (count($request->destinations) == 0) {

            if (isset($request->number)) {
                $count = str_pad($request->number, 6, "0", STR_PAD_LEFT);
            }

            $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->select(
                    DB::raw('CONCAT("'.$type.'", "-", "'.$count.'", "-", "'.$now->format('Y').'", "-", entities.abbreviation, "-", dependencies.abbreviation) AS detail')
                )
                ->where('dependencies.id', '=', $request->dependency_shipping_id)
                ->first();

            $shipping[] = $query;

            if ($request->dni != 0)
            {
                $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                    ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                    ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                    ->select(
                        'dependencies.abbreviation AS abbreviation_dependency',
                        'dependencies.id AS dependency_reception_id',
                        'entities.abbreviation AS abbreviation_entity',
                        'dependencies.description',
                        'charge_assignments.charge',
                        DB::raw('CONCAT(firstName, " ", lastName) AS fullName'),
                        'persons.id'
                    )
                    ->where('dependencies.id', '=', $request->dependency_id)
                    ->where('persons.dni', '=', $request->dni)
                    ->where('charge_assignments.charge_state_id', '=', 1)
                    ->where('charge_assignments.deleted_at', '=', null)
                    ->first();

                $destinations[] = $query;
            } else {
                $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                    ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                    ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                    ->select(
                        'dependencies.abbreviation AS abbreviation_dependency',
                        'dependencies.id AS dependency_reception_id',
                        'entities.abbreviation AS abbreviation_entity',
                        'dependencies.description',
                        'charge_assignments.charge',
                        DB::raw('CONCAT(firstName, " ", lastName) AS fullName'),
                        'persons.id'
                    )
                    ->where('dependencies.id', '=', $request->dependency_id)
                    ->where('charge_assignments.charge_state_id', '=', 1)
                    ->where('charge_assignments.deleted_at', '=', null)
                    ->first();

                $destinations[] = $query;
            }
        }else {

            if ($this->checkDestinations($request->destinations, $request->dependency_id) == false) {

                if (isset($request->number)) {
                    $count = str_pad($request->number, 6, "0", STR_PAD_LEFT);
                }

                $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                    ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                    ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                    ->select(
                        DB::raw('CONCAT("'.$type.'", "-", "'.$count.'", "-", "'.$now->format('Y').'", "-", entities.abbreviation, "-", dependencies.abbreviation) AS detail')
                    )
                    ->where('dependencies.id', '=', $request->dependency_shipping_id)
                    ->first();

                $shipping[] = $query;

                if ($request->dni != 0)
                {
                    $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                        ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                        ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                        ->select(
                            'dependencies.abbreviation AS abbreviation_dependency',
                            'dependencies.id AS dependency_reception_id',
                            'entities.abbreviation AS abbreviation_entity',
                            'dependencies.description',
                            'charge_assignments.charge',
                            DB::raw('CONCAT(firstName, " ", lastName) AS fullName'),
                            'persons.id'
                        )
                        ->where('dependencies.id', '=', $request->dependency_id)
                        ->where('persons.dni', '=', $request->dni)
                        ->where('charge_assignments.charge_state_id', '=', 1)
                        ->where('charge_assignments.deleted_at', '=', null)
                        ->first();

                    $destinations[] = $query;
                } else {
                    $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                        ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                        ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                        ->select(
                            'dependencies.abbreviation AS abbreviation_dependency',
                            'dependencies.id AS dependency_reception_id',
                            'entities.abbreviation AS abbreviation_entity',
                            'dependencies.description',
                            'charge_assignments.charge',
                            DB::raw('CONCAT(firstName, " ", lastName) AS fullName'),
                            'persons.id'
                        )
                        ->where('dependencies.id', '=', $request->dependency_id)
                        ->where('charge_assignments.charge_state_id', '=', 1)
                        ->where('charge_assignments.deleted_at', '=', null)
                        ->first();

                    $destinations[] = $query;
                }

            }
        }

        $query = Provided::select('provided.id AS provided_id', 'provided')->where('id', $request->provided_id)->first();

        $provided[] = $query;

        Session::put('destinations', $destinations);
        $destinations = Session::get('destinations');

        Session::put('provided', $provided);
        $provided = Session::get('provided');

        Session::put('shipping', $shipping);
        $shipping = Session::get('shipping');

        return [
            'destinations' => $destinations,
            'provided'     => $provided,
            'shipping'     => $shipping
        ];
    }

    public function deleteProvided($id)
    {
        $destinations = Session::get('destinations');
        unset($destinations[$id]);
        $destinations = array_values($destinations);

        foreach ($destinations as $i => $destination)
        {
            $query = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
                ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->select(
                    'dependencies.abbreviation AS abbreviation_dependency',
                    'dependencies.id AS dependency_reception_id',
                    'entities.abbreviation AS abbreviation_entity',
                    'dependencies.description',
                    'charge_assignments.charge',
                    DB::raw('CONCAT(firstName, " ", lastName) AS fullName')
                )
                ->where('dependencies.id', '=', $destination->dependency_reception_id)
                ->first();

            $destinations[$i] = $query;

        }
        Session(['destinations' => $destinations]);

        $provided = Session::get('provided');
        unset($provided[$id]);
        $provided = array_values($provided);

        foreach($provided as $i =>$provide)
        {
            $query = Provided::select('provided.id AS provided_id', 'provided')->where('id', $provide->provided_id)->first();
            $provided[$i] = $query;
        }
        Session(['provided' => $provided ]);

        $shipping = Session::get('shipping');
        unset($shipping[$id]);
        $shipping = array_values($shipping);

        Session(['shipping' => $shipping]);

        return [
            'destinations' => $destinations,
            'provided'     => $provided,
            'shipping'     => $shipping
        ];
    }

    public function getDocumentTypes($id)
    {
        $document_type = DB::table('document_types')
            ->select('id', 'type AS label')
            ->whereNull('deleted_at')
            ->where('detail_document_type_id', $id)
            ->orderBy('type')
            ->get();

        return [
            'document_type' => $document_type
        ];
    }

    public function getDocumentReferences(Request $request)
    {
        $newQuery = $request->queryRef;

        if($newQuery != null)
        {
            $references = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
                ->join('persons', 'persons.id', '=', 'documentary_procedures.person_id')
                ->join('dependencies', 'dependencies.id', '=', 'documentary_procedures.dependency_shipping_id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->select(
                    'documents.id',
                    'documents.records',
                    'documents.affair',
                    'documents.shipping_date',
                    'dependencies.description',
                    'entities.description AS entity',
                    DB::raw('CONCAT(persons.firstName, " ", persons.lastName) AS fullName')
                )
                ->where('documents.registry', 'like', $newQuery)
                ->first();

            return [
                'references' => $references
            ];
        }

        return [
            'references' => ''
        ];

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'document_origin_id'      => 'required|integer|not_in:0',
            'dependency_shipping_id'  => 'required|integer|not_in:0',
            'detail_document_type_id' => 'required|integer|not_in:0',
            'document_type_id'        => 'required|integer|not_in:0',
            'affair'                  => 'required',
            'folio'                   => 'required',
            'destinations'            => 'required'
        ], array(
            'document_origin_id.not_in'      => 'Elige orígen del documento',
            'dependency_shipping_id.not_in'  => 'Elige dependencia',
            'detail_document_type_id.not_in' => 'Elije Tipo de documento',
            'document_type_id.not_in'        => 'Elije documento',
            'affair.required'                => 'El asunto es obligatorio',
            'folio.required'                 => 'Folios',
            'destinations.required'          => 'Debe de asignar alguna dependencia para el documento'
        ));

        try{
            DB::beginTransaction();

            $destinations = json_decode($request->destinations, true);
            $requirements = json_decode($request->requirements, true);
            $actions      = json_decode($request->actions, true);
            $shipping     = json_decode($request->shipping, true);
            $references   = $request->reference;
            $annexes      = json_decode($request->annexes, true);
            $append       = json_decode($request->append, true);
            $transparency = json_decode($request->transparency, true);
            $imageAnnexes = $request->imageAnnexes;

            $countNumber = Document::leftJoin('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
                ->where('documents.document_type_id','=', $request->document_type_id)
                ->where('documentary_procedures.dependency_shipping_id', '=', $request->dependency_shipping_id)
                ->where(DB::raw('YEAR(documents.created_at)'), Carbon::now()->year)
                ->value(DB::raw('MAX(number)'));

            $countRecord = Document::value(DB::raw('MAX(records)'));
            $record = Document::where('registry', $request->reference)->value('records');

            $document = new Document();
            $document->registry = str_pad($countRecord+1, 1, "0", STR_PAD_LEFT);
            isset($references) ? $document->reference = implode(",", $references) : $document->reference = '';
            $document->affair               = $request->affair;
            $document->description          = $request->description;
            $annexes['annexes'][0]['archive'] == -1 ? $document->annexes = 0 : $document->annexes = 1;
            $document->shipping_date        = Carbon::parse($request->date)->setTimeFromTimeString(date("H:i:s"));
            $document->code                 = $shipping['shipping'][0]['detail'];
            $append == true ? $document->records = $record : $document->records  = str_pad($countRecord+1, 6, "0", STR_PAD_LEFT);
            isset($request->number) ? $document->number = str_pad($request->number, 6, "0", STR_PAD_LEFT) : $document->number = str_pad($countNumber+1, 6, "0", STR_PAD_LEFT);
            $document->file                 = 'Archive';
            $document->folio                = $request->folio;
            $document->tupa                 = $request->tupa;
            $document->document_origin_id   = $request->document_origin_id;
            $document->document_priority_id = $request->document_priority_id;
            $document->document_type_id     = $request->document_type_id;
            $document->type_reception_id    = $request->type_reception_id;
            $document->created_by           = $this->getPersonId();
            $document->transparency         = $transparency;
            $document->created_at           = Carbon::now();
            $document->updated_at           = Carbon::now();
            $document->save();

            $document_update_registry = Document::findOrFail($document->id);
            $document_update_registry->registry = str_pad($document->id, 6, "0", STR_PAD_LEFT);
            $document_update_registry->save();

            if (isset($imageAnnexes)) {
                for($i=0; $i<count($annexes['annexes']); $i++)
                {
                    $annexeDocuments = new Annexe();
                    $annexeDocuments->document_id = $document->id;
                    $annexes['annexes'][$i]['annexe'] != '' ? $annexeDocuments->annexe = $annexes['annexes'][$i]['annexe'] : $annexeDocuments->annexe = 'Descripción de anexo vacío';
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
                $request->person_id != 0 ? $procedure->person_id = $request->person_id : $procedure->person_id = $this->getPersonId();
                $procedure->dependency_shipping_id  = $request->dependency_shipping_id;
                $procedure->person_reception        = $destinations['destinations'][$i]['id'];
                $procedure->dependency_reception_id = $destinations['destinations'][$i]['dependency_reception_id'];
                $procedure->provided_id             = $actions['actions'][$i]['provided_id'];
                $procedure->created_at              = Carbon::now();
                $procedure->updated_at              = Carbon::now();
                $procedure->save();
            }

            if (isset($requirements)) {

                for ( $i=0; $i<count($requirements['requirements']); $i++)
                {
                    $document_procedure = new DocumentProcedure();
                    $document_procedure->date                     = Carbon::parse($request->date)->setTimeFromTimeString(date("H:i:s"));
                    $document_procedure->observation              = 'Ninguno';
                    $document_procedure->records                  = $document->records;
                    $document_procedure->document_id              = $document->id;
                    $document_procedure->procedure_requirement_id = $requirements['requirements'][$i]['id'];
                    $document_procedure->process_state_id         = 2;
                    $document_procedure->created_at               = Carbon::now();
                    $document_procedure->updated_at               = Carbon::now();
                    $document_procedure->save();
                }

                $ids = DocumentProcedure::select('id')->where('document_id', $document->id)->get();

                $images          = $request->images;
                $idImage         = $request->idImage;
                $idCode          = $request->idCode;
                $codeRequirement = $request->codeRequirement;

                for ($i = 0; $i < count($requirements['requirements']); $i++)
                {
                    $procedure = DocumentProcedure::findOrFail($ids[$i]['id']);
                    for ($j = 0; $j < count($idImage); $j++)
                    {
                        if ($idImage[$j] == $requirements['requirements'][$i]['id']) {
                            $procedure->file = Storage::disk('s3')->put('files', $images[$j]);
                            if ($idImage[$j] == isset($idCode[$i]) ? $idCode[$i] : '0') {
                                $procedure->code = $codeRequirement[$j];
                            }
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

        Session::forget('destinations');
        Session::forget('provided');
        Session::forget('shipping');

        $show_document = Document::select(
                'documents.records',
                'documents.registry'
            )
            ->where('documents.id',$document->id)
            ->first();

        return [
            'records'           => $show_document->records,
            'registry'          => $show_document->registry,
            'id'                => $document->id
        ];
    }

    public function getAnnexes($id)
    {
        $annexes = Document::select('dependencies.description', 'annexes.id', 'annexes.annexe', 'annexes.file')
            ->join('annexes', 'documents.id', '=', 'annexes.document_id')
            ->join('documentary_procedures', 'documents.id', '=', 'documentary_procedures.document_id')
            ->join('dependencies', 'documentary_procedures.dependency_shipping_id', '=', 'dependencies.id')
            ->where('documents.records', $id)
            ->get();            

        return [
            'annexes' => $annexes
        ];
    }

    public function getPdfDocument($id)
    {
        $documents = Document::join('documentary_procedures', 'documents.id', '=', 'documentary_procedures.document_id')
            ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
            ->join('persons AS Receiver', 'documentary_procedures.person_reception', '=', 'Receiver.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->select(
                'documents.records',
                'documents.registry',
                'documents.code',
                'documents.affair',
                'documents.description',
                'documents.annexes',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'Dependency.description AS Dependency',
                'Destination.description AS Destination',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination'
            )
            ->where('documents.id',$id)
            ->first();

        $destinations = DocumentaryProcedure::join('persons', 'persons.id', '=', 'documentary_procedures.person_reception')
            ->join('dependencies', 'dependencies.id', '=', 'documentary_procedures.dependency_reception_id')
            ->select(
                DB::raw('CONCAT(persons.firstName, " ", persons.lastName) AS fullName'),
                'dependencies.description AS dependency'
            )
            ->where('document_id', $id)
            ->get();

        $document_references = Document::where('documents.id',$id)->value('reference');

        $references = explode(',', $document_references);

        $codes = Document::select('code')->whereIn('id', $references)->get();

        Date::setLocale('es');
        $date =  Date::now()->format('j F Y');

        $pdf = \PDF::loadView('pdf.document-internal', ['documents' => $documents, 'codes' => $codes, 'date' => $date, 'destinations' => $destinations]);
        return $pdf->download($documents->code.".pdf");
    }

    public function checkedRequirements(Request $request)
    {
        $document_procedure_id = DocumentProcedure::where('procedure_requirement_id', $request->id)
            ->where('document_id', $request->document_id)
            ->value('id');

        $document_procedure = DocumentProcedure::findOrFail($document_procedure_id);
        $document_procedure->process_state_id = $request->check;
        $document_procedure->save();
    }

    public function updateObservationRequirement(Request $request)
    {
        $document_procedure = DocumentProcedure::findOrFail($request->document_procedure_id);
        $document_procedure->observation = $request->observation;
        $document_procedure->save();
    }

    public function sentDocumentUpdate(Request $request)
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
            $document->shipping_date        = Carbon::parse($request->shipping_date)->setTimeFromTimeString(date("H:i:s"));
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
            $document->created_by           = $request->created_by;
            $document->updated_at           = Carbon::now();
            $document->save();

            $procedure = DocumentaryProcedure::findOrFail($request->documentary_procedure_id);
            $procedure->reception_date          = null;
            $procedure->procedure_date          = null;
            $procedure->document_id             = $document->id;
            $procedure->procedure_state_id      = 1;
            $procedure->person_id               = $request->person_id;
            $procedure->dependency_shipping_id  = $request->dependency_shipping_id;
            $procedure->dependency_reception_id = $request->dependency_reception_id;
            $procedure->provided_id             = $request->provided_id;
            $procedure->person_reception        = $request->person_reception;
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

    public function getTupa(Request $request)
    {
        $tupa = Document::where('records', $request->records)->where('tupa', 1)->value('tupa');

        return ['tupa' => $tupa];
    }

    public function getDocumentAuth()
    {
        $auth = $this->getRole();

        return ['auth' => $auth];
    }

    public function getDocumentCharts()
    {
        $states_1 = DocumentaryProcedure::join('dependencies', 'dependencies.id', '=', 'documentary_procedures.dependency_reception_id')
            ->select('dependencies.description', DB::raw('COUNT(documentary_procedures.dependency_reception_id) AS state'))
            ->where('procedure_state_id', 1)
            ->groupBy('dependency_reception_id')
            ->get();

        $states_2 = DocumentaryProcedure::join('dependencies', 'dependencies.id', '=', 'documentary_procedures.dependency_reception_id')
            ->select('dependencies.description', DB::raw('COUNT(documentary_procedures.dependency_reception_id) AS state'))
            ->where('procedure_state_id', 2)
            ->groupBy('dependency_reception_id')
            ->get();

        /*$months = DocumentaryProcedure::select(DB::raw('monthname(created_at) AS month'), DB::raw('COUNT(month(created_at)) AS quantity'))
            ->where('procedure_state_id', 2)
            ->groupBy(DB::raw('month(created_at)'), DB::raw('monthname(created_at)'))
            ->get();*/

        return [
            'states' => $states_1,
            'attend' => $states_2
        ];
    }
}
