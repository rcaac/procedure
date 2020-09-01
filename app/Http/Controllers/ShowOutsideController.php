<?php

namespace App\Http\Controllers;

use App\ChargeAssignment;
use App\DocumentaryProcedure;
use App\DocumentType;
use App\Provided;
use App\User;
use App\Person;
use App\Document;
use App\Procedure;
use App\Requirement;
use App\IdentificationDocument;
use App\DocumentProcedure;
use App\Annexe;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Storage;

class ShowOutsideController extends Controller
{
    public function getDocumentShowOutside()
    {
        return view('tracing.tracing');
    }

    public function process()
    {
        return view('process.process');
    }

    public function monitoring(Request $request)
    {
        $arrayTracing = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
            ->join('persons AS Receiver', 'documentary_procedures.person_reception', '=', 'Receiver.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
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
                'Transmitter.dni AS dniT',
                'Receiver.dni AS dniR',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'documentary_procedures.observations'
            )
            ->where('documents.records', $request->record )
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
            ->where('document_procedures.records', $request->record)
            ->get();

        $details = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->select(
                'documents.records',
                'documentary_procedures.procedure_state_id AS state',
                'Dependency.description AS Dependency',
                'Destination.description AS Destination'
            )
            ->where('documents.records', $request->record)
            ->orderBy('documents.created_at', 'DESC')
            ->first();

        return [
            'tracing'      => $arrayTracing,
            'requirements' => $requirements,
            'details'      => $details
        ];

    }

    public function getPdfMonitoring($id)
    {
        $tracings = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
            ->join('persons AS Receiver', 'documentary_procedures.person_reception', '=', 'Receiver.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
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
                'Transmitter.dni AS dniT',
                'Receiver.dni AS dniR',
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'documentary_procedures.observations'
            )
            ->where('documents.records', $id)
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
            ->where('document_procedures.records', $id)
            ->get();

        $details = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->select(
                'documents.records',
                'documentary_procedures.procedure_state_id AS state',
                'Dependency.description AS Dependency',
                'Destination.description AS Destination'
            )
            ->where('documents.records', $id)
            ->orderBy('documents.created_at', 'DESC')
            ->first();

        $code = Document::where('records', $id)->value('code');

        $documents = Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('dependencies AS Destination', 'Destination.id', '=', 'documentary_procedures.dependency_reception_id')
            ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
            ->join('entities AS EntityDestination', 'Destination.entity_id', '=', 'EntityDestination.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->select(
                'Dependency.description AS Dependency',
                'Destination.description AS Destination',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination'

            )
            ->where('documents.records', $id)
            ->where('documentary_procedures.procedure_state_id', '!=', 5)
            ->first();

        Date::setLocale('es');
        $date =  Date::now()->format('j F Y');

        $pdf = \PDF::loadView('pdf.tracing-external', [
            'tracings'     => $tracings,
            'requirements' => $requirements,
            'details'      => $details,
            'documents'    => $documents,
            'date'         => $date
        ]);

        return $pdf->download($code.".pdf");
    }

    public function getDocumentProcess(Request $request)
    {
        $newQuery = $request->newQuery;

        $procedures = Procedure::select('procedures.id', 'procedures.description', 'dependencies.id AS dependency_id')
        ->join('attention_procedures', 'attention_procedures.procedure_id', '=', 'procedures.id')
        ->join('dependencies', 'attention_procedures.dependency_id', '=', 'dependencies.id')
        ->join('attention_types', 'attention_types.id', '=', 'attention_procedures.attention_type_id')
        ->where('attention_procedures.deleted_at', null)
        ->where('attention_types.id', '=', 2)
        ->where('procedures.description', 'like', '%'.$newQuery.'%')
        ->get();

        return [
            'procedures' => $procedures
        ];
    }

    public function getDocumentProcessQuery($id)
    {
        $requirements = Requirement::join('procedure_requirements', 'procedure_requirements.requirement_id', 'requirements.id')
            ->select('procedure_requirements.id', 'requirements.description', 'procedure_requirements.cost')
            ->where('procedure_requirements.procedure_id', $id)
            ->get();

        return [
            'requirements' => $requirements
        ];
    }

