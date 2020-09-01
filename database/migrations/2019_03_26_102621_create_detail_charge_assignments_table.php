<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailChargeAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_charge_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('startDate', 30);
            $table->string('finalDate', 30);
            $table->string('detail');

            $table->unsignedInteger('charge_assignment_id');
            $table->foreign('charge_assignment_id')->references('id')->on('charge_assignments')->onDelete('cascade');

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
        Schema::dropIfExists('detail_charge_assignments');
    }
}
