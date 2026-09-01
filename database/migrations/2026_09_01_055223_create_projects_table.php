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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description');
            $table->string('industry')->nullable();
            $table->string('location')->nullable();
 
            // Listing status only — draft/published/closed/archived.
            // Progress statuses (in-progress/delayed/completed) belong
            // to the active-partnership/milestone-tracking module, NOT
            // this table, per leader's feedback.
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])
                  ->default('draft');
 
            // References teammates' tables — adjust table names below
            // if their migrations name them differently.
            $table->foreignId('organisation_id')
                  ->constrained('organisations')
                  ->onDelete('cascade');
 
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade');
 
            $table->timestamps();
            $table->softDeletes(); // matches SoftDeletes trait on Project model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
