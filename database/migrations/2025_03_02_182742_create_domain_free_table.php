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
        Schema::create('domain_free', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('domain', 70);
            $table->string('word', 192);
            $table->string('translate', 192)->nullable();
            $table->string('zone', 8);
            $table->string('word_type', 24);
            $table->unsignedTinyInteger('litera_count')->default(0);
            $table->string('litera_attr', 16);

            $table->index(['id', 'domain', 'word', 'translate', 'zone', 'word_type', 'litera_count', 'litera_attr'], 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_free');
    }
};
