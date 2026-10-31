<?php

use App\Services\Family\Enums\FamilyGroupLinkRequestStatusEnum;
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
        Schema::create('family_group_link_requests', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('link_id');
            $table->unsignedBigInteger('user_id');

            $table->enum('status', array_column(FamilyGroupLinkRequestStatusEnum::cases(), 'value'))
                ->default(FamilyGroupLinkRequestStatusEnum::PENDING->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_group_link_requests');
    }
};
