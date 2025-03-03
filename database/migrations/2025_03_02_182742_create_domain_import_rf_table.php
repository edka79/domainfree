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
        Schema::create('domain_import_rf', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain', 65)->unique('domain');
            $table->string('domain_short', 70)->index('domain_short');
            $table->unsignedTinyInteger('zone_id');
            $table->unsignedInteger('registrator_id');
            $table->date('date_create');
            $table->date('date_paid');
            $table->date('date_free');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_import_rf');
    }
};
