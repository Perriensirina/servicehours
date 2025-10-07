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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('shipment')->nullable();
            $table->string('box_number')->nullable();
            $table->string('ul')->nullable();
            $table->string('supplier')->nullable();
            $table->string('AT_number')->nullable();
            $table->string('zone');
            $table->string('reason');
            $table->timestamps();
            $table->integer('createdUserID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
