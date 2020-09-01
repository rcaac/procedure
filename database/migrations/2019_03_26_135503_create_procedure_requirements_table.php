<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedure_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cost', 20)->nullable();
            $table->unsignedInteger('procedure_id');
            $table->foreign('procedure_id')->references('id')->on('procedures')->onDelete('cascade');
            $table->unsignedInteger('requirement_id');
            $table->foreign('requirement_id')->references('id')->on('requirements')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procedure_requirements');
    }
}
