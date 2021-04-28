<?php

namespace App\Http\Controllers;

use App\ChargeAssignment;
use App\Dependency;
use App\DependencyType;
use App\Entity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DependencyController extends Controller
{
    public function getDependencySearch(Request $request)
    {
        $search = $request->search;

        $dependencies = ChargeAssignment::join('dependencies', 'dependencies.id', '=', 'charge_assignments.dependency_id')
            ->join('persons', 'persons.id', '=', 'charge_assignments.person_id')
            ->join('users', 'users.person_id', '=', 'persons.id')
            ->select('dependencies.id', 'dependencies.description')
            ->where('dependencies.entity_id', $request->id)
            ->where('charge_assignments.charge_state_id', 1)
            ->where(function ($query) use ($search){
                $query->where('users.user', 'LIKE', $search);
            })
            ->get();

        return [
            'dependencies' => $dependencies
        ];
    }

    public function getListDependency($id)
    {
        $bring_dependency = Dependency::where('entity_id', '=', $id)
            ->where('description', '!=', 'USUARIO EXTERNO')
            ->select('id', 'description')
            ->get();

        return ['bring_dependency' => $bring_dependency];
    }

    public function getAuthUser()
    {
        $entity_dependency_change_id = $this->getEntityIdSession();
        $list_entity_id              = $this->getEntityIdSession();

        return [
            'entity_dependency_change_id' => $entity_dependency_change_id,
            'list_entity_id'              => $list_entity_id
        ];
    }

    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function getEntityIdSession()
    {
        return Dependency::where('id', $this->getDependencyId())->value('entity_id');
    }

    public function getDependency()
    {
        $entities = Entity::select('id', 'description')->get();

        $types = DependencyType::select('id', 'description')->get();

        $dependencies = Dependency::select('id', 'description', 'code')->orderBy('code', 'asc')->get();

        return [
            'entities' => $entities,
            'types' => $types,
            'dependencies' => $dependencies
        ];
    }

    public function getCodeDependency($id)
    {
        $codes = Dependency::select('code')->where('id', '=', $id)->get();

        foreach ($codes as  $code)
        {
             return $code->code;
        }
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

    public function getIdEntity($id)
    {
        return Dependency::join('entities', 'entities.id', '=', 'dependencies.entity_id')
            ->where('dependencies.entity_id', $id)
            ->value('entities.id');
    }

    private function getRole()
    {
        return ChargeAssignment::charge();
    }

    public function dependency($search, $entity_id, $dependency_type_id)
    {
        if ($this->getRole() == 1) {
            return ChargeAssignment::join('dependencies', 'dependencies.id', '=', 'charge_assignments.dependency_id')
                ->join('persons', 'persons.id', '=', 'charge_assignments.person_id')
                ->select(
                    'dependencies.id',
                    'dependencies.description',
                    'dependencies.code',
                    'dependencies.abbreviation',
                    'dependencies.dependency',
                    'dependencies.entity_id',
                    'dependencies.dependency_type_id',
                    'dependencies.parent',
                    'dependencies.start_procedure',
                    DB::raw('CONCAT(firstName, " ", lastName) AS fullName')
                )
                ->where('dependencies.entity_id', $entity_id)
                ->where('dependencies.dependency_type_id', $dependency_type_id)
                ->where('dependencies.description', 'like', '%'.$search.'%')
                ->orderBy('dependencies.code', 'asc')
                ->paginate(40);
        }else if ($this->getRole() == 2) {
            return ChargeAssignment::join('dependencies', 'dependencies.id', '=', 'charge_assignments.dependency_id')
                ->join('persons', 'persons.id', '=', 'charge_assignments.person_id')
                ->select(
                    'dependencies.id',
                    'dependencies.description',
                    'dependencies.code',
                    'dependencies.abbreviation',
                    'dependencies.dependency',
                    'dependencies.entity_id',
                    'dependencies.dependency_type_id',
                    'dependencies.parent',
                    'dependencies.start_procedure',
                    DB::raw('CONCAT(persons.firstName, " ", persons.lastName) AS fullName')
                )
                ->where('dependencies.entity_id', $entity_id)
                ->where('dependencies.dependency_type_id', $dependency_type_id)
                ->whereNull('dependencies.deleted_at')
                ->whereNull('charge_assignments.deleted_at')
                ->where('charge_assignments.charge_state_id', 1)
                ->where('dependencies.id', '!=', 1)
                ->where('dependencies.description', 'like', '%'.$search.'%')
                ->orderBy('dependencies.code', 'asc')
                ->paginate(40);
        }
    }

    public function listDependency(Request $request)
    {
        $search             = $request->search;
        $entity_id          = $request->entity_id;
        $dependency_type_id = $request->dependency_type_id;

        if (($entity_id != 0) && ($dependency_type_id != 0) && ($search != '')) {

            $list_dependency = $this->dependency( $search, $entity_id, $dependency_type_id);
            return [
                'pagination' => [
                    'total'        => $list_dependency->total(),
                    'current_page' => $list_dependency->currentPage(),
                    'per_page'     => $list_dependency->perPage(),
                    'last_page'    => $list_dependency->lastPage(),
                    'from'         => $list_dependency->firstItem(),
                    'to'           => $list_dependency->lastItem(),
                ],
                'list_dependency' => $list_dependency
            ];

        }else if (($entity_id != 0) && ($dependency_type_id != 0) && ($search == '')) {
            $list_dependency = ChargeAssignment::join('dependencies', 'dependencies.id', '=', 'charge_assignments.dependency_id')
                ->join('persons', 'persons.id', '=', 'charge_assignments.person_id')
                ->select(
                    'dependencies.id',
                    'dependencies.description',
                    'dependencies.code',
                    'dependencies.abbreviation',
                    'dependencies.dependency',
                    'dependencies.entity_id',
                    'dependencies.dependency_type_id',
                    'dependencies.parent',
                    'dependencies.start_procedure',
                    DB::raw('CONCAT(persons.firstName, " ", persons.lastName) AS fullName')
                )
                ->where('dependencies.entity_id', $entity_id)
                ->where('dependencies.dependency_type_id', $dependency_type_id)
                ->whereNull('dependencies.deleted_at')
                ->whereNull('charge_assignments.deleted_at')
                ->where('charge_assignments.charge_state_id', 1)
                ->where('dependencies.id', '!=', 1)
                ->orderBy('dependencies.code', 'asc')
                ->paginate(40);

            return [
                'pagination' => [
                    'total'        => $list_dependency->total(),
                    'current_page' => $list_dependency->currentPage(),
                    'per_page'     => $list_dependency->perPage(),
                    'last_page'    => $list_dependency->lastPage(),
                    'from'         => $list_dependency->firstItem(),
                    'to'           => $list_dependency->lastItem(),
                ],
                'list_dependency' => $list_dependency
            ];
        }
    }

    public function getListDependencyChange()
    {
        $list_entity_change = Dependency::select('id', 'description')
            ->orderBy('code', 'asc')
            ->get();

        return ['list_dependency_change' => $list_entity_change];
    }

    public function getListEntityDependencyChange()
    {
        if ($this->getRole() == 1) {

            $list_entity_dependency_change = Entity::select('id', 'description')
                ->whereNull('entities.deleted_at')
                ->orderBy('description', 'asc')
                ->get();

            return ['list_entity_dependency_change' => $list_entity_dependency_change];

        }else if($this->getRole() == 2) {

            $list_entity_dependency_change = Entity::select('id', 'description')
                ->whereNull('entities.deleted_at')
                ->where('entities.id', '!=', 1)
                ->orderBy('description', 'asc')
                ->get();

            return ['list_entity_dependency_change' => $list_entity_dependency_change];
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

    public function store(Request $request)
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

        $dependency = Dependency::where('id',$dependencies->id)->select('id', 'description')->first();

        return [
            'id'  => $dependency->id,
            'description'  => $dependency->description
        ];
    }

    public function getRelationDependencyChildren($id)
    {
        return Dependency::with('allChildrenDependencies')->where('code', $this->getCodeDependency($id))->get();
    }

    public function getRelationDependencyChildrenId($id, $entity_id)
    {
        return Dependency::select('id')
            ->where('code', 'like', $this->getCodeDependency($id).'%')
            ->where('entity_id', $entity_id)
            ->get();
    }

    public function getIdDependencyChild($id)
    {
        $child = Dependency::join('dependencies as child', 'dependencies.code', '=', 'child.dependency')
            ->select('child.id')
            ->where('dependencies.id', '=', $id);

        return  Dependency::select('id')
        ->union($child)
        ->where('dependencies.id', $id)
        ->get();
    }

    public function getDependencyUpdate($id)
    {
        return Dependency::with('allChildrenDependencies')->where('dependency',$this->getCodeDependency($id))->get();
    }

    public function getIdDependency($id)
    {
        return Dependency::select('id')->where('dependency', 'like', $this->getCodeDependency($id).'%')->get();
    }

    public function listDependencies($collection, $quantity, $code, $dependency_change_id)
    {
        $dependency = new Dependency();
        if ($quantity<10)
        {
            $dependency->code = $code.".0".$quantity;
        }else {
            $dependency->code = $code.".".$quantity;
        }
        $dependency->description        = $collection->description;
        $dependency->abbreviation       = $collection->abbreviation;
        $dependency->dependency         = $code;
        $dependency->parent             = $dependency_change_id;
        $dependency->start_procedure    = $collection->start_procedure;
        $dependency->created_by         = $this->getPersonId();
        $dependency->entity_id          = $collection->entity_id ;
        $dependency->dependency_type_id = $collection->dependency_type_id;
        $dependency->save();

        $code = $this->getCodeDependency($dependency->id);
        foreach ($collection->allChildrenDependencies as $item)
        {
            $quantity = $this->getCountDependency($code, $collection->entity_id);
            $this->listDependencies($item, $quantity, $code, $dependency_change_id );
        }
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    public function update(Request $request)
    {
        switch ($request)
        {
            case($request->child_id == $request->dependency_change_id):

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
                    ]
                );

                if ($this->startProcedureDependence($request->start_procedure, $request->entity_id) == 1) {
                    $errors = ['validates' => 'Ya se asignó a quién iniciará procedimiento'];
                    return response()->json($errors, 422);
                }else {
                    $dependencies = Dependency::findOrFail($request->id);
                    $dependencies->description        = $request->description;
                    $dependencies->abbreviation       = $request->abbreviation;
                    $dependencies->parent             = $request->id ;
                    $dependencies->start_procedure    = $request->start_procedure;
                    $dependencies->created_by         = $this->getPersonId();
                    $dependencies->dependency_type_id = $request->dependency_type_id;
                    $dependencies->save();
                }

                break;

            case($request->child_id != $request->dependency_change_id):

                $collectionChildren = $this->getRelationDependencyChildren($request->id);
                $ids1 = $this->getIdDependencyChild($request->id);
                foreach ($ids1 as $ids)
                {
                    $entity = Dependency::findOrFail($ids->id);
                    $entity->abbreviation = 'update_1_'.$ids->id;
                    $entity->dependency = 'update_1';
                    $entity->save();

                    Dependency::findOrFail($ids->id)->delete();
                }

                $collectionEntity = $this->getDependencyUpdate($request->child_id);
                $ids2 = $this->getIdDependency($request->child_id);
                foreach ($ids2 as $ids)
                {
                    $entity = Dependency::findOrFail($ids->id);
                    $entity->abbreviation = 'update_2_'.$ids->id;
                    $entity->dependency = 'update_2';
                    $entity->save();

                    Dependency::findOrFail($ids->id)->delete();
                }

                foreach ($collectionEntity as $collection)
                {
                    $code = $this->getCodeDependency($request->child_id);
                    $quantity = $this->getCountDependency($code, $request->list_entity_id);
                    $this->listDependencies($collection, $quantity, $code, $request->dependency_change_id);
                }

                foreach ($collectionChildren as $collection)
                {
                    $code = $this->getCodeDependency($request->dependency_change_id);
                    $quantity = $this->getCountDependency($code, $request->list_entity_id);
                    $this->listDependencies($collection, $quantity, $code, $request->dependency_change_id);
                }
                break;
        }
    }

    public function trash(Request $request)
    {
        $dependency_ids = $this->getRelationDependencyChildrenId($request->id, $request->entity_id);

        foreach ($dependency_ids as $ids)
        {
            $entity = Dependency::findOrFail($ids->id);
            $entity->abbreviation = 'trashed_'.$ids->id;
            $entity->dependency = 'trashed';
            $entity->save();

            Dependency::findOrFail($ids->id)->delete();
        }
    }

    public function trashed(Request $request)
    {
        $search = $request->search;

        $trashed = Dependency::select(
            'id',
            'description',
            'code',
            'dependency',
            'dependency_type_id'
        )
            ->where('description', 'like', '%'.$search.'%')
            ->orderBy('code', 'asc')
            ->onlyTrashed()
            ->get()
            ->toArray();

        $page = $request->page;
        $paginate = 10;
        $slice = array_slice($trashed, $paginate * ($page - 1), $paginate);
        $data = new LengthAwarePaginator($slice, count($trashed), $paginate);
        return [
            'pagination' => [
                'total'        => $data->total(),
                'current_page' => $data->currentPage(),
                'per_page'     => $data->perPage(),
                'last_page'    => $data->lastPage(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
            ],
            'listTrashed' => $data
        ];
    }

    public function destroy($id)
    {
        $entity = Dependency::onlyTrashed()->where('id', $id)->firstOrFail();

        $entity->forceDelete();
    }

}
