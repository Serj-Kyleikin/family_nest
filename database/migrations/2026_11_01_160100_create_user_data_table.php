<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_data', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id')
                ->unique();
            $table->string('status')
                ->nullable();
            $table->string('family_status')
                ->nullable();
            $table->string('avatar', 500)
                ->nullable();
            $table->date('birthday')
                ->nullable();
            $table->string('address', 500)
                ->nullable();
            $table->string('phone', 32)
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_data');
    }
};
