<?php

namespace App\Http\Controllers;

use App\Dependency;
use App\Document;
use App\DocumentaryProcedure;
use App\Entity;
use App\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DocumentPendingController extends Controller
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

    private function queryPending()
    {
        return Document::join('documentary_procedures', 'documentary_procedures.document_id', '=', 'documents.id')
            ->join('persons', 'documentary_procedures.person_id', '=', 'persons.id')
            ->join('dependencies AS Dependency', 'Dependency.id', '=', 'documentary_procedures.dependency_shipping_id')
            ->join('entities', 'Dependency.entity_id', '=', 'entities.id')
            ->join('document_priorities', 'documents.document_priority_id', '=', 'document_priorities.id')
            ->join('provided', 'documentary_procedures.provided_id', '=', 'provided.id')
            ->select(
                'documents.id',
                'documentary_procedures.id AS documentary_procedure_id',
                'documents.records',
                'documents.registry',
                'documents.reference',
                'documents.code',
                'document_priorities.priority',
                'entities.description',
                'Dependency.description AS Dependency',
                'provided.provided',
                'documents.affair',
                'documents.folio',
                'documents.shipping_date',
                'documents.tupa',
                'documents.annexes',
                'persons.dni',
                'documentary_procedures.procedure_state_id AS state',
                DB::raw('CONCAT(firstName, " ", lastName) AS fullName')
            )
            ->where('documentary_procedures.dependency_reception_id', $this->getDependencyId())
            ->where('documentary_procedures.procedure_state_id', 1)
            ->orderBy('documents.id', 'ASC');
    }

    public function getSlopes(Request $request)
    {
        $search = $request->search;

        if ($request->search == '') {
            switch ($request)
            {
                case($request->pending_change_id == 0 && $request->document_type_id == 0):
                    $query = $this->queryPending()->paginate(5);
                    break;
                case($request->pending_change_id == 1 && $request->document_type_id == 0):
                    $query = $this->queryPending()->where('documents.document_origin_id', '=', 1)->paginate(5);
                    break;
                case($request->pending_change_id == 2 && $request->document_type_id == 0):
                    $query = $this->queryPending()->where('documents.document_origin_id', '!=', 1)->paginate(5);
                    break;
                case($request->pending_change_id == 0 && $request->document_type_id != 0):
                    $query = $this->queryPending()->where('documents.document_type_id', '=', $request->document_type_id)->paginate(5);
                    break;
                case($request->pending_change_id == 1 && $request->document_type_id != 0):
                    $query = $this->queryPending()->where('documents.document_origin_id', '=', 1)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
                case($request->pending_change_id == 2 && $request->document_type_id != 0):
                    $query = $this->queryPending()->where('documents.document_origin_id', '!=', 1)
                        ->where('documents.document_type_id', '=', $request->document_type_id)
                        ->paginate(5);
                    break;
            }

        }else {
            $query = $this->queryPending()
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
            'slopes' => $query
        ];

    }

    private function queryPartyTable()
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
                'documentary_procedures.id AS documentary_procedure_id',
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
            ->orderBy('documents.id', 'DESC')
            ->where('documents.document_origin_id', '!=', 1);
    }

    public function getPartyTable(Request $request)
    {
        $search = $request->search;

        switch ($request)
        {
            case(
                $request->pending_change_id == 0
                 && $request->search == ''
                 && $request->transparency == 0
                 && $request->initial_date == ''
                 && $request->final_date == ''
            ):
                $query = $this->queryPartyTable()
                        ->paginate(5);
                break;
            case(
                $request->pending_change_id != 0
                && $request->search == ''
                && $request->transparency == 0
                && $request->initial_date == ''
                && $request->final_date == ''
            ):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id == 0
                && $request->search != ''
                && $request->transparency == 0
                && $request->initial_date == ''
                && $request->final_date == ''
            ):
                $query = $this->queryPartyTable()
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id != 0
                && $request->search != ''
                && $request->transparency == 0
                && $request->initial_date == ''
                && $request->final_date == ''
            ):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id == 0
                && $request->search == ''
                && $request->transparency == 1
                && $request->initial_date == ''
                && $request->final_date == ''
            ):
                $query = $this->queryPartyTable()
                    ->where('documents.transparency', 1)
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id != 0
                && $request->search == ''
                && $request->transparency == 1
                && $request->initial_date == ''
                && $request->final_date == ''
            ):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where('documents.transparency', 1)
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where('documents.transparency', 1)
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id != 0
                && $request->search != ''
                && $request->transparency == 1
                && $request->initial_date == ''
                && $request->final_date == ''
            ):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where('documents.transparency', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where('documents.transparency', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id == 0
                && $request->search == ''
                && $request->transparency == 0
                && $request->initial_date != ''
                && $request->final_date == ''
            ):
                $query = $this->queryPartyTable()
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->paginate(5);
                break;
            case(
                $request->pending_change_id != 0
                && $request->search == ''
                && $request->transparency == 0
                && $request->initial_date != ''
                && $request->final_date == ''
            ):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereDate('documents.created_at', $request->initial_date)
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->paginate(5);
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereDate('documents.created_at', $request->initial_date)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereDate('documents.created_at', $request->initial_date)
                        ->where('documents.transparency', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->where('documents.transparency', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date != ''):
                $query = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->paginate(5);
                break;
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                $query = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->paginate(5);
                break;
            case($request->pending_change_id == 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                $query = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->where('documents.transparency', 1)
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->paginate(5);
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->paginate(5);
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $query = $this->queryPartyTable()
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->where('documents.transparency', 1)
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->paginate(5);
                    break;
                }
                $query = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->paginate(5);
                break;
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
            'partyTable' => $query
        ];
    }

    public function getPartyTableReport(Request $request)
    {
        $entity = Entity::where('id', $this->getEntityId())->value('description');
        $dependency = Dependency::where('id', $this->getDependencyId())->value('description');
        $person = Person::where('id', $this->getPersonId())->value(DB::raw('CONCAT(firstName, " ", lastName)'));

        $search = $request->search;

        switch ($request)
        {
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date == '' && $request->final_date == ''):
                $queryReport = $this->queryPartyTable()->get();
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date == '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)->get();
                break;
            case($request->pending_change_id == 0 && $request->search != '' && $request->transparency == 0 && $request->initial_date == '' && $request->final_date == ''):
                $queryReport = $this->queryPartyTable()
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 0 && $request->initial_date == '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 1 && $request->initial_date == '' && $request->final_date == ''):
                $queryReport = $this->queryPartyTable()
                    ->where('documents.transparency', 1)
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 1 && $request->initial_date == '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where('documents.transparency', 1)
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where('documents.transparency', 1)
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date == '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where('documents.transparency', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where('documents.transparency', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date == ''):
                $queryReport = $this->queryPartyTable()
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereDate('documents.created_at', $request->initial_date)
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereDate('documents.created_at', $request->initial_date)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date == ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereDate('documents.created_at', $request->initial_date)
                        ->where('documents.transparency', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereDate('documents.created_at', $request->initial_date)
                    ->where('documents.transparency', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date != ''):
                $queryReport = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->get();
                break;
            case($request->pending_change_id == 0 && $request->search == '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                $queryReport = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->get();
                break;
            case($request->pending_change_id == 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                $queryReport = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->where('documents.transparency', 1)
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search == '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 0 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
            case($request->pending_change_id != 0 && $request->search != '' && $request->transparency == 1 && $request->initial_date != '' && $request->final_date != ''):
                if ($request->pending_change_id == 1) {
                    $queryReport = $this->queryPartyTable()
                        ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                        ->where('documents.transparency', 1)
                        ->where('documentary_procedures.procedure_state_id', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('documents.registry', 'like', '%'.$search.'%' )
                                ->orWhere('documents.records', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                                ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                        })
                        ->get();
                    break;
                }
                $queryReport = $this->queryPartyTable()
                    ->whereBetween('documents.created_at', [$request->initial_date, $request->final_date])
                    ->where('documents.transparency', 1)
                    ->where('documentary_procedures.procedure_state_id', '!=', 1)
                    ->where(function ($q) use ($search) {
                        $q->where('documents.registry', 'like', '%'.$search.'%' )
                            ->orWhere('documents.records', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.lastName', 'like', '%'.$search.'%' )
                            ->orWhere('Transmitter.dni', 'like', '%'.$search.'%' );
                    })
                    ->get();
                break;
        }

        return [
            'partyTableReport' => $queryReport,
            'count'            => count($queryReport),
            'entity'           => $entity,
            'dependency'       => $dependency,
            'person'           => $person
        ];
    }

    public function slopesFetched($id) {

        $documentary_procedure = DocumentaryProcedure::findOrFail($id);
        $documentary_procedure->procedure_state_id = 2;
        $documentary_procedure->reception_date = Carbon::now();
        $documentary_procedure->save();
    }

    public function getCountPending()
    {
        $pending = DocumentaryProcedure::select('procedure_state_id')
            ->where('procedure_state_id', 1)
            ->where('dependency_reception_id', $this->getDependencyId())
            ->get();

        return ['pending' => $pending];
    }
}
