<?php

namespace Tests\Feature;

use App\Models\TimelineEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimelineCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_timeline_event_can_be_created_with_chapters(): void
    {
        $this->asDashboardAdmin();

        $this->post('/dashboard/timeline', [
            'year' => '2039',
            'title' => 'Основание Векселя',
            'text' => 'Название закрепилось в журналах контрактов и радиопозывных.',
            'sort_order' => 3,
            'chapters' => [
                ['chapter' => '01', 'title' => 'От лагеря к лагерю', 'text' => 'Вексель рос не как город, а как сеть.'],
                ['chapter' => '02', 'title' => 'Имя в журнале', 'text' => 'Слово Вексель сначала было пометкой рядом с долгом.'],
            ],
        ])->assertRedirect(route('dashboard.timeline.index'));

        $this->assertDatabaseHas('timeline_events', [
            'year' => '2039',
            'title' => 'Основание Векселя',
            'sort_order' => 3,
        ]);

        $this->assertDatabaseHas('timeline_chapters', [
            'chapter' => '01',
            'title' => 'От лагеря к лагерю',
            'sort_order' => 0,
        ]);
    }

    public function test_timeline_event_can_be_updated_and_chapters_are_synced(): void
    {
        $this->asDashboardAdmin();

        $event = TimelineEvent::query()->create([
            'year' => '2035',
            'title' => 'Старое событие',
            'text' => 'Старый текст.',
            'sort_order' => 0,
        ]);

        $event->chapters()->create([
            'chapter' => '01',
            'title' => 'Старая глава',
            'text' => 'Старый текст главы.',
            'sort_order' => 0,
        ]);

        $this->put("/dashboard/timeline/{$event->id}", [
            'year' => '2040',
            'title' => 'Настоящее время',
            'text' => 'Архив открыт для внутреннего доступа группы.',
            'sort_order' => 5,
            'chapters' => [
                ['chapter' => '01', 'title' => 'Современный Вексель', 'text' => 'Сегодня Вексель держит маршруты.'],
            ],
        ])->assertRedirect(route('dashboard.timeline.index'));

        $this->assertDatabaseHas('timeline_events', [
            'id' => $event->id,
            'year' => '2040',
            'title' => 'Настоящее время',
            'sort_order' => 5,
        ]);

        $this->assertDatabaseMissing('timeline_chapters', [
            'title' => 'Старая глава',
        ]);

        $this->assertDatabaseHas('timeline_chapters', [
            'timeline_event_id' => $event->id,
            'title' => 'Современный Вексель',
        ]);
    }

    public function test_timeline_event_can_be_deleted_with_chapters(): void
    {
        $this->asDashboardAdmin();

        $event = TimelineEvent::query()->create([
            'year' => '2037',
            'title' => 'Формирование Совета',
            'text' => 'Логистика, медицина и разведка получили отдельных ответственных.',
            'sort_order' => 2,
        ]);

        $chapter = $event->chapters()->create([
            'chapter' => '01',
            'title' => 'Старики',
            'text' => 'Стариками стали не по возрасту, а по памяти.',
            'sort_order' => 0,
        ]);

        $this->delete("/dashboard/timeline/{$event->id}")
            ->assertRedirect(route('dashboard.timeline.index'));

        $this->assertDatabaseMissing('timeline_events', ['id' => $event->id]);
        $this->assertDatabaseMissing('timeline_chapters', ['id' => $chapter->id]);
    }

    public function test_timeline_pages_render(): void
    {
        $this->asDashboardAdmin();

        TimelineEvent::query()->create([
            'year' => '2035',
            'title' => 'Падение Лос-Сантоса',
            'text' => 'Город раскололся на сектора.',
            'sort_order' => 0,
        ]);

        $this->get('/dashboard/timeline')
            ->assertOk()
            ->assertSee('Хронология')
            ->assertSee('Создать событие')
            ->assertSee('Падение Лос-Сантоса');

        $this->get('/dashboard/timeline/create')
            ->assertOk()
            ->assertSee('Создать событие')
            ->assertSee('Главы');
    }
}
