<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_document_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('detail');
            $table->timestamps();
        });
        DB::table('detail_document_types')->insert(array('id'=>'1','detail'=>'SIMPLE','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('detail_document_types')->insert(array('id'=>'2','detail'=>'MÚLTIPLE','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_document_types');
    }
}
