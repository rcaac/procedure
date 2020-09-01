<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDependencyTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependency_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });
        DB::table('dependency_types')->insert(array('id'=>'1','description'=>'DEPENDE DEL ROF','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('dependency_types')->insert(array('id'=>'2','description'=>'NO DEPENDE DEL ROF','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependency_types');
    }
}
