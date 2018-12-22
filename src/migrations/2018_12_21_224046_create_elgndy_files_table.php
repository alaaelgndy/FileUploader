<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElgndyFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('elgndy_files', function (Blueprint $table) {
	    $table->increments('id');
	    $table->string('file_path');
	    $table->integer('file_status');
	    $table->string('related_model');
	    $table->integer('related_id');
	    $table->timestamps();
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	Schema::dropIfExists('elgndy_files');
    }
}
