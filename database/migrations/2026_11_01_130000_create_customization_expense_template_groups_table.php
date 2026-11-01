<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_expense_template_groups', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('expense_template_group_id');
            $table->unsignedSmallInteger('priority');

            $table->unique(['user_id', 'expense_template_group_id'], 'customization_etg_user_group_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_expense_template_groups');
    }
};
