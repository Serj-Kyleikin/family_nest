<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diary_entries', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('text');
            $table->dateTime('date');
            $table->dateTime('date_end')
                ->nullable();

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_entries');
    }
};
