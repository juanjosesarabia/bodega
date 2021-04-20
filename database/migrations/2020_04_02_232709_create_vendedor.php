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
            $table->integer("cedula",100)->unique()->required();;            
            $table->String("nombres",100)->required();;
            $table->String("apellidos",100)->required();;            
            $table->String("telefono",100);           
            $table->integer("historial")->default(0);
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
