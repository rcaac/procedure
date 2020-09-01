<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('charge');
            $table->unsignedInteger('type');

            $table->unsignedInteger('person_id');
            $table->foreign('person_id')->references('id')->on('persons');

            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->unsignedInteger('dependency_id');
            $table->foreign('dependency_id')->references('id')->on('dependencies');

            $table->unsignedInteger('charge_state_id');
            $table->foreign('charge_state_id')->references('id')->on('charge_state');

            $table->text('created_by');

            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('charge_assignments')->insert(array(
            'id'=>'1',
            'charge'=>'Super',
            'type'=>'1',
            'person_id' => 1,
            'role_id' => 1,
            'dependency_id' => 1,
            'charge_state_id' => 1,
            'created_by' => 1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_assignments');
    }
}
