<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


/**
 * BASE scheme only. This table is created here because PartnershipsRequest::accept().
 * Progress-tracking columns belong sto the active-partnership/milestone-tracking module and should be 
 * added via a SEPARATE migration by whoever owns that module - do not add those colums here.
 * 
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->onDelete('cascade');
 
            // The organisation whose PartnershipRequest was accepted.
            $table->foreignId('organisation_id')
                  ->constrained('organisations')
                  ->onDelete('cascade');
 
            $table->timestamp('formed_at')->useCurrent();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnerships');
    }
};
