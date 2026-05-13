<?php

namespace Tests\Feature;

use App\Models\Contract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_can_be_created_updated_and_deleted(): void
    {
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

        $this->getJson('/api/v1/contracts')
            ->assertOk()
            ->assertJsonPath('0.number', '#019')
            ->assertJsonPath('0.title', 'Охрана каравана')
            ->assertJsonPath('0.status', 'Активно');
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
}
