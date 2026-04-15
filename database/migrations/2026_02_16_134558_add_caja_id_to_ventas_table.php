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
        Schema::table('ventas', function (Blueprint $table) {
        $table->unsignedBigInteger('caja_id')->nullable()->after('user_id');
        $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
        $table->dropForeign(['caja_id']);
        $table->dropColumn('caja_id');
    });
    }
};
