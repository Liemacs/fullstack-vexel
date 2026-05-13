<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_login_with_nickname_and_fetch_profile(): void
    {
        $member = Member::query()->create($this->memberPayload());

        $login = $this->postJson('/api/v1/member/login', [
            'nickname' => 'hero',
            'password' => 'secret123',
        ])->assertOk()
            ->assertJsonPath('member.nickname', 'hero')
            ->assertJsonPath('member.slug', 'hero-name')
            ->assertJsonStructure(['token', 'member']);

        $token = $login->json('token');

        $this->getJson('/api/v1/member/me', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()
            ->assertJsonPath('member.name', $member->name);
    }

    public function test_member_login_rejects_wrong_password(): void
    {
        Member::query()->create($this->memberPayload());

        $this->postJson('/api/v1/member/login', [
            'nickname' => 'hero',
            'password' => 'bad-password',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Неверный никнейм или пароль.');
    }

    public function test_member_can_update_own_profile(): void
    {
        $member = Member::query()->create($this->memberPayload());
        $position = Position::query()->create([
            'name' => 'Медик',
            'image' => '/rank.webp',
            'sort_order' => 1,
        ]);

        $token = $this->postJson('/api/v1/member/login', [
            'nickname' => 'hero',
            'password' => 'secret123',
        ])->json('token');

        $this->postJson('/api/v1/member/me', [
            'name' => 'Hero Updated',
            'call_sign' => 'Medic',
            'age' => 28,
            'status' => 'Активен',
            'position_id' => $position->id,
            'quote' => 'Quote',
            'bio' => 'Updated bio',
            'character' => 'Calm',
            'vexel_history' => 'Joined later.',
            'skills_text' => "Медицина|88\nВыживание|72",
            'gear_text' => "аптечка\nрация",
            'connections_text' => 'Совет',
        ], [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()
            ->assertJsonPath('member.slug', 'hero-updated')
            ->assertJsonPath('member.role', 'Медик')
            ->assertJsonPath('member.position.name', 'Медик')
            ->assertJsonPath('member.skills.0.name', 'Медицина')
            ->assertJsonPath('member.skills.0.value', 88);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'slug' => 'hero-updated',
            'role' => 'Медик',
        ]);
    }

    private function memberPayload(): array
    {
        return [
            'slug' => 'hero-name',
            'nickname' => 'hero',
            'password' => Hash::make('secret123'),
            'name' => 'Hero Name',
            'call_sign' => null,
            'age' => null,
            'status' => null,
            'role' => null,
            'specialization' => null,
            'image' => null,
            'quote' => null,
            'bio' => null,
            'skills' => [],
            'gear' => [],
            'connections' => [],
        ];
    }
}
