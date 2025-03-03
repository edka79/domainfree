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
        Schema::create('domain_import_expired', function (Blueprint $table) {
            $table->string('id', 192)->primary();
            $table->dateTime('dateload')->nullable();
            $table->string('zone', 4)->nullable()->index('zone');
            $table->boolean('stavka')->nullable();
            $table->integer('tic')->default(0);
            $table->integer('iks')->default(0);
            $table->bigInteger('alexa')->default(0);
            $table->unsignedInteger('links')->default(0);
            $table->unsignedBigInteger('sw')->default(0);
            $table->unsignedInteger('li')->default(0);
            $table->unsignedInteger('mydrop')->default(0);
            $table->unsignedTinyInteger('ur')->nullable();
            $table->unsignedTinyInteger('vozrast')->default(0);
            $table->date('date_create')->nullable();
            $table->date('date_free')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_import_expired');
    }
};
