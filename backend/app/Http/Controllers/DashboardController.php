<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\MapPoint;
use App\Models\Member;
use App\Models\TimelineEvent;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'stats' => [
                ['label' => 'Участники', 'value' => Member::query()->count(), 'hint' => 'записей'],
                ['label' => 'Контракты', 'value' => Contract::query()->count(), 'hint' => 'записей'],
                ['label' => 'Точки карты', 'value' => MapPoint::query()->count(), 'hint' => 'записей'],
                ['label' => 'События', 'value' => TimelineEvent::query()->count(), 'hint' => 'записей'],
            ],
            'navigation' => $this->navigation(),
        ]);
    }

    public function page(string $section): View
    {
        $navigation = collect($this->navigation());
        $current = $navigation->firstWhere('key', $section);

        abort_if($current === null, 404);

        return view('dashboard-section', [
            'current' => $current,
            'navigation' => $navigation->all(),
        ]);
    }

    private function navigation(): array
    {
        return [
            ['key' => 'dashboard', 'label' => 'Панель', 'href' => route('dashboard'), 'description' => 'Обзор backend'],
            ['key' => 'members', 'label' => 'Участники', 'href' => route('dashboard.page', 'members'), 'description' => 'CRUD для участников'],
            ['key' => 'contracts', 'label' => 'Контракты', 'href' => route('dashboard.page', 'contracts'), 'description' => 'CRUD для контрактов'],
            ['key' => 'vehicles', 'label' => 'Транспорт', 'href' => route('dashboard.page', 'vehicles'), 'description' => 'CRUD для транспорта'],
            ['key' => 'map-points', 'label' => 'Точки карты', 'href' => route('dashboard.page', 'map-points'), 'description' => 'CRUD для зон'],
            ['key' => 'timeline', 'label' => 'Хронология', 'href' => route('dashboard.timeline.index'), 'description' => 'CRUD для событий'],
            ['key' => 'settings', 'label' => 'Настройки', 'href' => route('dashboard.page', 'settings'), 'description' => 'Конфигурация backend'],
        ];
    }
}
