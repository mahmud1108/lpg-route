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
        Schema::create('admin_reset_password_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id');
            $table->foreign('admin_id')->on('admins')->references('admin_id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('email', 50)->unique();
            $table->string('token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_reset_password_tokens');
    }
};
