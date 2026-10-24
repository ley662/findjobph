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
        Schema::create('job_post_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignId('requirement_type_id')->constrained('requirement_types')->cascadeOnDelete();
            $table->boolean('is_mandatory')->default(true);
            $table->string('custom_instruction')->nullable();
            $table->timestamps();

            $table->unique(['job_post_id', 'requirement_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_post_requirements');
    }
};
