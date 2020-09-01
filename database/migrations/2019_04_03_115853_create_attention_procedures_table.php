<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttentionProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attention_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dependency_id');
            $table->foreign('dependency_id')->references('id')->on('dependencies')->onDelete('cascade');
            $table->unsignedInteger('procedure_id');
            $table->foreign('procedure_id')->references('id')->on('procedures')->onDelete('cascade');
            $table->unsignedInteger('attention_type_id');
            $table->foreign('attention_type_id')->references('id')->on('attention_types')->onDelete('cascade');
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
        Schema::dropIfExists('attention_procedures');
    }
}
