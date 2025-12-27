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
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('name');
            $table->string('sku')->index();
            $table->string('barcode')->nullable()->index();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_age_verification')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['sku', 'store_id']);
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
