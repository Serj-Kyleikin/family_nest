<?php

namespace Tests\Unit\Services\User;

use App\Models\{
    User,
    UserData,
};
use App\Services\User\UserDataService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserDataServiceTest extends TestCase
{
    public function test_positive_get_empty_data(): void
    {
        $service        = $this->app->make(UserDataService::class);

        $user           = User::factory()->create();

        Auth::login($user);

        $result = $service->get();

        $this->assertEquals($user->id, $result->user_id);
        $this->assertNull($result->status);
        $this->assertNull($result->family_status);
        $this->assertNull($result->avatar);
        $this->assertNull($result->birthday);
        $this->assertNull($result->address);
        $this->assertNull($result->phone);
        $this->assertDatabaseCount('user_data', 0);
    }

    public function test_positive_update_creates_data(): void
    {
        $service        = $this->app->make(UserDataService::class);

        $user           = User::factory()->create();
        $data           = [
            'status'        => 'At the dacha',
            'family_status' => 'Mother',
            'birthday'      => '1990-05-17',
            'phone'         => '+7 900 123-45-67',
        ];

        Auth::login($user);

        $result = $service->update($data);

        $this->assertEquals('At the dacha', $result->status);
        $this->assertEquals('Mother', $result->family_status);
        $this->assertEquals('1990-05-17', $result->birthday->toDateString());
        $this->assertNull($result->address);
        $this->assertDatabaseHas('user_data', [
            'user_id'       => $user->id,
            'status'        => 'At the dacha',
            'family_status' => 'Mother',
        ]);
    }

    public function test_positive_update_changes_only_passed_fields(): void
    {
        $service        = $this->app->make(UserDataService::class);

        $user           = User::factory()->create();
        UserData::factory()->create([
            'user_id'       => $user->id,
            'status'        => 'Old status',
            'family_status' => 'Father',
            'address'       => 'Moscow',
        ]);

        Auth::login($user);

        $result = $service->update(['status' => 'New status', 'address' => null]);

        $this->assertEquals('New status', $result->status);
        $this->assertEquals('Father', $result->family_status);
        $this->assertNull($result->address);
        $this->assertDatabaseCount('user_data', 1);
    }

    public function test_negative_update_foreign_data(): void
    {
        $service        = $this->app->make(UserDataService::class);

        $user           = User::factory()->create();
        $foreignUser    = User::factory()->create();
        $foreignData    = UserData::factory()->create([
            'user_id'   => $foreignUser->id,
            'status'    => 'Foreign status',
        ]);

        Auth::login($user);

        $service->update(['status' => 'Changed']);

        $this->assertDatabaseHas('user_data', [
            'id'        => $foreignData->id,
            'status'    => 'Foreign status',
        ]);
    }
}
