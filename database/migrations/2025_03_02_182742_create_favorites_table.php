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
        Schema::create('favorites', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('datecreate')->useCurrent();
            $table->string('area', 12)->nullable();
            $table->string('domain', 192);
            $table->integer('user_id')->nullable()->default(0);
            $table->unsignedBigInteger('nobody_id');
            $table->text('data')->nullable();

            $table->index(['id', 'area', 'domain', 'nobody_id'], 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
