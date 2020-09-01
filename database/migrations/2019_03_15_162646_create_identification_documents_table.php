<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentificationDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identification_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('document', 60);
            $table->timestamps();
        });
        DB::table('identification_documents')->insert(array('id'=>'1','document'=>'DNI','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('identification_documents')->insert(array('id'=>'2','document'=>'CARNET DE EXTRANGERÍA','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('identification_documents')->insert(array('id'=>'3','document'=>'PASAPORTE','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identification_documents');
    }
}
