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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('external_id')->unique()->nullable()->default(null);
            $table->string('name', 255)->nullable();
            $table->string('sku', 255)->nullable();
            $table->enum('status', ['sale', 'hidden', 'out', 'deleted'])->nullable()->default('hidden');
            $table->text('variations')->nullable();
            $table->string('image')->nullable()->default(null);
            $table->decimal('price', 7, 2)->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->string('currency', 20)->nullable();
            $table->text('reason')->nullable();
            $table->unique(['sku', 'status']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
