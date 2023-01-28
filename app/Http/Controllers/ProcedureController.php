<?php

namespace App\Http\Controllers;

use App\AttentionProcedure;
use App\AttentionType;
use App\Dependency;
use App\Module;
use App\Person;
use App\Procedure;
use App\ProcedureQualification;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function index()
    {
        $modules = Module::select('id', 'description')->get();

        $qualifications = ProcedureQualification::select('id', 'description')->get();

        $procedures = Person::select('procedures.id', 'procedures.description')
            ->join('charge_assignments', 'charge_assignments.person_id', '=', 'persons.id')
            ->join('dependencies', 'charge_assignments.dependency_id', '=', 'dependencies.id')
            ->join('entities', 'dependencies.entity_id', 'entities.id')
            ->join('procedures', 'persons.id', 'procedures.created_by')
            ->where('entities.id', $this->getEntityId())
            ->groupBy('procedures.id')
            ->get();

        if ($this->getRole() == 1) {
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('charge_assignments.deleted_at', '=', null)
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.dependency_type_id', 1)
                ->groupBy('charge_assignments.dependency_id')
                ->get();
        }else if ($this->getRole() == 2){
            $dependencies = Dependency::select('dependencies.id', 'dependencies.description')
                ->join('charge_assignments', 'charge_assignments.dependency_id', '=', 'dependencies.id')
                ->join('entities', 'entities.id', '=', 'dependencies.entity_id')
                ->where('charge_assignments.charge_state_id', '=', 1)
                ->where('charge_assignments.deleted_at', '=', null)
                ->where('dependencies.description', '!=', 'USUARIO EXTERNO')
                ->where('dependencies.dependency_type_id', 1)
                ->where('dependencies.entity_id', $this->getEntityId())
                ->groupBy('charge_assignments.dependency_id')
                ->get();
        }

        $dependency = Dependency::select('id', 'description AS label')->get();

        $attentionTypes = AttentionType::select('id', 'description')->get();

        return [
            'modules' => $modules,
            'qualifications' => $qualifications,
            'procedures' => $procedures,
            'dependencies' => $dependencies,
            'dependency' => $dependency,
            'attentionTypes' => $attentionTypes,
        ];

    }

    private function getPersonId()
    {
        return auth()->user()->person->id;
    }

    private function getRole()
    {
        return auth()->user()->person->chargeAssignments->first()->role_id;
    }

    private function getDependencyId()
    {
        return auth()->user()->person->chargeAssignments->first()->dependency_id;
    }

    private function getEntityId()
    {
        return Dependency::where('id', $this->getDependencyId())->value('entity_id');
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'module_id'                  => 'required|integer|not_in:0',
                'procedure_qualification_id' => 'required|integer|not_in:0',
                'description'                => 'required',
                'legal_base'                 => 'required',
                'term'                       => 'required|numeric'
            ],
            [
                'module_id.not_in'                  => 'Elige un módulo',
                'procedure_qualification_id.not_in' => 'Elige una calificación',
                'description.required'              => 'Ingresa procedimiento',
                'legal_base.required'               => 'Ingresa base legal',
                'term.required'                     => 'Ingresa plazo a resolver',
                'term.numeric'                      => 'Únicamente números'
            ]
        );

        $procedure = new Procedure();
        $procedure->module_id                  = $request->module_id;
        $procedure->procedure_qualification_id = $request->procedure_qualification_id;
        $procedure->description                = $request->description;
        $procedure->note                       = $request->note;
        $procedure->legal_base                 = $request->legal_base;
        $procedure->term                       = $request->term;
        $procedure->created_by                 = $this->getPersonId();
        $procedure->save();

        return  $procedure;
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'module_id'                  => 'required|integer|not_in:0',
                'procedure_qualification_id' => 'required|integer|not_in:0',
                'description'                => 'required',
                'legal_base'                 => 'required',
                'term'                       => 'required|numeric'
            ],
            [
                'module_id.not_in'                  => 'Elige un módulo',
                'procedure_qualification_id.not_in' => 'Elige una calificación',
                'description.required'              => 'Ingresa procedimiento',
                'legal_base.required'               => 'Ingresa base legal',
                'term.required'                     => 'Ingresa plazo a resolver',
                'term.numeric'                      => 'Únicamente números'
            ]
        );

        $procedure = Procedure::findOrFail($request->id);
        $procedure->module_id                  = $request->module_id;
        $procedure->procedure_qualification_id = $request->procedure_qualification_id;
        $procedure->description                = $request->description;
        $procedure->note                       = $request->note;
        $procedure->legal_base                 = $request->legal_base;
        $procedure->term                       = $request->term;
        $procedure->save();

        return  $procedure;
    }

    private function queryProcedure()
    {
        return Procedure::join('modules', 'modules.id', '=', 'procedures.module_id')
            ->join('procedure_qualifications', 'procedure_qualifications.id', '=', 'procedures.procedure_qualification_id')
            ->select(
                'procedures.id',
                'procedures.module_id',
                'procedures.procedure_qualification_id',
                'modules.description AS module_description',
                'procedure_qualifications.description AS qualification',
                'procedures.description',
                'procedures.legal_base',
                'procedures.note',
                'procedures.term'
            )
            ->orderBy('procedures.id', 'DESC')
            ->whereNull('procedures.deleted_at');
    }

    public function listProcedure(Request $request)
    {
        if ($request->search == '' && $request->module == 0) {
            $query = $this->queryProcedure()
                ->paginate(5);
        }

        if ($request->search != '' && $request->module == 0) {
            $query = $this->queryProcedure()
                ->where('procedures.description', 'LIKE', '%' . $request->search . '%')
                ->paginate(5);
        }

        if ($request->search == '' && $request->module != 0) {
            $query = $this->queryProcedure()
                ->where('procedures.module_id', $request->module)
                ->paginate(5);
        }

        return [
            'pagination' => [
                'total' => $query->total(),
                'current_page' => $query->currentPage(),
                'per_page' => $query->perPage(),
                'last_page' => $query->lastPage(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
            'procedure' => $query
        ];
    }

    public function listProcedureQualification()
    {
        $procedure_qualifications = ProcedureQualification::select('id', 'description')->get();

        return [
            'procedure_qualifications' => $procedure_qualifications
        ];
    }

    public function trashProcedure($id)
    {
        Procedure::findOrFail($id)->delete();
    }
}
