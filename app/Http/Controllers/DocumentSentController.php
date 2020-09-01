<?php

namespace App\Http\Controllers;

use App\Annexe;
use App\ChargeAssignment;
use App\Document;
use App\DocumentaryProcedure;
use App\DocumentProcedure;
use App\DocumentType;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DocumentSentController extends Controller
{
    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    private function getRole()
    {
        return ChargeAssignment::charge();
    }

    private function query()
    {
        return Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
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
            ->orderBy('documents.id', 'DESC');
    }

    public function getSent(Request $request)
    {
        $search = $request->search;

        if ($request->search == '') {
            switch ($request) {
                case($request->pending_change_id == 0 && $request->document_type_id == 0):
                    $query = $this->query()
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 1 && $request->document_type_id == 0):
                    $query = $this->query()
                        ->where('documents.document_origin_id', '=', 1)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 2 && $request->document_type_id == 0):
                    $query = $this->query()
                        ->where('documents.document_origin_id', '!=', 1)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0):
                    $query = $this->query()
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 1 && $request->document_type_id != 0):
                    $query = $this->query()
                        ->where('documents.document_origin_id', '=', 1)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 2 && $request->document_type_id != 0):
                    $query = $this->query()
                        ->where('documents.document_origin_id', '!=', 1)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
            }
        }else {
            $query = $this->query()
                ->where(function ($q) use ($search) {
                    $q->where('documents.registry', 'like', '%'.$search.'%' )
                        ->orWhere('documents.records', 'like', '%'.$search.'%' );
                })
                ->paginate(5);
        }

        $auth = $this->getRole();

        return [
            'pagination' => [
                'total'        => $query->total(),
                'current_page' => $query->currentPage(),
                'per_page'     => $query->perPage(),
                'last_page'    => $query->lastPage(),
                'from'         => $query->firstItem(),
                'to'           => $query->lastItem(),
            ],
            'sent' => $query,
            'auth' => $auth,
        ];
    }

    public function documentInvalid(Request $request)
    {
        if(!isset($request->observation)) {
            $errors = ['errors' => 'Debe de ingresar el motivo'];
            return response()->json($errors, 422);
        }

        $documentary = DocumentaryProcedure::findOrFail($request->documentary_procedure_id);
        $documentary->procedure_state_id = 5;
        $documentary->procedure_date     = Carbon::now();
        $documentary->observations       = $request->observation;
        $documentary->save();


        $document_procedures = DocumentProcedure::select('id')->where('document_id', $request->id)->get();

        foreach ($document_procedures as $document_procedure) {
            $document = DocumentProcedure::findOrFail($document_procedure->id);
            $document->process_state_id = 3;
            $document->date             = Carbon::now();
            $document->save();
        }
    }

    public function documentFinalize(Request $request)
    {
        if(!isset($request->observation)) {
            $errors = ['errors' => 'Debe de ingresar el motivo'];
            return response()->json($errors, 422);
        }

        $documentary = DocumentaryProcedure::findOrFail($request->documentary_procedure_id);
        $documentary->procedure_state_id = 6;
        $documentary->reception_date     = Carbon::now();
        $documentary->procedure_date     = Carbon::now();
        $documentary->observations       = $request->observation;
        $documentary->save();


        $document_procedures = DocumentProcedure::select('id')->where('document_id', $request->id)->get();

        foreach ($document_procedures as $document_procedure) {
            $document = DocumentProcedure::findOrFail($document_procedure->id);
            $document->process_state_id = 4;
            $document->date             = Carbon::now();
            $document->save();
        }
    }

    public function getPrintDocument($id)
    {
        $show_document = Document::join('documentary_procedures', 'documents.id', '=', 'documentary_procedures.document_id')
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
                DB::raw('CONCAT(Transmitter.firstName, " ", Transmitter.lastName) AS fullNameT'),
                DB::raw('CONCAT(Receiver.firstName, " ", Receiver.lastName) AS fullNameR'),
                'Dependency.description AS Dependency',
                'Destination.description AS Destination',
                'EntityDependency.description AS entityDependency',
                'EntityDestination.description AS entityDestination'
            )
            ->where('documents.id',$id)
            ->first();

        $document_reference = Document::where('documents.id',$id)->value('reference');

        $reference_id = (string)((int)($document_reference));

        $code = Document::where('id', $reference_id)->value('code');

        return [
            'records'           => $show_document->records,
            'registry'          => $show_document->registry,
            'code'              => $show_document->code,
            'affair'            => $show_document->affair,
            'description'       => $show_document->description,
            'fullNameT'         => $show_document->fullNameT,
            'fullNameR'         => $show_document->fullNameR,
            'dependency'        => $show_document->Dependency,
            'destination'       => $show_document->Destination,
            'entityDependency'  => $show_document->entityDependency,
            'entityDestination' => $show_document->entityDestination,
            'reference'         => $code
        ];
    }

    public function getDocumentSend($id)
    {
        $dependency_charge = ChargeAssignment::join('persons','charge_assignments.person_id','=','persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->select('persons.id')
            ->where('dependencies.id', '=', $id)
            ->where('charge_assignments.charge_state_id', '=', 1)
            ->first();

        return [
            'dependency_charge' => $dependency_charge->id
        ];
    }

    public function setDocumentsSent(Request $request)
    {
        $dependency_reception_id = DocumentaryProcedure::where('document_id', $request->document_id)
            ->where('dependency_reception_id', $request->dependency_reception_id)
            ->value('dependency_reception_id');

        if ($dependency_reception_id == $request->dependency_reception_id)
        {
            $errors = ['errors' => 'No se puede reenviar a la misma dependencia'];
            return response()->json($errors, 422);
        }

        $procedure = new DocumentaryProcedure();
        $procedure->reception_date          = null;
        $procedure->procedure_date          = null;
        $procedure->document_id             = $request->document_id;
        $procedure->procedure_state_id      = 1;
        $procedure->person_id               = $request->person_id;
        $procedure->dependency_shipping_id  = $request->dependency_shipping_id;
        $procedure->person_reception        = $request->person_reception;
        $procedure->dependency_reception_id = $request->dependency_reception_id;
        $procedure->provided_id             = $request->provided_id;
        $procedure->created_at              = Carbon::now();
        $procedure->updated_at              = Carbon::now();
        $procedure->save();
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
            $procedure->updated_at              = Carbon::now();
            $procedure->save();            

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            return $e;
        }
    }

    public function documentRequirementUpdate(Request $request)
    {
        try{
            DB::beginTransaction();

            $document = Document::findOrFail($request->document_id);
            if($request->affair != null){
                $document->affair = $request->affair;
            }
            $document->tupa   = 1;
            $document->save();
            
            $ids = DocumentProcedure::select('id')->where('document_id', $request->document_id)->get();
            $images = $request->images;
            $idArchives = $request->ids;
            $requirements = json_decode($request->requirements, true);

            if(count($ids) > 0) {
                for ($i = 0; $i < count($ids); $i++)
                {
                    $procedure = DocumentProcedure::findOrFail($ids[$i]['id']);
                    for ($j = 0; $j < count($idArchives); $j++)
                    {
                        if ((int)$idArchives[$j] == $procedure->procedure_requirement_id) {
                            $procedure->file = Storage::disk('s3')->put('files', $images[$j]);
                            $procedure->save();
                        }
                    }
                }
            }else {
                for ( $i=0; $i<count($requirements['requirements']); $i++)
                {
                    $document_procedure = new DocumentProcedure();
                    $document_procedure->date                     = Carbon::parse($request->date)->setTimeFromTimeString(date("H:i:s"));
                    $document_procedure->observation              = 'Ninguno';
                    $document_procedure->records                  = $request->records;
                    $document_procedure->document_id              = $request->document_id;
                    $document_procedure->procedure_requirement_id = $requirements['requirements'][$i]['id'];
                    $document_procedure->process_state_id         = 2;
                    $document_procedure->created_at               = Carbon::now();
                    $document_procedure->updated_at               = Carbon::now();
                    $document_procedure->save();
                }
                if (isset($idArchives)) {
                    $requirement_ids = DocumentProcedure::select('id')->where('document_id', $request->document_id)->get();

                    for ($i = 0; $i < count($idArchives); $i++)
                    {
                        $procedure = DocumentProcedure::findOrFail($requirement_ids[$i]['id']);
                        for ($j = 0; $j < count($idArchives); $j++)
                        {
                            if ((int)$idArchives[$j] == $procedure->procedure_requirement_id) {
                                $procedure->file = Storage::disk('s3')->put('files', $images[$j]);
                                $procedure->save();
                            }
                        }
                    }
                }
            }    

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function deleteRequirement($id)
    {
        try{
            DB::beginTransaction();
            $document = Document::findOrFail($id);
            $document->affair = 'Asunto pendiente de modificar....';
            $document->tupa   = 0;
            $document->save();

            $document_procedure_ids = DocumentProcedure::select('id')->where('document_id', $id)->get();

            foreach ($document_procedure_ids as $id)
            {
                $file = DocumentProcedure::where('id', $id->id)->value('file');
                
                DocumentProcedure::findOrFail($id->id)->forceDelete();

                Storage::disk('s3')->delete($file);
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function documentAnnexeUpdate(Request $request)
    {
        try{
            DB::beginTransaction();

            $document = Document::findOrFail($request->document_id);
            $document->annexes = 1;
            $document->save();

            $annexes    = json_decode($request->annexes, true);
            $imageAnnexes     = $request->images;

            for ($i=0; $i<count($annexes['annexes']); $i++)
            {
                $annexeDocuments = Annexe::findOrFail($annexes['annexes'][$i]['id']);
                $annexeDocuments->annexe = $annexes['annexes'][$i]['annexe'];
                if (isset($imageAnnexes[$i])) {
                    $annexeDocuments->file = Storage::disk('s3')->put('files', $imageAnnexes[$i]);
                }
                $annexeDocuments->save();
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function deleteAnnexe(Request $request)
    {
        try{
            DB::beginTransaction();

            $file = Annexe::where('id', $request->annexe_id)->value('file');
            Annexe::findOrFail($request->annexe_id)->forceDelete();
            Storage::disk('s3')->delete($file);

            if (Annexe::where('document_id', $request->document_id)->count() == 0) {
                $document = Document::findOrFail($request->document_id);
                $document->annexes   = 0;
                $document->save();
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        if (Annexe::where('document_id', $request->document_id)->count() == 0) {
            return [
                'annexe' => false
            ];
        }else {
            return [
                'annexe' => true
            ];
        }
    }

    public function documentAnnexeCreate(Request $request)
    {
        try{
            DB::beginTransaction();

            $document = Document::findOrFail($request->document_id);
            $document->annexes = 1;
            $document->save();

            $annexes    = json_decode($request->annexes, true);
            $imageAnnexes     = $request->images;

            for ($i=0; $i<count($annexes['annexes']); $i++)
            {
                $annexeDocuments = new Annexe();
                $annexeDocuments->document_id = $request->document_id;
                $annexeDocuments->annexe = $annexes['annexes'][$i]['annexe'];
                if (isset($imageAnnexes[$i])) {
                    $annexeDocuments->file = Storage::disk('s3')->put('files', $imageAnnexes[$i]);
                }
                $annexeDocuments->save();
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }
}
