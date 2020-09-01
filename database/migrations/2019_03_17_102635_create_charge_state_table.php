<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_state', function (Blueprint $table) {
            $table->increments('id');
            $table->string('charge', 30);
            $table->timestamps();
        });
        DB::table('charge_state')->insert(array('id'=>'1','charge'=>'Activo','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
        DB::table('charge_state')->insert(array('id'=>'2','charge'=>'Inactivo','created_at'=>Carbon::now(),'updated_at'=>Carbon::now()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_state');
    }
}
