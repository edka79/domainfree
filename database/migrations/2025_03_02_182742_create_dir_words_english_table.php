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
        Schema::create('dir_words_english', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word', 192)->index('word');
            $table->string('translate', 192)->index('translate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dir_words_english');
    }
};
