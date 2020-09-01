<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDependenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 15);
            $table->string('description');
            $table->text('abbreviation');
            $table->string('dependency', 15);
            $table->unsignedInteger('parent')->nullable();
            $table->unsignedInteger('start_procedure');
            $table->text('created_by');

            $table->unsignedInteger('entity_id');
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');

            $table->unsignedInteger('dependency_type_id');
            $table->foreign('dependency_type_id')->references('id')->on('dependency_types')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('dependencies')->insert(array(
            'id'                 => 1,
            'code'               => 0,
            'description'        => 'GERENTE GENERAL',
            'abbreviation'       => 'GGEN',
            'dependency'         => 0,
            'parent'             => 1,
            'start_procedure'    => 0,
            'created_by'         => 1,
            'entity_id'          => 1,
            'dependency_type_id' => 2,
            'created_at'         => Carbon::now(),
            'updated_at'         => Carbon::now()
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependencies');
    }
}
