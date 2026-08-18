<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->restrictOnDelete();
            $table->string('route_name');
            $table->date('scheduled_date');
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['driver_id', 'scheduled_date']);
        });

        // Pivot: which shipments belong to which route (ordered by stop sequence)
        Schema::create('courier_route_shipment', function (Blueprint $table) {
            $table->foreignId('courier_route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('stop_order')->default(0);
            $table->primary(['courier_route_id', 'shipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_route_shipment');
        Schema::dropIfExists('courier_routes');
    }
};
