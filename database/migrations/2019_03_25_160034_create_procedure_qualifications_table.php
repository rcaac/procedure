<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedure_qualifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description', 30);
            $table->timestamps();
        });
        DB::table('procedure_qualifications')->insert(array('id'=>'1','description'=>'AUTOMÁTICO','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('procedure_qualifications')->insert(array('id'=>'2','description'=>'POSITIVO','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('procedure_qualifications')->insert(array('id'=>'3','description'=>'NEGATIVO','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procedure_qualifications');
    }
}
