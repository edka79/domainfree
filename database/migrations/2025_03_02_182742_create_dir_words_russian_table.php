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
        Schema::create('dir_words_russian', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word', 192)->unique('word');
            $table->string('alias_ya', 192)->nullable()->index('alias_ya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dir_words_russian');
    }
};
