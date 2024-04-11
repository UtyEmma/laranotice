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
        Schema::create('mailables', function (Blueprint $table) {
            $table->id();
            $table->string('mailable')->unique();
            $table->longText('content')->nullable();
            $table->mediumText('subject')->nullable();
            $table->integer('sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailables');
    }
};
