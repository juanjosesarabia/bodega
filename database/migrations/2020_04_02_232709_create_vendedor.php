<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendedor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendedor', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements("id_vendedor");
            $table->integer("cedula")->unique()->required();;            
            $table->String("nombres",45)->required();;
            $table->String("apellidos",45)->required();;            
            $table->String("telefono",45);           
            $table->integer("historial");
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
        Schema::dropIfExists('vendedor');
    }
}
