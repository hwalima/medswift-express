<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 32)->unique();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('origin_address');
            $table->text('destination_address');
            // Temperature handling class for bio-specimens
            $table->enum('temperature_class', ['ambient', 'refrigerated', 'frozen'])->default('ambient');
            $table->enum('priority', ['routine', 'urgent'])->default('routine');
            $table->enum('current_status', [
                'pending',
                'picked_up',
                'cold_chain_validated',
                'in_transit',
                'lab_arrived',
                'delivered',
                'exception',
                'cancelled',
            ])->default('pending');
            $table->boolean('is_biohazard')->default(false);
            $table->text('special_instructions')->nullable();
            $table->string('proof_of_delivery_path')->nullable();
            $table->timestamp('scheduled_pickup_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'current_status']);
            $table->index('courier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
