<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_procedures', function (Blueprint $table) {
            $table->string('file')->after('procedure_requirement_id')->nullable();
            $table->string('code')->after('process_state_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_procedures', function (Blueprint $table) {
            $table->dropColumn('file');
            $table->dropColumn('code');
        });
    }
}
