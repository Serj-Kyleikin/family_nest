<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_permissions', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('viewer_id');

            $table->unique(['owner_id', 'viewer_id']);
            $table->index(['viewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_permissions');
    }
};
