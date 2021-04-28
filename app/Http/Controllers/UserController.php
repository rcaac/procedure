<?php

namespace App\Http\Controllers;

use App\ChargeAssignment;
use App\ChargeState;
use App\Dependency;
use App\DetailChargeAssignment;
use App\Entity;
use App\IdentificationDocument;
use Illuminate\Http\Request;
use App\User;
use App\Person;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        if ($search==''){
            if ($this->getRole() == 1) {
                $persons = Person::join('users', 'users.person_id', 'persons.id')
                    ->select(
                        'persons.id',
                        'persons.firstName',
                        'persons.lastName',
                        'persons.dni',
                        'persons.phone',
                        'persons.email',
                        'persons.direction',
                        'persons.identification_document_id',
                        'users.id as user_id'
                    )
                    ->orderBy('id', 'desc')
                    ->whereNull('persons.deleted_at')
                    ->paginate(10);
            }else {
                $persons = Person::join('users', 'users.person_id', 'persons.id')
                    ->select(
                        'persons.id',
                        'persons.firstName',
                        'persons.lastName',
                        'persons.dni',
                        'persons.phone',
                        'persons.email',
                        'persons.direction',
                        'persons.identification_document_id',
                        'users.id as user_id'
                    )
                    ->orderBy('id', 'desc')
                    ->where('persons.id', '!=', 1)
                    ->whereNull('persons.deleted_at')
                    ->paginate(10);
            }
        }
        else{
            if ($this->getRole() == 1) {
                $persons = Person::join('users', 'users.person_id', 'persons.id')
                    ->select(
                        'persons.id',
                        'persons.firstName',
                        'persons.lastName',
                        'persons.dni',
                        'persons.phone',
                        'persons.email',
                        'persons.direction',
                        'persons.identification_document_id',
                        'users.id as user_id'
                    )
                    ->whereNull('persons.deleted_at')
                    ->where('persons.firstName', 'like', '%'. $search . '%')
                    ->orWhere('persons.lastName', 'like', '%'. $search . '%')
                    ->orWhere('persons.dni', 'like', '%'. $search . '%')
                    ->orWhere('persons.phone', 'like', '%'. $search . '%')
                    ->orWhere('persons.email', 'like', '%'. $search . '%')
                    ->orderBy('persons.id', 'desc')
                    ->paginate(10);
            }else {
                $persons = Person::join('users', 'users.person_id', 'persons.id')
                    ->select(
                        'persons.id',
                        'persons.firstName',
                        'persons.lastName',
                        'persons.dni',
                        'persons.phone',
                        'persons.email',
                        'persons.direction',
                        'persons.identification_document_id',
                        'users.id as user_id'
                    )
                    ->where('persons.id', '!=', 1)
                    ->whereNull('persons.deleted_at')
                    ->where('persons.firstName', 'like', '%'. $search . '%')
                    ->orWhere('persons.lastName', 'like', '%'. $search . '%')
                    ->orWhere('persons.dni', 'like', '%'. $search . '%')
                    ->orWhere('persons.phone', 'like', '%'. $search . '%')
                    ->orWhere('persons.email', 'like', '%'. $search . '%')
                    ->orderBy('persons.id', 'desc')
                    ->paginate(10);
            }
        }

        if ($this->getRole() == 1) {

            $dependencies = DB::table('dependencies')->select('id', 'description')->get();

        }else if ($this->getRole() == 2) {

            $entity_id = Dependency::where('id', auth()->user()->person->chargeAssignments->first()->dependency_id)->value('entity_id');

            $dependencies = DB::table('dependencies')->select('id', 'description')->get();
        }

        if ($this->getRole() == 1) {

            $entities     = Entity::select('id', 'description')->get();

        }else if ($this->getRole() == 2) {

            $entities     = Entity::select('id', 'description')
                ->where('id', '!=', 1)
                ->where('id', '!=', $entity_id)
                ->get();
        }

        if ($this->getRole() == 1) {

            $dependency_internal = DB::table('dependencies')->select('id', 'description')
                ->get();

            $dependency_citizen = DB::table('dependencies')->select('id', 'description')
                ->where('description', '=', 'USUARIO EXTERNO')
                ->get();

        }else if ($this->getRole() == 2) {

            $dependency_internal = DB::table('dependencies')->select('id', 'description')
                ->where('id', '!=', 1)
                ->where('entity_id', $entity_id)
                ->where('description', '!=', 'USUARIO EXTERNO')
                ->get();

            $dependency_citizen = DB::table('dependencies')->select('id', 'description')
                ->where('description', '=', 'USUARIO EXTERNO')
                ->get();
        }else {

            $dependency_internal = DB::table('dependencies')->select('id', 'description')
                ->where('id', '!=', 1)
                ->where('description', '!=', 'USUARIO EXTERNO')
                ->where('entity_id', $entity_id)
                ->get();

            $dependency_citizen = DB::table('dependencies')->select('id', 'description')
                ->where('description', '=', 'USUARIO EXTERNO')
                ->where('entity_id', $entity_id)
                ->get();
        }

        $documents    = IdentificationDocument::all();

        $state = ChargeState::all();

        return [
            'pagination' => [
                'total'        => $persons->total(),
                'current_page' => $persons->currentPage(),
                'per_page'     => $persons->perPage(),
                'last_page'    => $persons->lastPage(),
                'from'         => $persons->firstItem(),
                'to'           => $persons->lastItem(),
            ],
            'persons'             => $persons,
            'entities'            => $entities,
            'dependency_internal' => $dependency_internal,
            'dependency_citizen'  => $dependency_citizen,
            'dependencies'        => $dependencies,
            'documents'           => $documents,
            'state'               => $state
        ];
    }

    private function getRole()
    {
        return ChargeAssignment::charge();
    }

    private function getDependencyId()
    {
        return auth()->user()->person->chargeAssignments->first()->dependency_id;
    }

    public function getAuthUser()
    {
        return auth()->user()->person->toJson();
    }

    public function getIdentificationDocument()
    {
        $identifications = IdentificationDocument::select('id', 'document')->get();

        $dependency_entity_id = Dependency::where('id', $this->getDependencyId())->value('entity_id');
        $dependency_external_id = Dependency::where('entity_id', $dependency_entity_id)
            ->where('description', 'USUARIO EXTERNO')
            ->value('id');


        return [
            'identifications' => $identifications,
            'dependency_id'   => $dependency_external_id
        ];
    }

    private function validateAuthUpdate($request)
    {
        return request()->validate([
            'firstName'                   => 'required',
            'lastName'                    => 'required',
            'dni'                         => 'required|numeric|unique:persons,dni,'.$request->id.',id',
            'phone'                       => 'required|numeric',
            'email'                       => 'required|email|unique:persons,email,'.$request->id.',id',
            'identification_document_id'  => 'required|not_in:0',
        ], [
            'firstName.required'                => 'El nombre es obligatorio',
            'lastName.required'                 => 'El apellido es obligatorio',
            'dni.required'                      => 'El dni es obligatorio',
            'dni.numeric'                       => 'El dni debe de contener caracteres numéricos',
            'dni.unique'                        => 'El dni ya existe',
            'phone.required'                    => 'El teléfono es obligatorio',
            'phone.numeric'                     => 'El Teléfono debe de contener caracteres numéricos',
            'email.required'                    => 'El correo electrónico es obligatorio',
            'email.email'                       => 'Debe ser un correo electrónico válido',
            'email.unique'                      => 'El correo electrónico ya existe',
            'identification_document_id.not_in' => 'Elige un tipo',

        ]);
    }

    public function userAuthUpdate(Request $request)
    {
        $this->validateAuthUpdate($request);

        try{
            DB::beginTransaction();

            $person = Person::findOrFail($request->id);
            $person->firstName                  = $request->firstName;
            $person->lastName                   = $request->lastName;
            $person->dni                        = $request->dni;
            $person->phone                      = $request->phone;
            $person->email                      = $request->email;
            $person->direction                  = $request->direction;
            $person->identification_document_id = $request->identification_document_id;
            $person->created_by                 = auth()->user()->id;
            $person->save();

            $user = User::findOrFail(auth()->user()->id);
            $user->user = $request->dni;
            if ($request->password != null) {
                $user->password = bcrypt($request->password);
            } else {
                unset($request->password);
            };
            $user->person_id = $person->id;
            $user->created_by = auth()->user()->id;
            $user->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

    }

    public  function validateUser()
    {
        return request()->validate([
            'firstName'                   => 'required',
            'lastName'                    => 'required',
            'dni'                         => 'required|numeric|digits:8|unique:persons,dni,' .auth()->user()->person->id,
            'identification_document_id'  => 'required|integer|not_in:0',
        ], [
            'firstName.required'                => 'El nombre es obligatorio',
            'lastName.required'                 => 'El apellido es obligatorio',
            'dni.required'                      => 'El dni es obligatorio',
            'dni.numeric'                       => 'El dni debe de contener caracteres numéricos',
            'dni.unique'                        => 'El dni ya existe',
            'dni.digits'                        => 'El dni debe de contener 08 dígitos',
            'identification_document_id.not_in' => 'Elige un tipo',
        ]);
    }

    public function store(Request $request)
    {
        $this->validateUser();

        try{
            DB::beginTransaction();

            $person = new Person();
            $person->firstName                  = $request->firstName;
            $person->lastName                   = $request->lastName;
            $person->dni                        = $request->dni;
            $person->phone                      = $request->phone;
            $person->email                      = $request->email;
            $person->direction                  = $request->direction;
            $person->identification_document_id = $request->identification_document_id;
            $person->created_by                 = auth()->user()->id;
            $person->save();

            $user = new User();
            $user->user = $request->dni;
            $user->password = bcrypt($request->dni);
            $user->person_id  = $person->id;
            $user->created_by = auth()->user()->id;
            $user->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    public function externalStore(Request $request)
    {
        $this->validateUser();

        try{
            DB::beginTransaction();

            $person = new Person();
            $person->firstName                  = $request->firstName;
            $person->lastName                   = $request->lastName;
            $person->dni                        = $request->dni;
            $person->phone                      = $request->phone;
            $person->email                      = $request->email;
            $person->direction                  = $request->direction;
            $person->identification_document_id = $request->identification_document_id;
            $person->created_by                 = $this->getPersonId();
            $person->save();

            $user = new User();
            $user->user = $request->dni;
            $user->password = bcrypt($request->dni);
            $user->person_id  = $person->id;
            $user->created_by = $this->getPersonId();
            $user->save();

            $charge = new ChargeAssignment();
            $charge->charge          = 'Ciudadano';
            $charge->type            = 2;
            $charge->person_id       = $person->id;
            $charge->role_id         = 4;
            $charge->dependency_id   = $request->dependency_citizen_id;
            $charge->charge_state_id = 1;
            $charge->created_by      = $this->getPersonId();
            $charge->save();

            DB::commit();

            $dni = Person::where('id', $person->id)->value('dni');

            return ['dni' => $dni];

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    private function getEntityId($entity_id)
    {
        return Dependency::where('entity_id', $entity_id)->value('entity_id');
    }

    private function startProcedureDependence($start_procedure_id, $entity_id)
    {
        return Dependency::where('start_procedure', $start_procedure_id)->where('entity_id', $entity_id)->value('start_procedure');
    }

    public function getCountDependency($code, $entity_id)
    {
        $counts = DB::table('dependencies')
            ->select(DB::raw('COUNT(dependency) as quantity'))
            ->where('dependency', '=', $code)
            ->where('entity_id', '=', $entity_id)
            ->get();

        foreach ($counts as  $count)
        {
            return $count->quantity+1;
        }
    }

    public function getCodeDependency($id)
    {
        $codes = Dependency::select('code')->where('id', '=', $id)->get();

        foreach ($codes as  $code)
        {
            return $code->code;
        }
    }

    public function personDependencyCharge(Request $request)
    {
        $this->validate($request, [
            'description'        => 'required',
            'abbreviation'       => 'required',
            'dependency_type_id' => 'required|integer|not_in:0',
            'list_entity_id'     => 'required|integer|not_in:0'
        ],
        [
            'description.required'      => 'Ingrese la unidad orgánica',
            'abbreviation.required'     => 'Debe ingresar una abreviatura',
            'dependency_type_id.not_in' => 'Debe de elegir un tipo de dependencia',
            'list_entity_id.not_in'     => 'Debe de elegir una entidad',
        ]);

        if ($request->person_id == 0){

            $this->validateUser();
        }

        try{
            DB::beginTransaction();

            if (($request->dependency_id == 0) || ($this->getEntityId($request->entity_id) == null))
            {
                if ($this->startProcedureDependence($request->start_procedure, $request->entity_id) == 1) {
                    $errors = ['validates' => 'Ya se asignó a quién iniciará procedimiento'];
                    return response()->json($errors, 422);
                }else {
                    $dependency = '00';
                    $quantity = $this->getCountDependency($dependency, $request->entity_id);

                    $dependencies = new Dependency();
                    $dependencies->code               = '0'.$quantity;
                    $dependencies->description        = $request->description;
                    $dependencies->abbreviation       = $request->abbreviation;
                    $dependencies->dependency         = '00';
                    $dependencies->parent             = 0;
                    $dependencies->start_procedure    = $request->start_procedure;
                    $dependencies->created_by         = $this->getPersonId();
                    $dependencies->entity_id          = $request->entity_id;
                    $dependencies->dependency_type_id = $request->dependency_type_id;
                    $dependencies->save();

                    $dependency_update = Dependency::findOrFail($dependencies->id);
                    $dependency_update->parent = $dependencies->id;
                    $dependency_update->save();
                }

            }else {

                if ($this->startProcedureDependence($request->start_procedure, $request->entity_id) == 1) {
                    $errors = ['validates' => 'Ya se asignó a quién iniciará procedimiento'];
                    return response()->json($errors, 422);
                }else {
                    $dependency = $this->getCodeDependency($request->dependency_id);
                    $quantity = $this->getCountDependency($dependency, $request->entity_id);

                    $dependencies = new Dependency();
                    if ($quantity<10)
                    {
                        $dependencies->code = $dependency.".0".$quantity;
                    }else {
                        $dependencies->code = $dependency.".".$quantity;
                    }
                    $dependencies->description        = $request->description;
                    $dependencies->abbreviation       = $request->abbreviation;
                    $dependencies->dependency         = $dependency;
                    $dependencies->parent             = $request->dependency_id;
                    $dependencies->start_procedure    = $request->start_procedure ;
                    $dependencies->created_by         = $this->getPersonId();
                    $dependencies->entity_id          = $request->entity_id;
                    $dependencies->dependency_type_id = $request->dependency_type_id;
                    $dependencies->save();
                }
            }

            if ($request->person_id == 0){

                $person = new Person();
                $person->firstName                  = $request->firstName;
                $person->lastName                   = $request->lastName;
                $person->dni                        = $request->dni;
                $person->phone                      = $request->phone;
                $person->email                      = $request->email;
                $person->direction                  = $request->direction;
                $person->identification_document_id = $request->identification_document_id;
                $person->created_by                 = $this->getPersonId();
                $person->save();

                $user = new User();
                $user->user = $request->dni;
                $user->password = bcrypt($request->dni);
                $user->person_id  = $person->id;
                $user->created_by = $this->getPersonId();
                $user->save();
            }

            $charge = new ChargeAssignment();
            $charge->charge          = 'Entidad Externa';
            $charge->type            = 3;
            if ($request->person_id == 0) {
                $charge->person_id   = $person->id;
            }else {
                $charge->person_id   = $request->person_id;
            }
            $charge->role_id         = 4;
            $charge->dependency_id   = $dependencies->id;
            $charge->charge_state_id = 1;
            $charge->created_by      = $this->getPersonId();
            $charge->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        $dependency = Dependency::where('id',$dependencies->id)->select('id', 'description')->first();

        return [
            'id'  => $dependency->id,
            'description'  => $dependency->description
        ];
    }

    public  function validateUserUpdate($request)
    {
        return $this->validate($request, [
            'firstName'                   => 'required',
            'lastName'                    => 'required',
            'dni'                         => 'required|numeric|unique:persons,dni,'.$request->person_id.',id',
            //'phone'                       => 'required|numeric',
            //'email'                       => 'required|email|unique:persons,email,'.$request->person_id.',id',
            'identification_document_id'  => 'required|integer|not_in:0',
            //'direction'                   => 'required',
        ], [
            'firstName.required'                => 'El nombre es obligatorio',
            'lastName.required'                 => 'El apellido es obligatorio',
            'dni.required'                      => 'El dni es obligatorio',
            'dni.numeric'                       => 'El dni debe de contener caracteres numéricos',
            'dni.unique'                        => 'El dni ya existe',
            //'phone.required'                    => 'El teléfono es obligatorio',
            //'phone.numeric'                     => 'El Teléfono debe de contener caracteres numéricos',
            //'email.required'                    => 'El correo electrónico es obligatorio',
            //'email.email'                       => 'Debe ser un correo electrónico válido',
            //'email.unique'                      => 'El correo electrónico ya existe',
            'identification_document_id.not_in' => 'Elige un tipo',
            //'direction.required'                => 'Ingresa una dirección',
        ]);
    }

    public function update(Request $request)
    {
        $this->validateUserUpdate($request);

        try{
            DB::beginTransaction();

            $person = Person::findOrFail($request->person_id);
            $person->firstName                  = $request->firstName;
            $person->lastName                   = $request->lastName;
            $person->dni                        = $request->dni;
            $person->phone                      = $request->phone;
            $person->email                      = $request->email;
            $person->direction                  = $request->direction;
            $person->identification_document_id = $request->identification_document_id;
            $person->created_by                 = $this->getPersonId();
            $person->save();

            $user = User::findOrFail($request->user_id);
            $user->user = $request->dni;
            if($request->password != null){
                $user->password = bcrypt( $request->password);
            } else {
                unset($request->password );
            };
            $user->person_id  = $person->id;
            $user->created_by = $this->getPersonId();
            $user->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function listChargeAssignment($id)
    {
        $charge_assignment = ChargeAssignment::leftJoin('detail_charge_assignments', 'detail_charge_assignments.charge_assignment_id', 'charge_assignments.id')
            ->join('charge_state', 'charge_state.id', 'charge_assignments.charge_state_id')
            ->join('dependencies', 'dependencies.id', 'charge_assignments.dependency_id')
            ->join('entities', 'entities.id', 'dependencies.entity_id')
            ->join('roles', 'roles.id', 'charge_assignments.role_id')
            ->select(
                'charge_assignments.id as charge_id',
                'charge_assignments.role_id',
                'charge_assignments.charge',
                'charge_assignments.type',
                'detail_charge_assignments.detail',
                'detail_charge_assignments.startDate',
                'detail_charge_assignments.finalDate',
                'detail_charge_assignments.id as detail_id',
                'charge_state.charge as state',
                'charge_state.id as charge_state_id',
                'entities.description AS entity',
                'dependencies.description AS dependency',
                'dependencies.id as dependency_id',
                'roles.description as role'
            )
            ->where('charge_assignments.person_id', $id)
            ->whereNull('detail_charge_assignments.deleted_at')
            ->get();

        return ['charge_assignment' => $charge_assignment];

    }

    public  function validateChargeInternal()
    {
        return request()->validate([
            'role_internal_id'            => 'required|not_in:0',
            'charge_state_id'             => 'required|not_in:0',
            'charge'                      => 'required',
            'dependency_internal_id'      => 'required|not_in:0',
            'startDate'                   => 'required',
            'finalDate'                   => 'required',
            'detail'                      => 'required'
        ], [
            'role_internal_id.not_in'           => 'Elige un Rol',
            'charge_state_id.not_in'            => 'Elige un estado',
            'charge.required'                   => 'El cargo es obligatorio',
            'dependency_internal_id.not_in'     => 'Elige una dependencia',
            'startDate.required'                => 'La fecha de inicio es obligatoria',
            'finalDate.required'                => 'La fecha final es obligatoria',
            'detail.required'                   => 'Debe ingresar el documento sustentatorio'
        ]);
    }

    public  function validateChargeCitizen()
    {
        return request()->validate([
            'role_citizen_id'       => 'required|not_in:0',
            'charge_state_id'       => 'required|not_in:0',
            'dependency_citizen_id' => 'required|not_in:0',
        ], [
            'role_citizen_id.not_in'       => 'Elige un Rol',
            'charge_state_id.not_in'       => 'Elige un estado',
            'dependency_citizen_id.not_in' => 'Elige una dependencia'
        ]);
    }

    public  function validateChargeExternal()
    {
        return request()->validate([
            'role_external_id'       => 'required|not_in:0',
            'charge_state_id'        => 'required|not_in:0',
            'change_entity_id'       => 'required|not_in:0',
            'dependency_external_id' => 'required|not_in:0',
        ], [
            'role_external_id.not_in'       => 'Elige un Rol',
            'charge_state_id.not_in'        => 'Elige un estado',
            'dependency_external_id.not_in' => 'Elige una dependencia',
            'change_entity_id.not_in'       => 'Elige una entidad'
        ]);
    }

    private function validateChargeAssignment($dependency_id) {

        return ChargeAssignment::where('dependency_id', $dependency_id)->where('charge_state_id', 1)->value('charge_state_id');
    }

    private function validateChargeCitizenAssignment($person_id)
    {
        return ChargeAssignment::where('person_id', $person_id)->where('role_id', 4)->whereNull('deleted_at')->value('charge_state_id');
    }

    private function validateChargeExternalAssignment($dependency_id)
    {
        return ChargeAssignment::where('dependency_id', $dependency_id)->whereNull('deleted_at')->value('charge_state_id');
    }

    public function registerCharge(Request $request)
    {
        if(($request->role_internal_id == 2 || $request->role_internal_id == 3) && $request->type == 1)
        {
            $this->validateChargeInternal();

            if ($this->validateChargeAssignment($request->dependency_internal_id) == 1) {

                $errors = ['validates' => 'Esta dependencia ya ha sido asignada'];
                return response()->json($errors, 422);

            }else {

                $charge = new ChargeAssignment();
                $charge->charge          = $request->charge;
                $charge->type            = $request->type;
                $charge->person_id       = $request->person_id;
                $charge->role_id         = $request->role_internal_id;
                $charge->dependency_id   = $request->dependency_internal_id;
                $charge->charge_state_id = $request->charge_state_id;
                $charge->created_by      = $this->getPersonId();
                $charge->save();

                $assignment = new DetailChargeAssignment();
                $assignment->startDate            = $request->startDate;
                $assignment->finalDate            = $request->finalDate;
                $assignment->detail               = $request->detail;
                $assignment->charge_assignment_id = $charge->id;
                $assignment->save();
            }

        }else if($request->role_citizen_id == 4 && $request->type == 2) {

            $this->validateChargeCitizen();

            if ($this->validateChargeCitizenAssignment($request->person_id) == 1) {

                $errors = ['validates' => 'Esta persona, ya cuenta con un rol de usuario externo'];
                return response()->json($errors, 422);

            }else {

                $charge = new ChargeAssignment();
                $charge->charge          = 'Ciudadano';
                $charge->type            = $request->type;
                $charge->person_id       = $request->person_id;
                $charge->role_id         = $request->role_citizen_id;
                $charge->dependency_id   = $request->dependency_citizen_id;
                $charge->charge_state_id = $request->charge_state_id;
                $charge->created_by      = $this->getPersonId();
                $charge->save();
            }

        }else if ($request->role_external_id == 4 && $request->type == 3) {

            $this->validateChargeExternal();

            if ($this->validateChargeExternalAssignment($request->dependency_external_id) == 1) {

                $errors = ['validates' => 'Esta dependencia ya ha sido asignada'];
                return response()->json($errors, 422);

            } else {

                $charge = new ChargeAssignment();
                $charge->charge = 'Entidad Externa';
                $charge->type = $request->type;
                $charge->person_id = $request->person_id;
                $charge->role_id = $request->role_external_id;
                $charge->dependency_id = $request->dependency_external_id;
                $charge->charge_state_id = $request->charge_state_id;
                $charge->created_by = $this->getPersonId();
                $charge->save();

            }
        }
    }

    public function updateCharge(Request $request)
    {
        if(($request->role_id == 2 || $request->role_id == 3) && $request->type == 1) {
            $charge = ChargeAssignment::findOrFail($request->charge_id);
            $charge->charge          = $request->charge;
            $charge->type            = $request->type;
            $charge->person_id       = $request->person_id;
            $charge->role_id         = $request->role_id;
            $charge->dependency_id   = $request->dependency_id;
            $charge->charge_state_id = $request->charge_state_id;
            $charge->created_by      = $this->getPersonId();
            $charge->save();

            if ($request->detail_id != null) {
                $assignment = DetailChargeAssignment::findOrFail($request->detail_id);
                $assignment->startDate            = $request->startDate;
                $assignment->finalDate            = $request->finalDate;
                $assignment->detail               = $request->detail;
                $assignment->charge_assignment_id = $charge->id;
                $assignment->save();
            }

        }else if ($request->role_id == 4 && $request->type == 2) {
            $charge = ChargeAssignment::findOrFail($request->charge_id);
            $charge->charge          = 'Ciudadano';
            $charge->type            = $request->type;
            $charge->person_id       = $request->person_id;
            $charge->role_id         = $request->role_id;
            $charge->dependency_id   = $request->dependency_id;
            $charge->charge_state_id = $request->charge_state_id;
            $charge->created_by      = $this->getPersonId();
            $charge->save();

        }else if ($request->role_id == 4 && $request->type == 3){
            $charge = ChargeAssignment::findOrFail($request->charge_id);
            $charge->charge          = 'Entidad Externa';
            $charge->type            = $request->type;
            $charge->person_id       = $request->person_id;
            $charge->role_id         = $request->role_id;
            $charge->dependency_id   = $request->dependency_id;
            $charge->charge_state_id = $request->charge_state_id;
            $charge->created_by      = $this->getPersonId();
            $charge->save();

            return $charge;
        }
    }

    public function trash($id)
    {
        $person = Person::findOrFail($id);
        $person->delete();

        $user = User::where('person_id', $id);
        $user->delete();

        $charge_assignment = DB::table('charge_assignments')->where('person_id', $id)->get();
        foreach ($charge_assignment as $c)
        {
            $detail_charge_assignment = DB::table('detail_charge_assignments')->where('charge_assignment_id', $c->id)->get();
            foreach ($detail_charge_assignment as $detail)
            {
                DetailChargeAssignment::findOrFail($detail->id)->delete();
            }
            ChargeAssignment::findOrFail($c->id)->delete();
        }

    }

    public function chargeTrash($id)
    {
        $detail_charge_assignment = DB::table('detail_charge_assignments')->where('charge_assignment_id', $id)->get();
        foreach ($detail_charge_assignment as $detail)
        {
            DetailChargeAssignment::findOrFail($detail->id)->delete();
        }
        ChargeAssignment::findOrFail($id)->delete();
    }

    public function getListUserTrashed(Request $request)
    {
        $search = $request->search;

        $personTrashed = Person::select(
                'id',
                'firstName',
                'lastName',
                'dni',
                'phone',
                'email',
                'identification_document_id'
            )
            ->where(function ($query) use ($search){
                $query->where('firstName', 'like', '%' . $search . '%')
                    ->orWhere('lastName', 'like', '%' . $search . '%')
                    ->orWhere('dni', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $personTrashed ->total(),
                'current_page' => $personTrashed ->currentPage(),
                'per_page' => $personTrashed ->perPage(),
                'last_page' => $personTrashed ->lastPage(),
                'from' => $personTrashed ->firstItem(),
                'to' => $personTrashed ->lastItem(),
            ],
            'personTrashed' => $personTrashed,
        ];
    }

    public function getListChargeTrashed(Request $request)
    {
        $search = $request->search;

        $charge = ChargeAssignment::leftJoin('detail_charge_assignments', 'charge_assignments.id', 'detail_charge_assignments.charge_assignment_id')
            ->select(
                'charge_assignments.id',
                'charge_assignments.charge',
                'detail_charge_assignments.detail',
                'detail_charge_assignments.startDate',
                'detail_charge_assignments.finalDate'
            )
            ->where(function ($query) use ($search){
                $query->where('charge_assignments.charge', 'like', '%' . $search . '%')
                    ->orWhere('detail_charge_assignments.detail', 'like', '%' . $search . '%')
                    ->orWhere('detail_charge_assignments.startDate', 'like', '%' . $search . '%')
                    ->orWhere('detail_charge_assignments.finalDate', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'desc')
            ->onlyTrashed()
            ->paginate(10);

        return [
            'pagination' => [
                'total' => $charge ->total(),
                'current_page' => $charge ->currentPage(),
                'per_page' => $charge ->perPage(),
                'last_page' => $charge ->lastPage(),
                'from' => $charge ->firstItem(),
                'to' => $charge ->lastItem(),
            ],
            'chargeTrashed' => $charge,
        ];
    }
}
