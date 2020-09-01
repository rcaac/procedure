<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentaryProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentary_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('reception_date')->nullable();
            $table->dateTime('procedure_date')->nullable();

            $table->unsignedInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents');

            $table->text('observations')->nullable();

            $table->unsignedInteger('procedure_state_id');
            $table->foreign('procedure_state_id')->references('id')->on('procedure_states');

            $table->unsignedInteger('person_id');
            $table->foreign('person_id')->references('id')->on('persons');

            $table->unsignedInteger('dependency_shipping_id');
            $table->foreign('dependency_shipping_id')->references('id')->on('dependencies');

            $table->unsignedInteger('dependency_reception_id');
            $table->foreign('dependency_reception_id')->references('id')->on('dependencies');

            $table->unsignedInteger('person_reception');
            $table->foreign('person_reception')->references('id')->on('persons');

            $table->unsignedInteger('provided_id');
            $table->foreign('provided_id')->references('id')->on('provided');

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
        Schema::dropIfExists('documentary_procedures');
    }

}
