<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements("id_usuario"); 
            $table->integer('cedula')->unique();       
            $table->string('nombres',50);            
            $table->string('apellidos',50);
            $table->string('correo',200)->unique();           
            $table->string('contrasena');
            $table->string('tipo_usuario',20)->default("no asignado");
            $table->rememberToken();
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
        Schema::dropIfExists('usuario');
    }
}
