<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Member;
use App\Models\MemberAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ContractCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_can_be_created_updated_and_deleted(): void
    {
        $this->asDashboardAdmin();

        $this->get('/dashboard/contracts')->assertOk();
        $this->get('/dashboard/contracts/create')->assertOk();

        $this->post('/dashboard/contracts', $this->payload())
            ->assertRedirect(route('dashboard.contracts.index'));

        $contract = Contract::query()->firstOrFail();

        $this->assertSame('#019', $contract->number);
        $this->assertSame('Охрана каравана', $contract->title);
        $this->get("/dashboard/contracts/{$contract->id}/edit")->assertOk();

        $this->put("/dashboard/contracts/{$contract->id}", $this->payload([
            'number' => '#020',
            'title' => 'Вывод группы',
            'status' => 'Выполнено',
        ]))->assertRedirect(route('dashboard.contracts.index'));

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'number' => '#020',
            'title' => 'Вывод группы',
            'status' => 'Выполнено',
        ]);

        $this->delete("/dashboard/contracts/{$contract->id}")
            ->assertRedirect(route('dashboard.contracts.index'));

        $this->assertDatabaseMissing('contracts', ['id' => $contract->id]);
    }

    public function test_contract_api_returns_database_contracts(): void
    {
        Contract::query()->create($this->payload());

        $this->getJson('/api/v1/contracts', [
            'Authorization' => 'Bearer '.$this->memberToken(),
        ])
            ->assertOk()
            ->assertJsonPath('0.number', '#019')
            ->assertJsonPath('0.title', 'Охрана каравана')
            ->assertJsonPath('0.status', 'Активно');
    }

    public function test_contract_api_requires_member_login(): void
    {
        $this->getJson('/api/v1/contracts')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Требуется вход участника.');
    }

    private function payload(array $overrides = []): array
    {
        return [
            'number' => '#019',
            'title' => 'Охрана каравана',
            'status' => 'Активно',
            'payment' => 'топливо / 80 л',
            'client' => 'Северный рынок',
            'text' => 'Три грузовика и ночной переход через сухое русло.',
            ...$overrides,
        ];
    }

    private function memberToken(): string
    {
        $plainToken = 'test-member-token';
        $member = Member::query()->create([
            'slug' => 'member',
            'nickname' => 'member',
            'password' => Hash::make('secret123'),
            'name' => 'Member',
            'skills' => [],
            'gear' => [],
            'connections' => [],
        ]);

        MemberAccessToken::query()->create([
            'member_id' => $member->id,
            'token_hash' => hash('sha256', $plainToken),
        ]);

        return $plainToken;
    }
}
