<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->uuid('external_id');
            $table->char('state_type_id', 2);
            $table->char('soap_type_id', 2);
            $table->string('ubl_version');
            $table->string('document_type_code');
            $table->string('series');
            $table->string('number');
            $table->date('date_of_issue');
            $table->time('time_of_issue');
            $table->string('currency_type_code');
            $table->decimal('total_exportation', 12, 2);
            $table->decimal('total_taxed', 12, 2);
            $table->decimal('total_unaffected', 12, 2);
            $table->decimal('total_exonerated', 12, 2);
            $table->decimal('total_igv', 12, 2);
            $table->decimal('total_isc', 12, 2);
            $table->decimal('total_other_taxes', 12, 2);
            $table->decimal('total_other_charges', 12, 2);
            $table->decimal('total_discount', 12, 2);
            $table->decimal('total_value_sale', 12, 2);
            $table->decimal('total_price_sale', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->json('company');
            $table->json('establishment');
            $table->json('customer');
            $table->json('guides');
            $table->json('additional_documents');
            $table->json('legends');
            $table->json('optional')->nullable();
            $table->string('filename');
            $table->string('hash');
            $table->text('qr');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('state_type_id')->references('id')->on('state_types');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
