<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('logged_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', [
                'pending',
                'picked_up',
                'cold_chain_validated',
                'in_transit',
                'lab_arrived',
                'delivered',
                'exception',
                'cancelled',
            ]);
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            // Ambient temperature reading at time of log (°C)
            $table->decimal('temperature_reading', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_status_logs');
    }
};
