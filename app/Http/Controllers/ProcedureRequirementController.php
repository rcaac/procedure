<?php

namespace App\Http\Controllers;

use App\AttentionProcedure;
use App\ProcedureRequirement;
use App\Requirement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProcedureRequirementController extends Controller
{
    public function getDependentTheProcedure(Request $request)
    {
        $page = $request->page;
        $paginate = 5;

        $procedureRequirements = ProcedureRequirement::join('requirements', 'procedure_requirements.requirement_id', '=', 'requirements.id')
            ->select(
                'requirements.id',
                'requirements.description',
                'procedure_requirements.cost',
                'procedure_requirements.id AS procedure_requirement_id'
            )
            ->where('procedure_requirements.procedure_id', '=', $request->id)
            ->where('requirements.deleted_at', '=', null)
            ->get()
            ->toArray();

        $slice = array_slice($procedureRequirements, $paginate * ($page - 1), $paginate);
        $data = new LengthAwarePaginator($slice, count($procedureRequirements), $paginate);

        $attentionProcedures = AttentionProcedure::join('dependencies', 'attention_procedures.dependency_id', '=', 'dependencies.id')
            ->join('procedures', 'attention_procedures.procedure_id', '=', 'procedures.id')
            ->join('attention_types', 'attention_procedures.attention_type_id', '=', 'attention_types.id')
            ->leftjoin('attention_details', 'attention_details.attention_procedure_id', '=', 'attention_procedures.id')
            ->select(
                'attention_procedures.id',
                'dependencies.description',
                'attention_details.detail',
                'attention_details.id as attention_detail_id',
                'attention_types.description as types',
                'attention_types.resource_detail',
                'attention_procedures.dependency_id',
                'attention_procedures.procedure_id',
                'attention_procedures.attention_type_id'
            )
            ->where('attention_procedures.procedure_id', '=', $request->id)
            ->get();

        return [
            'pagination' => [
                'total'        => $data->total(),
                'current_page' => $data->currentPage(),
                'per_page'     => $data->perPage(),
                'last_page'    => $data->lastPage(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
            ],
            'procedureRequirements' => $data,
            'attentionProcedures' => $attentionProcedures
        ];
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'cost'        => 'required',
            'description' => 'required'
            ],
            [
            'cost.required'        => 'El costo es obligatorio',
            'description.required' => 'El requisito es obligatorio'

            ]
        );
        try{
            DB::beginTransaction();

        $requirement = Requirement::findOrFail($request->requirement_id);
        $requirement->description = $request->description;
        $requirement->save();

        $procedureRequirement = ProcedureRequirement::findOrFail($request->procedure_requirement_id);
        $procedureRequirement->cost = $request->cost;
        $procedureRequirement->procedure_id = $request->procedure_id;
        $procedureRequirement->requirement_id = $requirement->id;
        $procedureRequirement->save();

            DB::commit();

        } catch (Exception $e){
            DB::rollBack();
        }
    }
}
