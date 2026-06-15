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
        Schema::create('chats_discussions', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id');
            $table->unsignedBigInteger('user_id');
            $table->string('text')
                ->nullable();
            $table->tinyInteger('is_read')
                ->nullable()
                ->comment('Is message read or not');
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['chat_id', 'user_id'],
                'chats_discussions_chat_user_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats_discussions');
    }
};
