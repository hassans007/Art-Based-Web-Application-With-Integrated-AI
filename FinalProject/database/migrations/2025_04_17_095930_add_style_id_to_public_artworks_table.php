<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('public_artworks', function (Blueprint $table) {
            $table->unsignedBigInteger('style_id')->nullable()->after('user_id');

            $table->foreign('style_id')->references('id')->on('styles')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('public_artworks', function (Blueprint $table) {
            $table->dropForeign(['style_id']);
            $table->dropColumn('style_id');
        });
    }

};
