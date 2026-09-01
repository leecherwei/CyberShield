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
        Schema::create('partnership_requests', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->onDelete('cascade');
 
            // The organisation expressing interest — NOT the project owner.
            $table->foreignId('organisation_id')
                  ->constrained('organisations')
                  ->onDelete('cascade');
 
            $table->text('message');
 
            $table->enum('status', ['pending', 'accepted', 'rejected'])
                  ->default('pending');
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_requests');
    }
};
