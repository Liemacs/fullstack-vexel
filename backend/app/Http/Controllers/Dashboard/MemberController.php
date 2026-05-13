<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        return view('dashboard.members.index', [
            'navigation' => $this->navigation(),
            'members' => Member::query()->with('position')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.members.form', [
            'navigation' => $this->navigation(),
            'member' => null,
            'positions' => Position::query()->orderBy('sort_order')->orderBy('name')->get(),
            'action' => route('dashboard.members.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['image'] = $this->storeImage($request, 'members');
        $data['skills'] = $this->lines($data['skills_text'] ?? '', true);
        $data['gear'] = $this->lines($data['gear_text'] ?? '');
        $data['connections'] = $this->lines($data['connections_text'] ?? '');
        $data['role'] = Position::query()->find($data['position_id'])?->name ?? ($data['role'] ?? '');
        $data['password'] = Hash::make($data['password']);

        unset($data['skills_text'], $data['gear_text'], $data['connections_text']);

        Member::query()->create($data);

        return redirect()->route('dashboard.members.index')->with('status', 'Участник создан.');
    }

    public function edit(Member $member): View
    {
        return view('dashboard.members.form', [
            'navigation' => $this->navigation(),
            'member' => $member,
            'positions' => Position::query()->orderBy('sort_order')->orderBy('name')->get(),
            'action' => route('dashboard.members.update', $member),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validated($request, $member);
        $data['slug'] = $this->uniqueSlug($data['name'], $member);
        $image = $this->storeImage($request, 'members');

        if ($image !== null) {
            $data['image'] = $image;
        }

        $data['skills'] = $this->lines($data['skills_text'] ?? '', true);
        $data['gear'] = $this->lines($data['gear_text'] ?? '');
        $data['connections'] = $this->lines($data['connections_text'] ?? '');
        $data['role'] = Position::query()->find($data['position_id'])?->name ?? ($data['role'] ?? '');
        if (filled($data['password'] ?? null)) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['skills_text'], $data['gear_text'], $data['connections_text']);

        $member->update($data);

        return redirect()->route('dashboard.members.index')->with('status', 'Участник обновлен.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('dashboard.members.index')->with('status', 'Участник удален.');
    }

    private function validated(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'nickname' => ['required', 'string', 'max:255', 'unique:members,nickname,'.($member?->id ?? 'NULL')],
            'password' => [$member ? 'nullable' : 'required', 'string', 'min:6'],
            'name' => ['required', 'string', 'max:255'],
            'call_sign' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'quote' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'character' => ['nullable', 'string'],
            'vexel_history' => ['nullable', 'string'],
            'skills_text' => ['nullable', 'string'],
            'gear_text' => ['nullable', 'string'],
            'connections_text' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function uniqueSlug(string $name, ?Member $member = null): string
    {
        $base = Str::slug($name) ?: 'member';
        $slug = $base;
        $counter = 2;

        while (
            Member::query()
                ->where('slug', $slug)
                ->when($member, fn ($query) => $query->whereKeyNot($member->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function lines(string $value, bool $skills = false): array
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $value);

        return collect(explode("\n", $normalized))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->map(function (string $line) use ($skills): array|string {
                if (! $skills) {
                    return $line;
                }

                [$name, $value] = array_pad(explode('|', $line, 2), 2, '0');

                return ['name' => trim($name), 'value' => (int) trim($value)];
            })
            ->values()
            ->all();
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
            ['key' => 'contracts', 'label' => 'Контракты', 'href' => route('dashboard.page', 'contracts'), 'description' => 'CRUD для контрактов'],
            ['key' => 'vehicles', 'label' => 'Транспорт', 'href' => route('dashboard.page', 'vehicles'), 'description' => 'CRUD для транспорта'],
            ['key' => 'map-points', 'label' => 'Точки карты', 'href' => route('dashboard.page', 'map-points'), 'description' => 'CRUD для зон'],
            ['key' => 'timeline', 'label' => 'Хронология', 'href' => route('dashboard.timeline.index'), 'description' => 'CRUD для событий'],
            ['key' => 'settings', 'label' => 'Настройки', 'href' => route('dashboard.page', 'settings'), 'description' => 'Конфигурация backend'],
        ];
    }
}
