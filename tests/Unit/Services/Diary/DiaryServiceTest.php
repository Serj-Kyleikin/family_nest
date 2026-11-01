<?php

namespace Tests\Unit\Services\Diary;

use App\Exceptions\HandledException;
use App\Models\{
    Diary\DiaryEntry,
    Diary\DiaryPermission,
    Family\FamilyGroup,
    User,
};
use App\Services\Diary\DiaryService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DiaryServiceTest extends TestCase
{
    public function test_positive_create_future_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $date       = Carbon::today()->addDays(3)->setTime(14, 30);

        Auth::login($user);

        $entry = $service->create('Записаться к врачу', $date);

        $this->assertSame($user->id, $entry->user_id);
        $this->assertSame('14:30', $entry->date->format('H:i'));
        $this->assertDatabaseHas('diary_entries', [
            'user_id'   => $user->id,
            'text'      => 'Записаться к врачу',
            'date'      => $date->toDateTimeString(),
        ]);
    }

    public function test_positive_create_entry_with_end(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $date       = Carbon::today()->addDays(2)->setTime(14, 30);
        $dateEnd    = Carbon::today()->addDays(2)->setTime(15, 45);

        Auth::login($user);

        $entry = $service->create('Приём у врача', $date, $dateEnd);

        $this->assertSame('15:45', $entry->date_end->format('H:i'));
        $this->assertDatabaseHas('diary_entries', [
            'user_id'   => $user->id,
            'date'      => $date->toDateTimeString(),
            'date_end'  => $dateEnd->toDateTimeString(),
        ]);
    }

    public function test_positive_create_entry_without_end(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $entry = $service->create('Без окончания', Carbon::today()->addDay()->setTime(10, 0));

        $this->assertNull($entry->date_end);
    }

    public function test_negative_create_entry_with_end_before_start(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $date       = Carbon::today()->addDay()->setTime(14, 30);
        $dateEnd    = Carbon::today()->addDay()->setTime(14, 0);

        Auth::login($user);

        try {
            $service->create('Неверный интервал', $date, $dateEnd);
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('diary_entries', 0);
    }

    public function test_negative_create_entry_with_end_equal_to_start_message(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $date       = Carbon::today()->addDay()->setTime(14, 30);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Время окончания должно быть позже начала');

        $service->create('Нулевой интервал', $date, $date->copy());
    }

    public function test_positive_create_today_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $service->create('Сегодня', Carbon::today()->setTime(9, 0));

        $this->assertDatabaseCount('diary_entries', 1);
    }

    public function test_negative_create_past_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        try {
            $service->create('Вчера', Carbon::yesterday()->setTime(9, 0));
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('diary_entries', 0);
    }

    public function test_negative_create_past_entry_message(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Дата записи не может быть в прошлом');

        $service->create('Вчера', Carbon::yesterday()->setTime(9, 0));
    }

    public function test_positive_update_own_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $entry      = DiaryEntry::factory()->create(['user_id' => $user->id]);
        $date       = Carbon::today()->addDays(4)->setTime(11, 15);
        $dateEnd    = Carbon::today()->addDays(4)->setTime(12, 0);

        Auth::login($user);

        $updated = $service->update($entry->id, 'Новый текст', $date, $dateEnd);

        $this->assertSame($entry->id, $updated->id);
        $this->assertSame('Новый текст', $updated->text);
        $this->assertDatabaseHas('diary_entries', [
            'id'        => $entry->id,
            'text'      => 'Новый текст',
            'date'      => $date->toDateTimeString(),
            'date_end'  => $dateEnd->toDateTimeString(),
        ]);
    }

    public function test_positive_update_clears_end(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $date       = Carbon::today()->addDays(2)->setTime(9, 0);
        $entry      = DiaryEntry::factory()->create([
            'user_id'   => $user->id,
            'date'      => $date->toDateTimeString(),
            'date_end'  => $date->copy()->setTime(10, 0)->toDateTimeString(),
        ]);

        Auth::login($user);

        $updated = $service->update($entry->id, 'Без конца', $date);

        $this->assertNull($updated->date_end);
    }

    public function test_positive_update_past_entry_keeping_date(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $date       = Carbon::yesterday()->setTime(18, 0);
        $entry      = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => $date->toDateTimeString()]);

        Auth::login($user);

        $updated = $service->update($entry->id, 'Поправил текст', $date);

        $this->assertSame('Поправил текст', $updated->text);
    }

    public function test_negative_update_moving_entry_to_past(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $entry      = DiaryEntry::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Дата записи не может быть в прошлом');

        $service->update($entry->id, 'В прошлое', Carbon::yesterday()->setTime(9, 0));
    }

    public function test_negative_update_end_before_start(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $entry      = DiaryEntry::factory()->create(['user_id' => $user->id, 'text' => 'Исходный текст']);
        $date       = Carbon::today()->addDays(3)->setTime(14, 30);

        Auth::login($user);

        try {
            $service->update($entry->id, 'Новый текст', $date, $date->copy()->setTime(14, 0));
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('diary_entries', ['id' => $entry->id, 'text' => 'Исходный текст']);
    }

    public function test_negative_update_foreign_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $foreign    = DiaryEntry::factory()->create(['text' => 'Чужая запись']);

        Auth::login($user);

        try {
            $service->update($foreign->id, 'Взлом', Carbon::today()->addDay()->setTime(9, 0));
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('diary_entries', ['id' => $foreign->id, 'text' => 'Чужая запись']);
    }

    public function test_negative_update_foreign_entry_message(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $foreign    = DiaryEntry::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Запись не найдена');

        $service->update($foreign->id, 'Взлом', Carbon::today()->addDay()->setTime(9, 0));
    }

    public function test_positive_delete_own_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $entry      = DiaryEntry::factory()->create(['user_id' => $user->id]);
        $keptEntry  = DiaryEntry::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $service->delete($entry->id);

        $this->assertDatabaseMissing('diary_entries', ['id' => $entry->id]);
        $this->assertDatabaseHas('diary_entries', ['id' => $keptEntry->id]);
    }

    public function test_negative_delete_foreign_entry(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $foreign    = DiaryEntry::factory()->create();

        Auth::login($user);

        try {
            $service->delete($foreign->id);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('diary_entries', ['id' => $foreign->id]);
    }

    public function test_negative_delete_foreign_entry_message(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();
        $foreign    = DiaryEntry::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Запись не найдена');

        $service->delete($foreign->id);
    }

    public function test_positive_list_own_entries_ordered_by_date(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $user           = User::factory()->create();
        $laterEntry     = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-10']);
        $earlierEntry   = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-01']);

        Auth::login($user);

        $items = $service->list();

        $this->assertSame([$earlierEntry->id, $laterEntry->id], $items->pluck('id')->all());
    }

    public function test_positive_list_same_day_ordered_by_time(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $user           = User::factory()->create();
        $laterEntry     = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-10 18:00:00']);
        $earlierEntry   = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-10 08:15:00']);

        Auth::login($user);

        $items = $service->list();

        $this->assertSame([$earlierEntry->id, $laterEntry->id], $items->pluck('id')->all());
    }

    public function test_positive_list_includes_entry_on_date_to_with_time(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $user           = User::factory()->create();
        $lastDayEntry   = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-08 18:45:00']);

        Auth::login($user);

        $items = $service->list([], Carbon::parse('2026-12-02'), Carbon::parse('2026-12-08'));

        $this->assertSame([$lastDayEntry->id], $items->pluck('id')->all());
    }

    public function test_positive_list_includes_permitted_member_entries(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $owner          = User::factory()->create(['group_id' => $group->id]);
        $ownerEntry     = DiaryEntry::factory()->create(['user_id' => $owner->id]);

        DiaryPermission::factory()->create(['owner_id' => $owner->id, 'viewer_id' => $user->id]);

        Auth::login($user);

        $items = $service->list();

        $this->assertTrue($items->contains('id', $ownerEntry->id));
        $this->assertSame($owner->name, $items->firstWhere('id', $ownerEntry->id)->user_name);
    }

    public function test_negative_list_hides_member_entries_without_permission(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $closedMember   = User::factory()->create(['group_id' => $group->id]);
        $closedEntry    = DiaryEntry::factory()->create(['user_id' => $closedMember->id]);

        Auth::login($user);

        $items = $service->list();

        $this->assertFalse($items->contains('id', $closedEntry->id));
    }

    public function test_negative_list_hides_reverse_permission(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);
        $memberEntry    = DiaryEntry::factory()->create(['user_id' => $member->id]);

        DiaryPermission::factory()->create(['owner_id' => $user->id, 'viewer_id' => $member->id]);

        Auth::login($user);

        $items = $service->list();

        $this->assertFalse($items->contains('id', $memberEntry->id));
    }

    public function test_negative_list_hides_permitted_owner_from_other_group(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $group          = FamilyGroup::factory()->create();
        $foreignGroup   = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $foreignOwner   = User::factory()->create(['group_id' => $foreignGroup->id]);
        $foreignEntry   = DiaryEntry::factory()->create(['user_id' => $foreignOwner->id]);

        DiaryPermission::factory()->create(['owner_id' => $foreignOwner->id, 'viewer_id' => $user->id]);

        Auth::login($user);

        $items = $service->list();

        $this->assertFalse($items->contains('id', $foreignEntry->id));
    }

    public function test_positive_list_filters_by_date_range(): void
    {
        $service        = $this->app->make(DiaryService::class);

        $user           = User::factory()->create();
        $beforeEntry    = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-01']);
        $insideEntry    = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-05']);
        $afterEntry     = DiaryEntry::factory()->create(['user_id' => $user->id, 'date' => '2026-12-09']);

        Auth::login($user);

        $items = $service->list([], Carbon::parse('2026-12-02'), Carbon::parse('2026-12-08'));

        $this->assertSame([$insideEntry->id], $items->pluck('id')->all());
    }

    public function test_negative_list_date_from_after_date_to(): void
    {
        $service    = $this->app->make(DiaryService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);

        $service->list([], Carbon::parse('2026-12-08'), Carbon::parse('2026-12-02'));
    }
}
