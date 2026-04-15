<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCobrosTable extends Migration
{
    public function up()
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->decimal('monto_pagado', 10, 2);
            $table->decimal('monto_aplicado', 10, 2);            
            $table->string('metodo_pago');
            $table->timestamp('fecha_cobro')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cobros');
    }
}