    public function getIdentification()
    {
        $identification = IdentificationDocument::all();

        return ['identification' => $identification];
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

    public function getDocumentTypeExtrernal()
    {
        $document_type = DocumentType::select('id', 'type')
            ->whereNull('deleted_at')
            ->orderBy('type')
            ->get();

        return [
            'document_type' => $document_type
        ];
    }

    public function getDocumentDestinationExternal(Request $request)
    {
        $countNumber = Document::leftJoin('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->where('documents.document_type_id','=', $request->document_type_id)
            ->where('documentary_procedures.dependency_shipping_id', '=', $request->dependency_shipping_id)
            ->where(DB::raw('YEAR(documents.created_at)'), Carbon::now()->year)
            ->value(DB::raw('MAX(number)'));

        if($countNumber == 0)
        {
            $count = str_pad(1, 6, "0", STR_PAD_LEFT);
        }else {
            $count = str_pad($countNumber+1, 6, "0", STR_PAD_LEFT);
        }

        $shipping[] = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
            ->select(
                DB::raw('CONCAT("'.$request->type.'", "-", "'.$count.'", "-", "'.(new DateTime())->format('Y').'", "-", entities.abbreviation, "-", dependencies.abbreviation) AS detail')
                )
            ->where('dependencies.id', '=', $request->dependency_shipping_id)
            ->first();

        $destinations[] = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
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
            ->first();

        $provided[] = Provided::select('provided.id AS provided_id', 'provided')->where('id', $request->provided_id)->first();

        return [
            'destinations' => $destinations,
            'provided'     => $provided,
            'shipping'     => $shipping
        ];
    }

    private function validateDocument($request)
    {
        return $this->validate( $request, [
            'affair'                  => 'required',
            'description'             => 'required',
            'folio'                   => 'required|numeric',
            'firstName'               => 'required',
            'lastName'                => 'required',
            'dni'                     => 'required|numeric|digits:8',
            'phone'                   => 'required|numeric',
            'direction'               => 'required',
            'agree'                   => 'accepted',
            'email'                   => 'required|email',
        ], array(
            'affair.required'                => 'El asunto del documento es obligatorio',
            'description.required'           => 'El contenido del documento es obligatorio',
            'folio.required'                 => 'Número de folios',
            'folio.numeric'                  => 'Debe ser número',
            'firstName.required'             => 'El nombre es obligatorio',
            'lastName.required'              => 'El apellido es obligatorio',
            'dni.required'                   => 'El dni es obligatorio',
            'dni.numeric'                    => 'El dni debe de contener caracteres numéricos',
            'dni.digits'                     => 'El dni debe contener 8 dígitos',
            'phone.required'                 => 'El teléfono es obligatorio',
            'phone.numeric'                  => 'El Teléfono debe de contener caracteres numéricos',
            'direction.required'             => 'Ingresa una dirección',
            'agree.accepted'                 => 'La declaración debe ser aceptada',
            'email.required'                 => 'El correo electrónico es obligatorio',
            'email.email'                    => 'Debe ser un correo electrónico válido',
        ));
    }

    public function store(Request $request)
    {
        $this->validateDocument($request);                  

        $destinations    = json_decode($request->destinations, true);
        $requirements    = json_decode($request->requirements, true);
        $actions         = json_decode($request->actions, true);
        $shipping        = json_decode($request->shipping, true);
        $annexes         = json_decode($request->annexes, true);
        $imageAnnexes    = $request->imageAnnexes;
        $images          = $request->images;
        $idImage         = $request->idImage;
        $idCode          = $request->idCode;
        $codeRequirement = $request->codeRequirement;

        try{
            DB::beginTransaction();

            $data = Person::where('dni', '=', $request->dni)->select('phone', 'direction')->first();

            if (Person::where('dni', '=', $request->dni)->exists() == false) {
                $person = new Person();
                $person->firstName                  = $request->firstName;
                $person->lastName                   = $request->lastName;
                $person->dni                        = $request->dni;
                $person->phone                      = $request->phone;
                $person->email                      = $request->email;
                $person->direction                  = $request->direction;
                $person->identification_document_id = $request->identification_document_id;
                $person->created_by                 = 0;
                $person->save();

                $user = new User();
                $user->user = $request->dni;
                $user->password = bcrypt($request->dni);
                $user->person_id  = $person->id;
                $user->created_by = 0;
                $user->save();

                $charge = new ChargeAssignment();
                $charge->charge          = 'Ciudadano';
                $charge->type            = 2;
                $charge->person_id       = $person->id;
                $charge->role_id         = 4;
                $charge->dependency_id   = 46;
                $charge->charge_state_id = 1;
                $charge->created_by      = 0;
                $charge->save();

                $person_id = $person->id;

            }else if ((Person::where('dni', '=', $request->dni)->exists() == true) && ($data->phone == null) && ($data->direction == null)) {
                $person = Person::findOrFail(Person::where('dni', '=', $request->dni)->value('id'));
                $person->phone = $request->phone;
                $person->direction = $request->direction;
                $person->save();

                $person_id = $person->id;
            }else {
                $person_id = Person::where('dni', $request->dni)->value('id');
            }

            $countNumber = Document::leftJoin('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
                ->where('documents.document_type_id','=', $request->document_type_id)
                ->where('documentary_procedures.dependency_shipping_id', '=', json_decode($request->dependency_shipping_id, true))
                ->where(DB::raw('YEAR(documents.created_at)'), Carbon::now()->year)
                ->value(DB::raw('MAX(number)'));

            $countRecord = Document::value(DB::raw('MAX(records)'));
            $record = Document::where('registry', $request->reference)->value('records');

            $document = new Document();
            $document->registry             = str_pad($countRecord+1, 1, "0", STR_PAD_LEFT);
            isset($request->reference) ? $document->reference = $request->reference : $document->reference = '';
            $document->affair               = $request->affair;
            $document->description          = $request->description;
            $annexes['annexes'][0]['archive'] == -1 ? $document->annexes = 0 : $document->annexes = 1;
            $document->shipping_date        = Carbon::parse($request->date)->setTimeFromTimeString(date("H:i:s"));
            $document->code                 = $shipping['shipping'][0]['detail'];
            $request->append == true ? $document->records = $record : $document->records = str_pad($countRecord+1, 6, "0", STR_PAD_LEFT);
            isset($request->number) ? $document->number = str_pad($request->number, 6, "0", STR_PAD_LEFT) : $document->number = str_pad($countNumber+1, 6, "0", STR_PAD_LEFT);
            $document->file                 = 'Archive';
            $document->folio                = $request->folio;
            $document->tupa                 = $request->tupa;
            $document->document_origin_id   = 3;
            $document->document_priority_id = 2;
            $document->document_type_id     = $request->document_type_id;
            $document->type_reception_id    = 1;
            $document->created_by           = $person_id;
            $document->transparency         = 0;
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
                $procedure->person_id               = $person_id;
                $procedure->dependency_shipping_id  = json_decode($request->dependency_shipping_id, true);
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
            }
            if (isset($images)) {
                $ids = DocumentProcedure::select('id')->where('document_id', $document->id)->get();
                
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
            DB::rollback();
            return $e;
        }

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

    public function getShowPersonDate($query)
    {
        $personDate = Person::select('dni', 'email', 'firstName', 'lastName', 'phone', 'direction')
            ->where('dni', $query)
            ->first();

        return ['personDate' => $personDate];
    }

    public function printPdf($id)
    {
        $document_origin_id = Document::where('id', $id)->value('document_origin_id');

        if ($document_origin_id == 2)
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
        }else {
            $documents = Document::join('documentary_procedures', 'documents.id', '=', 'documentary_procedures.document_id')
                ->join('persons AS Transmitter', 'documentary_procedures.person_id', '=', 'Transmitter.id')
                ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
                ->join('entities AS EntityDependency', 'Dependency.entity_id', '=', 'EntityDependency.id')
                ->select(
                    'documents.records',
                    'documents.registry',
                    'documents.affair',
                    'documents.description',
                    'documents.annexes',
                    'documents.code',
                    'Transmitter.dni',
                    'Transmitter.direction',
                    'Transmitter.phone',
                    DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                    'EntityDependency.description AS entityDependency'
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

            Date::setLocale('es');
            $date =  Date::now()->format('j F Y');

            $pdf = \PDF::loadView('pdf.document-external', ['documents' => $documents, 'date' => $date, 'destinations' => $destinations]);
            return $pdf->download($documents->code.".pdf");
        }
    }    
}