<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('monto_inicial', 12, 0);
            $table->decimal('total_ventas', 12, 0)->default(0);
            $table->decimal('total_egresos', 12, 0)->default(0);

            $table->decimal('monto_cierre', 12, 0)->nullable();
            $table->decimal('diferencia', 12, 0)->nullable();

            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();

            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
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
        Schema::dropIfExists('cajas');
    }
};
