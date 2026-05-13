<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TimelineEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TimelineController extends Controller
{
    public function index(): View
    {
        return view('dashboard.timeline.index', [
            'navigation' => $this->navigation(),
            'events' => TimelineEvent::query()
                ->with('chapters')
                ->orderBy('sort_order')
                ->orderBy('year')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.timeline.form', [
            'navigation' => $this->navigation(),
            'event' => null,
            'chapters' => $this->blankChapters(),
            'action' => route('dashboard.timeline.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $event = TimelineEvent::query()->create([
            'year' => $data['year'],
            'title' => $data['title'],
            'text' => $data['text'],
            'sort_order' => $data['sort_order'] ?? TimelineEvent::query()->max('sort_order') + 1,
        ]);

        $this->syncChapters($event, $data['chapters'] ?? []);

        return redirect()
            ->route('dashboard.timeline.index')
            ->with('status', 'Событие хронологии создано.');
    }

    public function edit(TimelineEvent $timelineEvent): View
    {
        $timelineEvent->load('chapters');

        return view('dashboard.timeline.form', [
            'navigation' => $this->navigation(),
            'event' => $timelineEvent,
            'chapters' => $this->chaptersForForm($timelineEvent),
            'action' => route('dashboard.timeline.update', $timelineEvent),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, TimelineEvent $timelineEvent): RedirectResponse
    {
        $data = $this->validated($request);

        $timelineEvent->update([
            'year' => $data['year'],
            'title' => $data['title'],
            'text' => $data['text'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->syncChapters($timelineEvent, $data['chapters'] ?? []);

        return redirect()
            ->route('dashboard.timeline.index')
            ->with('status', 'Событие хронологии обновлено.');
    }

    public function destroy(TimelineEvent $timelineEvent): RedirectResponse
    {
        $timelineEvent->delete();

        return redirect()
            ->route('dashboard.timeline.index')
            ->with('status', 'Событие хронологии удалено.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'chapters' => ['nullable', 'array'],
            'chapters.*.chapter' => ['nullable', 'string', 'max:20'],
            'chapters.*.title' => ['nullable', 'string', 'max:255'],
            'chapters.*.text' => ['nullable', 'string'],
        ]);

        $data['chapters'] = collect($data['chapters'] ?? [])
            ->filter(fn (array $chapter): bool => filled($chapter['chapter'] ?? null)
                || filled($chapter['title'] ?? null)
                || filled($chapter['text'] ?? null))
            ->values()
            ->all();

        foreach ($data['chapters'] as $index => $chapter) {
            if (! filled($chapter['chapter'] ?? null) || ! filled($chapter['title'] ?? null) || ! filled($chapter['text'] ?? null)) {
                throw ValidationException::withMessages([
                    "chapters.$index" => 'Заполните номер, заголовок и текст главы.',
                ]);
            }
        }

        return $data;
    }

    private function syncChapters(TimelineEvent $event, array $chapters): void
    {
        $event->chapters()->delete();

        foreach ($chapters as $index => $chapter) {
            $event->chapters()->create([
                'chapter' => $chapter['chapter'],
                'title' => $chapter['title'],
                'text' => $chapter['text'],
                'sort_order' => $index,
            ]);
        }
    }

    private function chaptersForForm(TimelineEvent $event): array
    {
        return [
            ...$event->chapters->map(fn ($chapter): array => [
                'chapter' => $chapter->chapter,
                'title' => $chapter->title,
                'text' => $chapter->text,
            ])->all(),
            ...$this->blankChapters(2),
        ];
    }

    private function blankChapters(int $count = 5): array
    {
        return array_fill(0, $count, ['chapter' => '', 'title' => '', 'text' => '']);
    }

    private function navigation(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Панель', 'href' => route('dashboard'), 'description' => 'Обзор backend'],
            ['key' => 'positions', 'label' => 'Должности', 'href' => route('dashboard.positions.index'), 'description' => 'CRUD для должностей'],
            ['key' => 'members', 'label' => 'Участники', 'href' => route('dashboard.members.index'), 'description' => 'CRUD для участников'],
            ['key' => 'contracts', 'label' => 'Контракты', 'href' => route('dashboard.contracts.index'), 'description' => 'CRUD для контрактов'],
            ['key' => 'vehicles', 'label' => 'Транспорт', 'href' => route('dashboard.page', 'vehicles'), 'description' => 'CRUD для транспорта'],
            ['key' => 'map-points', 'label' => 'Точки карты', 'href' => route('dashboard.page', 'map-points'), 'description' => 'CRUD для зон'],
            ['key' => 'timeline', 'label' => 'Хронология', 'href' => route('dashboard.timeline.index'), 'description' => 'CRUD для событий'],
            ['key' => 'settings', 'label' => 'Настройки', 'href' => route('dashboard.page', 'settings'), 'description' => 'Конфигурация backend'],
        ];
    }
}
