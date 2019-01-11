<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnulmentDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annulment_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('annulment_id');
            $table->unsignedInteger('document_id');
            $table->string('description');

            $table->foreign('annulment_id')->references('id')->on('annulments');
            $table->foreign('document_id')->references('id')->on('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('annulment_documents');
    }
}
