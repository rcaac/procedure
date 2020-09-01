<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->timestamps();
        });
        DB::table('entity_types')->insert(array('id'=>'1','type'=>'ENTIDAD PÚBLICA','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('entity_types')->insert(array('id'=>'2','type'=>'ENTIDAD PRIVADA','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('entity_types')->insert(array('id'=>'3','type'=>'ENTIDAD MIXTA','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('entity_types')->insert(array('id'=>'4','type'=>'OTROS','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_types');
    }
}
