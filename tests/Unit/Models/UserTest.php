<?php

namespace Tests\Unit\Models;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    User,
};
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_positive_group_loaded_by_default(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);

        $foundUser  = User::query()->find($user->id);

        $this->assertTrue($foundUser->relationLoaded('group'));
        $this->assertEquals($group->id, $foundUser->group->id);
    }

    public function test_positive_group_loaded_as_null_without_group(): void
    {
        $user       = User::factory()->create(['group_id' => null]);

        $foundUser  = User::query()->find($user->id);

        $this->assertTrue($foundUser->relationLoaded('group'));
        $this->assertNull($foundUser->group);
    }

    public function test_positive_group_loaded_for_token_user(): void
    {
        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        $plainTextToken = $user->createToken('test')->plainTextToken;

        $tokenUser      = PersonalAccessToken::findToken($plainTextToken)->tokenable;

        $this->assertTrue($tokenUser->relationLoaded('group'));
        $this->assertEquals($group->id, $tokenUser->group->id);
    }

    public function test_positive_get_group(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);

        $foundUser  = User::query()->find($user->id);

        $result     = $foundUser->getGroup();

        $this->assertEquals($group->id, $result->id);
    }

    public function test_negative_get_group_user_without_group(): void
    {
        $user       = User::factory()->create(['group_id' => null]);

        $foundUser  = User::query()->find($user->id);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $foundUser->getGroup();
    }

    public function test_negative_get_group_not_existing_group(): void
    {
        $notExistingGroupId = 999999;

        $user               = User::factory()->create(['group_id' => $notExistingGroupId]);

        $foundUser          = User::query()->find($user->id);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $foundUser->getGroup();
    }
}
