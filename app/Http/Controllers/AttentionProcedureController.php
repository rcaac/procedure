<?php

namespace App\Http\Controllers;

use App\AttentionDetail;
use App\AttentionProcedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttentionProcedureController extends Controller
{
    public function store()
    {
       request()->validate([
            'attention_type_id' => 'required|integer|not_in:0',
            'procedure_id'      => 'required',
            'dependency_id'     => 'required'
        ], [
            'attention_type_id.not_in' => 'Elige un recurso',
            'procedure_id.required'      => 'Elige una procedimiento',
            'dependency_id.required'     => 'Elige una dependencia'
        ]);

        $data = request();

        try{
            DB::beginTransaction();

            $attentionProcedure = AttentionProcedure::create([
                'dependency_id'     => $data['dependency_id'],
                'procedure_id'      => $data['procedure_id'],
                'attention_type_id' => $data['attention_type_id'],
            ]);
            if (! $data['detail'] == null)
            {
                AttentionDetail::create([
                    'detail'                 => $data['detail'],
                    'attention_procedure_id' => $attentionProcedure->id
                ]);
            }
            DB::commit();

            return $attentionProcedure;
        } catch (\Exception $e){
            DB::rollBack();
        }

    }

    public function update(Request $request)
    {
        try{
            DB::beginTransaction();

            switch ($request)
            {
                case ($request->attention_detail_id == null) && ($request->detail == null):
                    $attentionProcedure = AttentionProcedure::findOrFail($request->attention_procedure_id);

                    $attentionProcedure->dependency_id     = $request->dependency_id;
                    $attentionProcedure->procedure_id      = $request->procedure_id;
                    $attentionProcedure->attention_type_id = $request->attention_type_id;
                    $attentionProcedure->save();
                    break;
                case ($request->attention_detail_id == null) && ($request->detail != null):
                    $attentionProcedure = AttentionProcedure::findOrFail($request->attention_procedure_id);

                    $attentionProcedure->dependency_id     = $request->dependency_id;
                    $attentionProcedure->procedure_id      = $request->procedure_id;
                    $attentionProcedure->attention_type_id = $request->attention_type_id;
                    $attentionProcedure->save();

                    AttentionDetail::create([
                        'detail'                 => $request->detail,
                        'attention_procedure_id' => $request->attention_procedure_id
                    ]);
                    break;
                case ($request->attention_detail_id != null) && ($request->detail == null):
                    AttentionDetail::findOrFail($request->attention_detail_id)->delete();
                    break;
                case ($request->attention_detail_id != null) && ($request->detail != null) && ($request->resource_detail == 1):
                    AttentionDetail::findOrFail($request->attention_detail_id)->delete();

                    $attentionProcedure = AttentionProcedure::findOrFail($request->attention_procedure_id);

                    $attentionProcedure->dependency_id     = $request->dependency_id;
                    $attentionProcedure->procedure_id      = $request->procedure_id;
                    $attentionProcedure->attention_type_id = $request->attention_type_id;
                    $attentionProcedure->save();
                    break;
                case ($request->attention_detail_id != null) && ($request->detail != null):
                    $attentionProcedure = AttentionProcedure::findOrFail($request->attention_procedure_id);

                    $attentionProcedure->dependency_id     = $request->dependency_id;
                    $attentionProcedure->procedure_id      = $request->procedure_id;
                    $attentionProcedure->attention_type_id = $request->attention_type_id;
                    $attentionProcedure->save();

                    $attentionDetail = AttentionDetail::findOrFail($request->attention_detail_id);
                    $attentionDetail->detail                 = $request->detail;
                    $attentionDetail->attention_procedure_id = $request->attention_procedure_id;
                    $attentionDetail->save();
                    break;
            }

            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
        }
    }

    public function trashAttentionProcedure($id)
    {
        AttentionProcedure::findOrFail($id)->delete();
    }

}
