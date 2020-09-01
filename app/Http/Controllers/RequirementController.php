<?php

namespace App\Http\Controllers;

use App\ProcedureRequirement;
use App\Requirement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::paginate(10);

        return [
            'pagination' => [
                'total'        => $requirements->total(),
                'current_page' => $requirements->currentPage(),
                'per_page'     => $requirements->perPage(),
                'last_page'    => $requirements->lastPage(),
                'from'         => $requirements->firstItem(),
                'to'           => $requirements->lastItem(),
            ],
            'requirements' => $requirements
        ];
    }

    public function getListRequirement($id)
    {
        $requirementLists = Requirement::where('id', '=', $id)->get();

        return ['requirementLists' => $requirementLists];
    }

    public function store(Request $request)
    {
        $rules = [
            'procedure_id' => 'required|not_in:0',
            'inputs.*.description' => 'required',
            'inputs.*.cost'        => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 'false', 'errors' => $validator->errors()], 422);
        }

        try{
            DB::beginTransaction();

            foreach ($request->inputs as $value)
            {
                $requirement = Requirement::create([
                    'description' => $value['description'],
                ]);

                $procedure_requirement = new ProcedureRequirement();
                if (isset($value['cost'])) {
                    $procedure_requirement->cost = $value['cost'];
                }else {
                    $procedure_requirement->cost = '0.0';
                }
                $procedure_requirement->procedure_id = $request->procedure_id;
                $procedure_requirement->requirement_id = $requirement->id;
                $procedure_requirement->created_at = Carbon::now();
                $procedure_requirement->updated_at = Carbon::now();
                $procedure_requirement->save();
            }

            DB::commit();

            return ['success' => 'true'];

        } catch (Exception $e){
            DB::rollBack();
        }

    }

    public function trashRequirement($id)
    {
        Requirement::findOrFail($id)->delete();

        $procedure_requirement_id = ProcedureRequirement::where('requirement_id', $id)->value('id');

        ProcedureRequirement::findOrFail($procedure_requirement_id)->delete();


    }

}
