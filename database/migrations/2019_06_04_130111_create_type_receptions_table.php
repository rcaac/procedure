<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeReceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_receptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reception', 30);
            $table->timestamps();
        });
        DB::table('type_receptions')->insert(array('id'=>'1','reception'=>'DIRECTA','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('type_receptions')->insert(array('id'=>'2','reception'=>'CORREO ELECTRÓNICO','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('type_receptions')->insert(array('id'=>'3','reception'=>'FAX','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_receptions');
    }
}
