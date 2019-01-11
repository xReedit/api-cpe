<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('state_types', function (Blueprint $table) {
            $table->char('id', 2)->index();
            $table->string('description');
        });

        DB::table('state_types')->insert([
            ['id' => '01', 'description' => 'Registrado'],
            ['id' => '02', 'description' => 'Enviado Aceptado'],
            ['id' => '03', 'description' => 'Enviado Observación'],
            ['id' => '04', 'description' => 'Enviado Rechazado'],
            ['id' => '05', 'description' => 'Anulado'],
            ['id' => 'a1', 'description' => 'Solicitud de anulación enviada'],
            ['id' => 'a2', 'description' => 'Error en enviar la anulación'],
            ['id' => 'a3', 'description' => 'Solicitud de anulación aceptada'],
            ['id' => 'r1', 'description' => 'Solicitud de resumen enviada'],
            ['id' => 'r2', 'description' => 'Error al enviar resumen'],
            ['id' => 'r3', 'description' => 'Solicitud de resumen aceptada'],
        ]);

        Schema::create('soap_types', function (Blueprint $table) {
            $table->char('id', 2)->index();
            $table->string('description');
        });

        DB::table('soap_types')->insert([
            ['id' => '01', 'description' => 'Demo'],
            ['id' => '02', 'description' => 'Producción'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('state_types');
        Schema::dropIfExists('soap_types');
    }
}
