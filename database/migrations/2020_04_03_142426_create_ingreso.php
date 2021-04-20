<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngreso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id_ingreso');            
            $table->integer('cedulaNombreRecibe',100)->required();;        
            $table->string('nombreRecibe', 200)->required();;
            $table->date('fechaIngreso')->required();;            
            $table->integer("numero_acta",100)->unique()->required();;
            $table->integer("cantidadIngresada",1000)->required();;
            $table->string('ubicacionOperativo', 800);            
            $table->bigInteger('id_vendedor')->unsigned();
            $table->foreign('id_vendedor')->references('id_vendedor')->on('vendedor')->onDelete('cascade');
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
        Schema::dropIfExists('ingreso');
    }
}
