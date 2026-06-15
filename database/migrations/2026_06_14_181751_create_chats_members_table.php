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
        Schema::create('chats_members', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id');
            $table->unsignedBigInteger('user_id');

            $table->index(
                ['chat_id', 'user_id'],
                'chats_members_chat_user_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats_members');
    }
};
