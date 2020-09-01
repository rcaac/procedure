<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('registry', 10)->unique();
            $table->string('reference');
            $table->text('affair');
            $table->text('description')->nullable();
            $table->text('annexes')->nullable();
            $table->dateTime('shipping_date')->nullable();
            $table->string('code');
            $table->string('records', 10);
            $table->string('number', 10);
            $table->string('file', 60);
            $table->integer('folio');
            $table->boolean('tupa');
            $table->text('created_by');
            $table->boolean('transparency');

            $table->unsignedInteger('document_origin_id');
            $table->foreign('document_origin_id')->references('id')->on('document_origins');

            $table->unsignedInteger('document_priority_id');
            $table->foreign('document_priority_id')->references('id')->on('document_priorities');

            $table->unsignedInteger('document_type_id');
            $table->foreign('document_type_id')->references('id')->on('document_types');

            $table->unsignedInteger('type_reception_id');
            $table->foreign('type_reception_id')->references('id')->on('type_receptions');

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
        Schema::dropIfExists('documents');
    }
}
