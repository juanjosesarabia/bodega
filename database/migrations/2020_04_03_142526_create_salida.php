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
            $table->date('fechaSalida')->required();;
            $table->integer('cedulaNombreOficiaSalida')->required();;
            $table->string('nombreOficiaSalida', 300)->required();;
            $table->integer("cantidadRetirada")->required();;
            $table->bigInteger('id_vendedor')->unsigned();
            $table->foreign('id_vendedor')->references('id_vendedor')->on('vendedor')->onDelete('cascade');
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
