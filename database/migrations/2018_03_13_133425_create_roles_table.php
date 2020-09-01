<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->unique();
            $table->string('description', 100)->nullable();
        });
        DB::table('roles')->insert(array('id'=>'1','name'=>'Super Admin', 'description'=>'Super Administrador'));
        DB::table('roles')->insert(array('id'=>'2','name'=>'Admin', 'description'=>'Administrador del Sistema'));
        DB::table('roles')->insert(array('id'=>'3','name'=>'Internal', 'description'=>'Usuario Interno'));
        DB::table('roles')->insert(array('id'=>'4','name'=>'External', 'description'=>'Usuario Externo'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
