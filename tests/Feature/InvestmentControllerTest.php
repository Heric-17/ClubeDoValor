<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Client;
use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_nao_autenticado_nao_pode_criar_aporte(): void
    {
        $response = $this->post('/investments', [
            'client_id' => 1,
            'asset_id' => 1,
            'amount' => 1000.00,
            'investment_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect('/login');
    }

    public function test_usuario_autenticado_cria_aporte_com_sucesso(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/investments', [
                'client_id' => $client->id,
                'asset_id' => $asset->id,
                'amount' => 1000.00,
                'investment_date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('investments', [
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
        ]);
    }

    public function test_usuario_nao_pode_criar_aporte_para_cliente_de_outro_consultor(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user2->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();

        $response = $this
            ->actingAs($user1)
            ->post('/investments', [
                'client_id' => $client->id,
                'asset_id' => $asset->id,
                'amount' => 1000.00,
                'investment_date' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors('client_id');
        $this->assertDatabaseMissing('investments', [
            'client_id' => $client->id,
        ]);
    }
}
