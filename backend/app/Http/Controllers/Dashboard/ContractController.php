<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(): View
    {
        return view('dashboard.contracts.index', [
            'navigation' => $this->navigation(),
            'contracts' => Contract::query()->orderBy('number')->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.contracts.form', [
            'navigation' => $this->navigation(),
            'contract' => null,
            'action' => route('dashboard.contracts.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Contract::query()->create($this->validated($request));

        return redirect()->route('dashboard.contracts.index')->with('status', 'Контракт создан.');
    }

    public function edit(Contract $contract): View
    {
        return view('dashboard.contracts.form', [
            'navigation' => $this->navigation(),
            'contract' => $contract,
            'action' => route('dashboard.contracts.update', $contract),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $contract->update($this->validated($request, $contract));

        return redirect()->route('dashboard.contracts.index')->with('status', 'Контракт обновлен.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $contract->delete();

        return redirect()->route('dashboard.contracts.index')->with('status', 'Контракт удален.');
    }

    private function validated(Request $request, ?Contract $contract = null): array
    {
        return $request->validate([
            'number' => ['required', 'string', 'max:255', 'unique:contracts,number,'.($contract?->id ?? 'NULL')],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'payment' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ]);
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
