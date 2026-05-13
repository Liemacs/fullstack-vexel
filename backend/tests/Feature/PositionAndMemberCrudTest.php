<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PositionAndMemberCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_position_can_be_created_updated_and_deleted(): void
    {
        $this->post('/dashboard/positions', [
            'name' => 'Лидер',
            'sort_order' => 1,
            'image' => UploadedFile::fake()->image('leader.png'),
        ])->assertRedirect(route('dashboard.positions.index'));

        $position = Position::query()->firstOrFail();

        $this->assertSame('Лидер', $position->name);
        $this->assertStringStartsWith('/uploads/positions/', $position->image);

        $this->put("/dashboard/positions/{$position->id}", [
            'name' => 'Совет',
            'sort_order' => 2,
        ])->assertRedirect(route('dashboard.positions.index'));

        $this->assertDatabaseHas('positions', [
            'id' => $position->id,
            'name' => 'Совет',
            'sort_order' => 2,
        ]);

        $this->delete("/dashboard/positions/{$position->id}")
            ->assertRedirect(route('dashboard.positions.index'));

        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }

    public function test_member_can_be_created_updated_and_deleted_with_position(): void
    {
        $position = Position::query()->create([
            'name' => 'Механик',
            'image' => '/images/position.webp',
            'sort_order' => 1,
        ]);

        $this->post('/dashboard/members', $this->memberPayload([
            'position_id' => $position->id,
            'image' => UploadedFile::fake()->image('member.jpg'),
        ]))->assertRedirect(route('dashboard.members.index'));

        $member = Member::query()->with('position')->firstOrFail();

        $this->assertSame('Механик', $member->role);
        $this->assertSame('tyler-reed', $member->slug);
        $this->assertSame('tyler', $member->nickname);
        $this->assertTrue(Hash::check('secret123', $member->password));
        $this->assertSame($position->id, $member->position_id);
        $this->assertSame([['name' => 'Ремонт', 'value' => 80]], $member->skills);
        $this->assertStringStartsWith('/uploads/members/', $member->image);

        $this->put("/dashboard/members/{$member->id}", $this->memberPayload([
            'nickname' => 'ivan',
            'password' => 'newsecret',
            'name' => 'Иван',
            'position_id' => $position->id,
            'skills_text' => "Механика|90\nВыживание|70",
        ]))->assertRedirect(route('dashboard.members.index'));

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'slug' => 'ivan',
            'nickname' => 'ivan',
            'name' => 'Иван',
            'role' => 'Механик',
        ]);

        $this->assertTrue(Hash::check('newsecret', $member->fresh()->password));

        $this->delete("/dashboard/members/{$member->id}")
            ->assertRedirect(route('dashboard.members.index'));

        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    public function test_member_api_returns_position_data(): void
    {
        $position = Position::query()->create(['name' => 'Охотник', 'image' => '/rank.webp']);
        Member::query()->create([
            ...$this->memberModelPayload(),
            'position_id' => $position->id,
            'role' => 'Охотник',
        ]);

        $this->getJson('/api/v1/members')
            ->assertOk()
            ->assertJsonPath('0.nickname', 'hunter')
            ->assertJsonPath('0.role', 'Охотник')
            ->assertJsonPath('0.position.name', 'Охотник')
            ->assertJsonPath('0.position.image', '/rank.webp');
    }

    private function memberPayload(array $overrides = []): array
    {
        return [
            'nickname' => 'tyler',
            'password' => 'secret123',
            'name' => 'Tyler Reed',
            'call_sign' => 'Reed T.',
            'age' => 34,
            'status' => 'Активен',
            'role' => 'Механик',
            'position_id' => null,
            'specialization' => 'Ремонт техники',
            'quote' => 'Считает Вексель своей семьей.',
            'bio' => 'Биография участника.',
            'character' => 'Надежный.',
            'vexel_history' => 'История в группе.',
            'skills_text' => 'Ремонт|80',
            'gear_text' => "ключ\nрация",
            'connections_text' => "Совет\nМеханики",
            ...$overrides,
        ];
    }

    private function memberModelPayload(): array
    {
        return [
            'slug' => 'hunter',
            'nickname' => 'hunter',
            'password' => Hash::make('secret123'),
            'name' => 'Hunter',
            'call_sign' => 'H',
            'age' => 30,
            'status' => 'Активен',
            'specialization' => 'Разведка',
            'image' => '/member.webp',
            'quote' => 'Quote',
            'bio' => 'Bio',
            'skills' => [],
            'gear' => [],
            'connections' => [],
        ];
    }
}
