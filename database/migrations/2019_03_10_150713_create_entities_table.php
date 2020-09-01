<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 20);
            $table->string('description');
            $table->string('direction')->nullable();
            $table->string('ruc', 11)->unique()->nullable();
            $table->text('abbreviation')->nullable();
            $table->string('dependency', 20);
            $table->unsignedInteger('parent')->nullable();
            $table->text('created_by');

            $table->unsignedInteger('entity_type_id');
            $table->foreign('entity_type_id')->references('id')->on('entity_types')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('entities')->insert(array(
            'id'             => 1,
            'code'           => 0,
            'description'    => 'BUSINESS++',
            'direction'      => 'Calle Bellavista mz b lt 15',
            'ruc'            => 10438669294,
            'abbreviation'   => 'BSS',
            'dependency'     => 0,
            'parent'         => 1,
            'created_by'     => 1,
            'entity_type_id' => 2,
            'created_at'     => Carbon::now(),
            'updated_at'     => Carbon::now()
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entities');
    }
}
