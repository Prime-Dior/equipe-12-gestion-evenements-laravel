<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title', 160);
            $table->text('description');
            $table->dateTime('date')->index();
            $table->string('location', 180);
            $table->unsignedInteger('capacity');
            $table->timestamps();

            $table->index(['category_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
