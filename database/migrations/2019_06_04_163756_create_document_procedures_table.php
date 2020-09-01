<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date')->nullable();
            $table->mediumText('observation');
            $table->string('records', 10);

            $table->unsignedInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents');

            $table->unsignedInteger('procedure_requirement_id');
            $table->foreign('procedure_requirement_id')->references('id')->on('procedure_requirements');

            $table->unsignedInteger('process_state_id');
            $table->foreign('process_state_id')->references('id')->on('process_states');

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
        Schema::dropIfExists('document_procedures');
    }
}
