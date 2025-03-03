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
        Schema::create('crons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias', 192)->nullable();
            $table->string('command', 192)->nullable();
            $table->string('timetable', 32)->nullable();
            $table->text('comment')->nullable();
            $table->boolean('dayweek')->nullable();
            $table->string('last_status', 16)->nullable();
            $table->boolean('disabled')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crons');
    }
};
