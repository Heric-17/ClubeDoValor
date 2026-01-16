<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Client;
use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInvestmentsRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_acessar_investimentos_atraves_do_relacionamento(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();

        /** @var Investment $investment1 */
        $investment1 = Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
        ]);

        /** @var Investment $investment2 */
        $investment2 = Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 2000.00,
        ]);

        $investments = $user->investments;

        $this->assertCount(2, $investments);
        $this->assertTrue($investments->contains($investment1));
        $this->assertTrue($investments->contains($investment2));
    }

    public function test_relacionamento_nao_inclui_investimentos_de_outros_usuarios(): void
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
        ]);

        /** @var Investment $investment2 */
        Investment::factory()->create([
            'client_id' => $client2->id,
            'asset_id' => $asset->id,
        ]);

        $investments = $user1->investments;

        $this->assertCount(1, $investments);
        $this->assertEquals($client1->id, $investments->first()->client_id);
    }

    public function test_relacionamento_inclui_relacionamentos_client_e_asset(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create(['symbol' => 'PETR4']);

        /** @var Investment $investment */
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
        ]);

        $investment = $user->investments()->with(['client', 'asset'])->first();

        $this->assertNotNull($investment);
        $this->assertNotNull($investment->client);
        $this->assertEquals($client->id, $investment->client->id);
        $this->assertNotNull($investment->asset);
        $this->assertEquals($asset->id, $investment->asset->id);
        $this->assertEquals('PETR4', $investment->asset->symbol);
    }

    public function test_relacionamento_respeita_soft_deletes_do_cliente(): void
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
        ]);

        // Soft delete do cliente
        $client->delete();

        $investments = $user->investments;

        $this->assertCount(0, $investments);
    }

    public function test_relacionamento_pode_ser_usado_com_where_clauses(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();

        /** @var Investment $investment1 */
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
            'investment_date' => now(),
        ]);

        /** @var Investment $investment2 */
        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 2000.00,
            'investment_date' => now()->subMonth(),
        ]);

        $currentMonthInvestments = $user->investments()
            ->whereYear('investment_date', now()->year)
            ->whereMonth('investment_date', now()->month)
            ->get();

        $this->assertCount(1, $currentMonthInvestments);
        $this->assertEquals(1000.00, $currentMonthInvestments->first()->amount);
    }

    public function test_relacionamento_pode_somar_valores(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        $client = Client::factory()->create(['user_id' => $user->id]);
        /** @var Asset $asset */
        $asset = Asset::factory()->create();

        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 1000.00,
        ]);

        Investment::factory()->create([
            'client_id' => $client->id,
            'asset_id' => $asset->id,
            'amount' => 2000.00,
        ]);

        $total = $user->investments()->sum('amount');

        $this->assertEquals(3000.00, $total);
    }
}
