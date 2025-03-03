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
        Schema::create('domain_dir_registrators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->nullable()->unique('name');
            $table->date('dateload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_dir_registrators');
    }
};
