<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('conditions');
            $table->json('benefits');
            $table->integer('priority')->default(0);
            $table->boolean('active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_rules');
    }
};