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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32);
            $table->string('title');
            $table->string('anchor', 64)->unique();
            $table->string('nav_label', 64)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('position')->index();
            $table->json('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
