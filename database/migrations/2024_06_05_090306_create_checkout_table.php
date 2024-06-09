<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checkout', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('metode_pembayaran');
            $table->string('metode_pengiriman');
            $table->text('alamat');
            $table->integer('total_harga_product')->default(0);
            $table->integer('biaya_pengiriman')->default(0);
            $table->timestamps();
        });

        Schema::table('checkout', function (Blueprint $table) {
            $table->foreign('customer_id', 'customer_id_fk')->references('id')->on('checkout')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_to_cart', function (Blueprint $table) {
            $table->dropForeign('customer_id_fk');
        });

        Schema::dropIfExists('checkout');
    }
};
