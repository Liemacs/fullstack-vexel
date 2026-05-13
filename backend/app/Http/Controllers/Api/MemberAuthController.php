<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberAccessToken;
use App\Models\Position;
use App\Support\MemberPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'nickname' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $member = Member::query()
            ->with('position')
            ->where('nickname', $credentials['nickname'])
            ->first();

        if (! $member || ! Hash::check($credentials['password'], $member->password ?? '')) {
            return response()->json([
                'message' => 'Неверный никнейм или пароль.',
            ], 422);
        }

        $token = Str::random(80);

        $member->accessTokens()->create([
            'token_hash' => hash('sha256', $token),
            'last_used_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'member' => MemberPayload::format($member),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $token = $this->tokenFromRequest($request);

        if (! $token) {
            return $this->unauthorized();
        }

        $token->update(['last_used_at' => now()]);
        $member = $token->member()->with('position')->firstOrFail();

        return response()->json([
            'member' => MemberPayload::format($member),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $token = $this->tokenFromRequest($request);

        if (! $token) {
            return $this->unauthorized();
        }

        $member = $token->member()->firstOrFail();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'call_sign' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:0', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'quote' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
            'character' => ['nullable', 'string'],
            'vexel_history' => ['nullable', 'string'],
            'skills_text' => ['nullable', 'string'],
            'gear_text' => ['nullable', 'string'],
            'connections_text' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name'], $member);
        $data['skills'] = $this->lines($data['skills_text'] ?? '', true);
        $data['gear'] = $this->lines($data['gear_text'] ?? '');
        $data['connections'] = $this->lines($data['connections_text'] ?? '');
        $data['role'] = Position::query()->find($data['position_id'] ?? null)?->name;

        $image = $this->storeImage($request);
        if ($image !== null) {
            $data['image'] = $image;
        }

        unset($data['skills_text'], $data['gear_text'], $data['connections_text']);

        $member->update($data);
        $token->update(['last_used_at' => now()]);
        $member->refresh()->load('position');

        return response()->json([
            'member' => MemberPayload::format($member),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $this->tokenFromRequest($request);

        if ($token) {
            $token->delete();
        }

        return response()->json(['message' => 'Вы вышли из кабинета.']);
    }

    private function tokenFromRequest(Request $request): ?MemberAccessToken
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return null;
        }

        return MemberAccessToken::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();
    }

    private function unauthorized(): JsonResponse
    {
        return response()->json([
            'message' => 'Требуется вход участника.',
        ], 401);
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
                $percent = max(0, min(100, (int) trim($value)));

                return ['name' => trim($name), 'value' => $percent];
            })
            ->values()
            ->all();
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        if (! is_dir(public_path('uploads/members'))) {
            mkdir(public_path('uploads/members'), 0755, true);
        }
        $file->move(public_path('uploads/members'), $filename);

        return "/uploads/members/{$filename}";
    }
}
