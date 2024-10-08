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
        Schema::create('lock_ip_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->integer('count');
            $table->timestamps();
            $table->index(['ip']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lock_ip_feedback');
    }
};
