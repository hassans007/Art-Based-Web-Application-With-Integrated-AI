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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('artist_id');
            $table->smallinteger('year_created')->nullable();
            $table->unsignedBigInteger('style_id')->nullable();
            $table->string('image_path');
            $table->text('description')->nullable();
            $table->timestamps();
        
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
            $table->foreign('style_id')->references('id')->on('styles')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
