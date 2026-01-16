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

    public function test_usuario_nao_autenticado_nao_pode_acessar_index(): void
    {
        $response = $this->get('/investments');

        $response->assertRedirect('/login');
    }

    public function test_usuario_autenticado_pode_acessar_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();
        /** @var Investment $investment */
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->has('investments')
            ->has('clients')
            ->has('assets')
            ->has('stats')
        );
    }

    public function test_index_mostra_apenas_investimentos_do_usuario_logado(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        
        /** @var Client $client1 */
        $client1 = Client::factory()->create(['user_id' => $user1->id]);
        /** @var Client $client2 */
        $client2 = Client::factory()->create(['user_id' => $user2->id]);
        
        /** @var Asset $asset */
        $asset = Asset::factory()->create();
        
        /** @var Investment $investment1 */
        Investment::factory()->create([
            'client_id' => $client1->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);
        
        /** @var Investment $investment2 */
        Investment::factory()->create([
            'client_id' => $client2->id,
            'asset_id' => $asset->id,
            'amount' => 2000.00,
            'investment_date' => now(),
        ]);

        $response = $this->actingAs($user1)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->has('investments.data', 1)
            ->where('investments.data.0.amount', '1000.00')
        );
    }

    public function test_index_calcula_stats_corretamente(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset1 */
        $asset1 = Asset::factory()->create(['symbol' => 'PETR4']);
        /** @var Asset $asset2 */
        $asset2 = Asset::factory()->create(['symbol' => 'VALE3']);
        
        // Investimentos do mês atual
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset1->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);
        
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset1->id,
            'amount' => 2000.00,
            'investment_date' => now(),
        ]);
        
        // Investimento em outro ativo (menor valor total)
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset2->id,
            'amount' => 500.00,
            'investment_date' => now(),
        ]);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->where('stats.total_current_month', 3500)
            ->where('stats.top_asset', 'PETR4')
        );
    }

    public function test_index_mostra_stats_vazias_quando_nao_ha_investimentos(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Client::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->where('stats.total_current_month', 0)
            ->where('stats.top_asset', null)
        );
    }

    public function test_index_lista_clientes_do_usuario(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client1 */
        $client1 = Client::factory()->create(['user_id' => $user->id, 'name' => 'Cliente A']);
        /** @var Client $client2 */
        $client2 = Client::factory()->create(['user_id' => $user->id, 'name' => 'Cliente B']);
        
        // Cliente de outro usuário não deve aparecer
        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        Client::factory()->create(['user_id' => $otherUser->id, 'name' => 'Cliente C']);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->has('clients', 2)
            ->where('clients.0.name', 'Cliente A')
            ->where('clients.1.name', 'Cliente B')
        );
    }

    public function test_index_lista_todos_os_ativos(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Asset $asset1 */
        $asset1 = Asset::factory()->create(['symbol' => 'PETR4', 'name' => 'Petrobras']);
        /** @var Asset $asset2 */
        $asset2 = Asset::factory()->create(['symbol' => 'VALE3', 'name' => 'Vale']);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->has('assets', 2)
        );
    }

    public function test_index_calcula_top_asset_corretamente_com_multiplos_ativos(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset1 */
        $asset1 = Asset::factory()->create(['symbol' => 'PETR4']);
        /** @var Asset $asset2 */
        $asset2 = Asset::factory()->create(['symbol' => 'VALE3']);
        
        // VALE3 tem maior valor total (3000 vs 2000)
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset1->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);
        
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset1->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);
        
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset2->id,
            'amount' => 3000.00,
            'investment_date' => now(),
        ]);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->where('stats.top_asset', 'VALE3')
        );
    }

    public function test_index_nao_inclui_investimentos_de_meses_anteriores_no_total(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();
        
        // Investimento do mês atual
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);
        
        // Investimento do mês anterior
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 2000.00,
            'investment_date' => now()->subMonth(),
        ]);

        $response = $this->actingAs($user)->get('/investments');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Investments/Index')
            ->where('stats.total_current_month', 1000)
        );
    }
}
