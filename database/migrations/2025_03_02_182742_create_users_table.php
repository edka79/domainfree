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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('api_token', 80)->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
            $table->integer('group')->default(1);
            $table->integer('status')->default(1);
            $table->string('dogovor_num')->nullable();
            $table->string('dogovor_name')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('inn')->nullable();
            $table->string('kpp')->nullable();
            $table->integer('credit_limit')->nullable()->default(0);
            $table->string('notice')->nullable();
            $table->integer('inuser')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
