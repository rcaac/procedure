<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName', 60);
            $table->string('lastName', 60);
            $table->string('dni', 8)->min(8)->max(12);
            $table->string('phone', 30)->nullable();
            $table->string('email', 60)->nullable()->unique();
            $table->string('direction', 100)->nullable();
            $table->text('created_by');

            $table->unsignedInteger('identification_document_id');
            $table->foreign('identification_document_id')->references('id')->on('identification_documents');

            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('persons')->insert(array(
            'id'                         =>'1',
            'firstName'                  =>'Andrés',
            'lastName'                   =>'Atachagua',
            'dni'                        => '12457889',
            'phone'                      => '12457889',
            'email'                      => 'admin@gmail.com',
            'direction'                  => 'Dirección del admin',
            'created_by'                 => 1,
            'identification_document_id' => 1,
            'created_at'                 =>Carbon::now(),
            'updated_at'                 =>Carbon::now()
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persons');
    }
}
