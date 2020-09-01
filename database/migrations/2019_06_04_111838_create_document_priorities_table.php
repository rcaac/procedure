<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentPrioritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_priorities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('priority', 60);
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('document_priorities')->insert(array('id'=>'1','priority'=>'IMPORTANTE','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('document_priorities')->insert(array('id'=>'2','priority'=>'NORMAL','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('document_priorities')->insert(array('id'=>'3','priority'=>'URGENTE','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_priorities');
    }
}
