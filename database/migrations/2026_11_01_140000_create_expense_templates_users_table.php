<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_templates_user_groups', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('expense_template_id');
            $table->string('name');
            $table->decimal('price', 12, 2)
                ->nullable();
            $table->boolean('is_active');

            $table->unique(['group_id', 'expense_template_id'], 'expense_templates_user_groups_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_templates_user_groups');
    }
};
