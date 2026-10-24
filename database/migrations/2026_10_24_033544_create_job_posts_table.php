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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title')->index();
            $table->string('company_name')->index();
            $table->string('location')->index();
            $table->string('job_type')->default('Full-time')->index();
            $table->string('workplace_type')->default('On-site')->index();
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('salary_period')->default('month');
            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('benefits')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('active')->index();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
