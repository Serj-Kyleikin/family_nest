<?php

namespace Tests\Unit\Services\Chat;

use App\Models\{
    Chat\Chat,
    Chat\ChatDiscussion,
    Family\FamilyGroup,
    User,
};
use App\Services\Chat\ChatService;
use Illuminate\Support\Facades\Auth;
use Exception;
use Tests\TestCase;

class ChatServiceTest extends TestCase
{
    public function testIndexPositive(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);
        $someUser   = User::factory()->create(['group_id' => $group->id]);

        $olderChat          = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $olderDiscussion    = ChatDiscussion::factory()->create([
            'chat_id' => $olderChat->id,
            'user_id' => $user->id,
            'text'    => 'Older message',
        ]);

        $newerChat          = Chat::factory()
            ->withMembers($user, $someUser)
            ->create();

        $newerDiscussion    = ChatDiscussion::factory()->create([
            'chat_id' => $newerChat->id,
            'user_id' => $user->id,
            'text'    => 'Newer message',
        ]);

        Auth::login($user);
        $chats = $this->app->make(ChatService::class)->index();

        $this->assertCount(2, $chats);
        $this->assertEquals($olderChat->id, $chats->first()->id);
        $this->assertEquals($chats->first()->member->id, $otherUser->id);
        $this->assertEquals($chats->first()->lastDiscussion->id, $olderDiscussion->id);
        $this->assertEquals($newerChat->id, $chats->last()->id);
        $this->assertEquals($chats->last()->member->id, $someUser->id);
        $this->assertEquals($chats->last()->lastDiscussion->id, $newerDiscussion->id);
    }

    public function testPositiveIndexExcludesChatsWithoutDiscussions(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        Auth::login($user);
        $chats = $this->app->make(ChatService::class)->index();

        $this->assertCount(0, $chats);
    }

    public function testPositiveCreate(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);
        $chat = $this->app->make(ChatService::class)->create($otherUser->id);

        $this->assertDatabaseCount('chats', 1);
        $this->assertDatabaseHas('chats_members', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('chats_members', [
            'chat_id' => $chat->id,
            'user_id' => $otherUser->id,
        ]);
        $this->assertCount(2, $chat->members);
    }

    public function testPositiveCreateReturnsExistingChat(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $existingChat = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        Auth::login($user);
        $chat = $this->app->make(ChatService::class)->create($otherUser->id);

        $this->assertEquals($existingChat->id, $chat->id);
        $this->assertDatabaseCount('chats', 1);
    }

    public function testNegativeCreateSentToSelf(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Chat can't be created with yourself");

        $this->app->make(ChatService::class)->create($user->id);
    }

    public function testPositiveGetChatWithDiscussionsAndMembers(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'text'    => 'Hello',
        ]);

        Auth::login($user);
        $result = $this->app
            ->make(ChatService::class)
            ->getChatWithDiscussionsAndMembers($chat->id);

        $this->assertEquals($chat->id, $result->id);
        $this->assertCount(1, $result->discussions);
        $this->assertCount(2, $result->members);
        $this->assertEqualsCanonicalizing(
            [$user->id, $otherUser->id],
            $result->members->pluck('id')->toArray()
        );
    }

    public function testNegativeGetChatWithDiscussionsAndMembersNotMember(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);
        $outsider   = User::factory()->create(['group_id' => $group->id]);

        $chat = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        Auth::login($outsider);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("This user isn't member of this chat");

        $this->app
            ->make(ChatService::class)
            ->getChatWithDiscussionsAndMembers($chat->id);
    }
}
