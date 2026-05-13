<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(): View
    {
        return view('dashboard.positions.index', [
            'navigation' => $this->navigation(),
            'positions' => Position::query()
                ->withCount('members')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.positions.form', [
            'navigation' => $this->navigation(),
            'position' => null,
            'action' => route('dashboard.positions.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->storeImage($request, 'positions');

        Position::query()->create($data);

        return redirect()->route('dashboard.positions.index')->with('status', 'Должность создана.');
    }

    public function edit(Position $position): View
    {
        return view('dashboard.positions.form', [
            'navigation' => $this->navigation(),
            'position' => $position,
            'action' => route('dashboard.positions.update', $position),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $data = $this->validated($request, $position);
        $image = $this->storeImage($request, 'positions');

        if ($image !== null) {
            $data['image'] = $image;
        }

        $position->update($data);

        return redirect()->route('dashboard.positions.index')->with('status', 'Должность обновлена.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->route('dashboard.positions.index')->with('status', 'Должность удалена.');
    }

    private function validated(Request $request, ?Position $position = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions,name,'.($position?->id ?? 'NULL')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function storeImage(Request $request, string $folder): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        if (! is_dir(public_path("uploads/{$folder}"))) {
            mkdir(public_path("uploads/{$folder}"), 0755, true);
        }
        $file->move(public_path("uploads/{$folder}"), $filename);

        return "/uploads/{$folder}/{$filename}";
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
