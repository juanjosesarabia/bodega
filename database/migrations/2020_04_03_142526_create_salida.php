<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salida', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id_salida');            
            $table->integer('cedulaNombreRetira')->required();;
            $table->string('nombreRetira', 300)->required();;
            $table->set('salidaAprobada', ['si', 'no'])->default('no');;
            $table->date('fechaSalida');
            $table->integer('cedulaNombreOficiaSalida')->nullable();
            $table->string('nombreOficiaSalida', 300)->nullable();;
            $table->integer("cantidadRetirada")->required();
            $table->set('datoSalida', ['entrega', 'destruccion','custodia'])->default('entrega');
            $table->bigInteger('id_ingreso')->unsigned();
            $table->foreign('id_ingreso')->references('id_ingreso')->on('ingreso')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('salida');
    }
}
