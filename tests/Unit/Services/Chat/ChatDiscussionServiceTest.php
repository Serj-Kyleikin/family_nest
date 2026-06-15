<?php

namespace Tests\Unit\Services\Chat;

use App\Models\{
    Chat\Chat,
    Chat\ChatDiscussion,
    Family\FamilyGroup,
    User,
};
use App\Services\Chat\ChatDiscussionService;
use Illuminate\Support\Facades\Auth;
use Exception;
use Tests\TestCase;

class ChatDiscussionServiceTest extends TestCase
{
    public function testPositiveCreate(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        Auth::login($user);
        $this->app
            ->make(ChatDiscussionService::class)
            ->create($chat->id, 'Hello');

        $this->assertDatabaseHas('chats_discussions', [
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'text'    => 'Hello',
        ]);
    }

    public function testNegativeCreateNotMember(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);
        $outsider   = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        Auth::login($outsider);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("This user isn't member of this chat");

        $this->app
            ->make(ChatDiscussionService::class)
            ->create($chat->id, 'Hello');
    }

    public function testPositiveUpdate(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $discussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'text'    => 'Old text',
        ]);

        Auth::login($user);
        $this->app
            ->make(ChatDiscussionService::class)
            ->update($chat->id, $discussion->id, 'New text');

        $this->assertDatabaseHas('chats_discussions', [
            'id'   => $discussion->id,
            'text' => 'New text',
        ]);
    }

    public function testNegativeUpdateNotFound(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $discussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $otherUser->id,
            'text'    => 'Other user message',
        ]);

        Auth::login($user);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Chat message is not found');

        $this->app
            ->make(ChatDiscussionService::class)
            ->update($chat->id, $discussion->id, 'New text');
    }

    public function testPositiveDelete(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $discussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'text'    => 'Delete me',
        ]);

        Auth::login($user);
        $this->app
            ->make(ChatDiscussionService::class)
            ->delete($chat->id, $discussion->id);

        $this->assertSoftDeleted('chats_discussions', [
            'id' => $discussion->id,
        ]);
    }

    public function testNegativeDeleteNotFound(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $discussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $otherUser->id,
            'text'    => 'Other user message',
        ]);

        Auth::login($user);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Chat message is not found');

        $this->app
            ->make(ChatDiscussionService::class)
            ->delete($chat->id, $discussion->id);
    }

    public function testPositiveSearchByText(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat               = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $matchingDiscussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'text'    => 'Hello world',
        ]);

        ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $otherUser->id,
            'text'    => 'Goodbye',
        ]);

        Auth::login($user);
        $results = $this->app
            ->make(ChatDiscussionService::class)
            ->searchByText($chat->id, 'world');

        $this->assertCount(1, $results);
        $this->assertEquals($matchingDiscussion->id, $results->first()->id);
        $this->assertEquals($user->name, $results->first()->user->name);
    }

    public function testNegativeSearchByTextNotMember(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);
        $outsider   = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        Auth::login($outsider);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("This user isn't member of this chat");

        $this->app
            ->make(ChatDiscussionService::class)
            ->searchByText($chat->id, 'world');
    }

    public function testPositiveSetIsRead(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $discussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $otherUser->id,
            'text'    => 'Unread message',
            'is_read' => 0,
        ]);

        Auth::login($user);
        $this->app
            ->make(ChatDiscussionService::class)
            ->setIsRead($chat->id, [$discussion->id]);

        $this->assertDatabaseHas('chats_discussions', [
            'id'      => $discussion->id,
            'is_read' => 1,
        ]);
    }

    public function testNegativeSetIsReadWrongDiscussions(): void
    {
        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);
        $otherUser  = User::factory()->create(['group_id' => $group->id]);

        $chat       = Chat::factory()
            ->withMembers($user, $otherUser)
            ->create();

        $discussion = ChatDiscussion::factory()->create([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
            'text'    => 'Own message',
        ]);

        Auth::login($user);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Can't update is_read status for this messages");

        $this->app
            ->make(ChatDiscussionService::class)
            ->setIsRead($chat->id, [$discussion->id]);
    }
}
