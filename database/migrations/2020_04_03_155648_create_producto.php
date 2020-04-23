<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id_producto');
            $table->string('nombre', 100)->required();;
            $table->string('descripcion', 500)->required();; 
            $table->string('codigoBarra', 100)->unique()->required();
            $table->bigInteger('id_vendedor')->unsigned()->nullable();
            $table->foreign('id_vendedor')->references('id_vendedor')->on('vendedor')->onDelete('cascade');
            $table->bigInteger('id_ingreso')->unsigned()->nullable();
            $table->foreign('id_ingreso')->references('id_ingreso')->on('ingreso')->onDelete('cascade');
            $table->bigInteger('id_salida')->unsigned()->nullable();
            $table->foreign('id_salida')->references('id_salida')->on('salida')->onDelete('cascade');
            $table->set('riesgo', ['si', 'no'])->default('no');
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
        Schema::dropIfExists('producto');
    }
}
