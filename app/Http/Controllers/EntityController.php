<?php

namespace App\Http\Controllers;

use App\ChargeAssignment;
use App\Entity;
use App\Dependency;
use App\EntityType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EntityController extends Controller
{
    public function getSelectedEntity()
    {
        $entities = Entity::select('id', 'description')
            ->whereNull('deleted_at')
            ->get();

        return [
            'entities' => $entities
        ];
    }

    private function getRole()
    {
        return ChargeAssignment::charge();
    }

    public function getListEntity($id)
    {
        $lists = Entity::where('entity_type_id', '=', $id)
            ->whereNull('deleted_at')
            ->select('id', 'description', 'code')
            ->orderBy('code', 'asc')
            ->get();

        return ['list' => $lists];
    }

    public function listDependencyEntity($search)
    {
        if($this->getRole() == 1) {
            return Entity::select(
                'id',
                'description',
                'code',
                'direction',
                'ruc',
                'abbreviation',
                'dependency',
                'entity_type_id',
                'parent'
            )
            ->whereNull('deleted_at')
            ->where('description', 'like', '%' . $search . '%')
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        }

        if($this->getRole() == 2) {
            return Entity::select(
                'id',
                'description',
                'code',
                'direction',
                'ruc',
                'abbreviation',
                'dependency',
                'entity_type_id',
                'parent'
            )
            ->whereNull('deleted_at')
            ->where('id', '!=', 1)
            ->where('description', 'like', '%' . $search . '%')
            ->orderBy('code', 'asc')
            ->get()
            ->toArray();
        }
    }

    public function getListDependencyEntity(Request $request)
    {
        $search = $request->search;
        $id = $request->id;
        $page = $request->page;

        if (($id == 0) && ($search == '')) {
            $list_dependency_entity = $this->listDependencyEntity($search);
            $paginate = 10;
            $slice = array_slice($list_dependency_entity, $paginate * ($page - 1), $paginate);
            $data = new LengthAwarePaginator($slice, count($list_dependency_entity), $paginate);
            return [
                'pagination' => [
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
                'list_dependency_entity' => $data
            ];

        } else if ( ($id == 0) && ($search != '')){
            $list_dependency_entity = $this->listDependencyEntity($search);
            $paginate = 10;
            $slice = array_slice($list_dependency_entity, $paginate * ($page - 1), $paginate);
            $data = new LengthAwarePaginator($slice, count($list_dependency_entity), $paginate);
            return [
                'pagination' => [
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
                'list_dependency_entity' => $data
            ];
        }else if (($id != 0) && ($search == '')) {

            $list_dependency_entity = Entity::whereNull('deleted_at')
                ->where('id', '!=', 1)
                ->where('parent', $id)
                ->select(
                    'id',
                    'description',
                    'code',
                    'direction',
                    'ruc',
                    'abbreviation',
                    'dependency',
                    'entity_type_id',
                    'parent'
                )
                ->orderBy('code', 'asc')
                ->get()
                ->toArray();

            $paginate = 10;
            $slice = array_slice($list_dependency_entity, $paginate * ($page - 1), $paginate);
            $data = new LengthAwarePaginator($slice, count($list_dependency_entity), $paginate);
            return [
                'pagination' => [
                    'total' => $data->total(),
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'last_page' => $data->lastPage(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
                'list_dependency_entity' => $data
            ];
        }
    }

    private function getDependencyId()
    {
        return Session::get('dependency_id');
    }

    private function getEntityId()
    {
        return Dependency::where('id', $this->getDependencyId())->value('entity_id');
    }

    public function getEntityExternal()
    {
        if ($this->getRole() == 1) {
            $entity_external = Entity::select('id', 'description AS label')->get();

        }else if ($this->getRole() == 2 || $this->getRole() == 3){
            $entity_external = Entity::select('id', 'description AS label')
                ->where('id', '!=', 1)
                ->where('id', '!=', $this->getEntityId())
                ->get();
        }

        return [
            'entity_external'       => $entity_external,
        ];
    }

    public function getListEntityChange()
    {
        if ($this->getRole() == 1) {

            $list_entity_change = Entity::select('entities.id', 'entities.description')
                ->whereNull('entities.deleted_at')
                ->orderBy('entities.code', 'asc')
                ->get();

            return ['list_entity_change' => $list_entity_change];

        }else if ($this->getRole() == 2) {

            $list_entity_change = Entity::select('entities.id', 'entities.description')
                ->whereNull('entities.deleted_at')
                ->where('entities.id', '!=', 1)
                ->orderBy('entities.code', 'asc')
                ->get();

            return ['list_entity_change' => $list_entity_change];

        }else {

            $list_entity_change = Entity::select('entities.id', 'entities.description')
                ->join('dependencies', 'dependencies.entity_id', '=', 'entities.id')
                ->where('dependencies.id', auth()->user()->person->chargeAssignments->first()->dependency_id)
                ->whereNull('entities.deleted_at')
                ->orderBy('entities.code', 'asc')
                ->get();

            return ['list_entity_change' => $list_entity_change];
        }
    }

    public function getEntityType()
    {
        $entity_type = EntityType::select('id', 'type')->get();

        return ['type' => $entity_type];
    }

    public function getCodeEntity($id)
    {
        return Entity::where('id', '=', $id)->value('code');
    }

    public function getEntityUpdate($id)
    {
        return Entity::with('allChildrenEntities')->where('dependency',$this->getCodeEntity($id))->get();
    }

    public function getRelationEntity($id)
    {
        return  Entity::select(
            'id',
            'description',
            'code',
            'direction',
            'ruc',
            'abbreviation',
            'dependency',
            'entity_type_id',
            'entities.id as child_id'
        )
        ->where('entities.id', $id)
        ->get();
    }

    public function getRelationEntityChildren($id)
    {
         return Entity::with('allChildrenEntities')->where('code', $this->getCodeEntity($id))->get();
    }

    public function getIdEntityChild($id)
    {
        $child = Entity::join('entities as child', 'entities.code', '=', 'child.dependency')
            ->select(
                'child.id'
            )
            ->where('entities.id', '=', $id);

        return  Entity::select(
            'id'
        )
        ->union($child)
        ->where('entities.id', $id)
        ->get();
    }

    public function getIdEntity($id)
    {
        return Entity::select('id')->where('dependency', 'like', $this->getCodeEntity($id).'%')->get();
    }

    public function getCountEntity($code)
    {
        $counts = DB::table('entities')
            ->select(DB::raw('COUNT(dependency) as quantity'))
            ->where('dependency', '=', $code)
            ->get();

        foreach ($counts as  $count)
        {
            return $count->quantity+1;
        }
    }

    public function getDependencyEntity($id)
    {
        return  Entity::join('entities as child', 'entities.code', '=', 'child.dependency')
            ->select(
                'child.id',
                'child.description',
                'child.code',
                'child.direction',
                'child.ruc',
                'child.abbreviation',
                'child.dependency',
                'child.entity_type_id',
                'entities.id as child_id'
            )
            ->where('child.dependency', 'like', $this->getCodeEntity($id).'%')->get();
    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description'    => 'required',
            //'direction'      => 'required',
            //'ruc'            => 'required|numeric|unique:entities|digits:11',
            //'abbreviation'   => 'required',
            'entity_type_id' => 'required|integer|not_in:0'
            ],
            [
                'description.required'  => 'La entidad es obligatorio',
                //'direction.required'    => 'La dirección es obligatorio',
                //'ruc.required'          => 'El ruc es obligatorio',
                //'ruc.numeric'           => 'El ruc debe de contener caracteres numéricos',
                //'ruc.digits'            => 'El ruc debe de contener 11 dígitos exactamente',
                //'ruc.unique'            => 'El ruc ya existe',
                //'abbreviation.required' => 'Debe ingresar una abreviatura',
                'entity_type_id.not_in' => 'Debe de elegir un tipo de entidad'
            ]
        );

        if ($request->entity_id == 0)
        {
            $code = '00';
            $quantity = $this->getCountEntity($code);

            $entity = new Entity();
            $entity->code           = '0'.$quantity;
            $entity->description    = $request->description;
            $entity->direction      = $request->direction;
            $entity->ruc            = $request->ruc;
            $entity->abbreviation   = $request->abbreviation;
            $entity->dependency     = '00' ;
            $entity->parent         = 0 ;
            $entity->created_by     = $this->getPersonId();
            $entity->entity_type_id = $request->entity_type_id;
            $entity->save();

            $entity_update = Entity::findOrFail($entity->id);
            $entity_update->parent = $entity->id;
            $entity_update->save();


        }else {
            $code = $this->getCodeEntity($request->entity_id);
            $quantity = $this->getCountEntity($code);

            $entity = new Entity();
            if ($quantity<10)
            {
                $entity->code = $code.".0".$quantity;
            }else {
                $entity->code = $code.".".$quantity;
            }
            $entity->description    = $request->description;
            $entity->direction      = $request->direction;
            $entity->ruc            = $request->ruc;
            $entity->abbreviation   = $request->abbreviation;
            $entity->dependency     = $code ;
            $entity->parent         = $request->entity_id ;
            $entity->created_by     = $this->getPersonId();
            $entity->entity_type_id = $request->entity_type_id;
            $entity->save();
        }

        $entity = Entity::where('id',$entity->id)->select('id', 'description')->first();

        return [
            'id'  => $entity->id,
            'description'  => $entity->description
        ];
    }

    public function listEntities($collection, $quantity, $code, $entity_change_id)
    {
        $entity = new Entity();
        if ($quantity<10)
        {
            $entity->code = $code.".0".$quantity;
        }else {
            $entity->code = $code.".".$quantity;
        }
        $entity->description    = $collection->description;
        $entity->direction      = $collection->direction;
        $entity->ruc            = $collection->ruc;
        $entity->abbreviation   = $collection->abbreviation;
        $entity->dependency     = $code ;
        $entity->parent         = $entity_change_id ;
        $entity->created_by     = $this->getPersonId();
        $entity->entity_type_id = $collection->entity_type_id;
        $entity->save();

        $code = $this->getCodeEntity($entity->id);
        foreach ($collection->allChildrenEntities as $item)
        {
            $quantity = $this->getCountEntity($code);

            $this->listEntities($item, $quantity, $code, $entity_change_id);
        }
    }

    public function updateEntity($request)
    {
        $this->validate($request, [
            'description'    => 'required',
            //'direction'      => 'required',
            //'ruc'            => 'required|numeric',
            //'abbreviation'   => 'required|unique:entities,abbreviation,'.$request->id.',id',
            'entity_type_id' => 'required|integer|not_in:0'
        ],
            [
                'description.required'  => 'La entidad es obligatorio',
                //'direction.required'    => 'La dirección es obligatorio',
                //'ruc.required'          => 'El ruc es obligatorio',
                //'ruc.numeric'           => 'El ruc debe de contener caracteres numéricos',
                //'abbreviation.required' => 'Debe ingresar una abreviatura',
                //'abbreviation.unique'   => 'La abreviatura ya existe',
                'entity_type_id.not_in' => 'Debe de elegir un tipo de entidad'
            ]
        );

        $entity = Entity::findOrFail($request->id);
        $entity->description    = $request->description;
        $entity->direction      = $request->direction;
        $entity->ruc            = $request->ruc;
        $entity->created_by     = $this->getPersonId();
        $entity->abbreviation   = $request->abbreviation;
        $entity->parent         = $request->id ;
        $entity->entity_type_id = $request->entity_type_id;
        $entity->save();
    }

    public function getRelationEntityChildrenId($id)
    {
        return Entity::select('id')->where('code', 'like', $this->getCodeEntity($id).'%')->get();
    }

    public function getRelationDependencyId($id)
    {
        return Dependency::select('id')->where('entity_id', $id)->get();
    }

    public function update(Request $request)
    {
        switch ($request)
        {
            case($request->id == $request->child_id):
                $this->updateEntity($request);
                break;
            case($request->child_id == $request->entity_change_id):
                $this->updateEntity($request);
                break;
            case($request->child_id != $request->entity_change_id):
                $collectionChildren = $this->getRelationEntityChildren($request->id);
                $ids1 = $this->getIdEntityChild($request->id);
                foreach ($ids1 as $ids)
                {
                    $entity = Entity::findOrFail($ids->id);
                    $entity->dependency   = 'update_'.Carbon::now()->format('H:i:s');
                    $entity->ruc          = Carbon::now()->format('H:i:s');
                    $entity->abbreviation = 'trashed_'.Carbon::now()->format('H:i:s');
                    $entity->save();

                    Entity::findOrFail($ids->id)->delete();
                }

                $collectionEntity = $this->getEntityUpdate($request->child_id);
                $ids2 = $this->getIdEntity($request->child_id);
                foreach ($ids2 as $ids)
                {
                    $entity = Entity::findOrFail($ids->id);
                    $entity->dependency = 'update_2';
                    $entity->ruc        = 'update_2';
                    $entity->abbreviation = 'trashed_'.$ids->id;
                    $entity->save();

                    Entity::findOrFail($ids->id)->delete();
                }

                foreach ($collectionEntity as $collection)
                {
                    $code = $this->getCodeEntity($request->child_id);
                    $quantity = $this->getCountEntity($code);
                    $this->listEntities($collection, $quantity, $code, $request->entity_change_id);
                }

                foreach ($collectionChildren as $collection)
                {
                    $code = $this->getCodeEntity($request->entity_change_id);
                    $quantity = $this->getCountEntity($code);
                    $this->listEntities($collection, $quantity, $code, $request->entity_change_id);
                }
                break;
        }
    }

    public function trash($id)
    {
        $entity_ids = $this->getRelationEntityChildrenId($id);
        $dependency_ids = $this->getRelationDependencyId($id);

        foreach ($entity_ids as $ids)
        {
            $entity = Entity::findOrFail($ids->id);
            $entity->dependency = 'trashed';
            $entity->abbreviation = 'trashed_'.$ids->id;
            $entity->save();

            Entity::findOrFail($ids->id)->delete();
        }

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

        $trashed = Entity::select(
            'id',
            'description',
            'code',
            'direction',
            'ruc',
            'dependency',
            'entity_type_id'
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
        $entity = Entity::onlyTrashed()->where('id', $id)->firstOrFail();

        $entity->forceDelete();
    }
}
