<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_nao_autenticado_nao_pode_acessar_lista_de_clientes(): void
    {
        $response = $this->get('/clients');

        $response->assertRedirect('/login');
    }

    public function test_usuario_autenticado_pode_ver_lista_de_clientes(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Client $client */
        Client::factory()->create([
            'user_id' => $user->id,
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);

        $response = $this->actingAs($user)->get('/clients');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Clients/Index')
            ->has('clients', 1)
            ->where('clients.0.name', 'João Silva')
            ->where('clients.0.email', 'joao@example.com')
        );
    }

    public function test_clientes_sao_ordenados_alfabeticamente(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        
        /** @var Client $client1 */
        Client::factory()->create([
            'user_id' => $user->id,
            'name' => 'Zeca Silva',
            'email' => 'zeca@example.com',
        ]);
        
        /** @var Client $client2 */
        Client::factory()->create([
            'user_id' => $user->id,
            'name' => 'Ana Costa',
            'email' => 'ana@example.com',
        ]);
        
        /** @var Client $client3 */
        Client::factory()->create([
            'user_id' => $user->id,
            'name' => 'Bruno Santos',
            'email' => 'bruno@example.com',
        ]);

        $response = $this->actingAs($user)->get('/clients');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Clients/Index')
            ->has('clients', 3)
            ->where('clients.0.name', 'Ana Costa')
            ->where('clients.1.name', 'Bruno Santos')
            ->where('clients.2.name', 'Zeca Silva')
        );
    }

    public function test_usuario_so_ve_seus_proprios_clientes(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        
        /** @var Client $client1 */
        Client::factory()->create([
            'user_id' => $user1->id,
            'name' => 'Cliente User 1',
            'email' => 'cliente1@example.com',
        ]);
        
        /** @var Client $client2 */
        Client::factory()->create([
            'user_id' => $user2->id,
            'name' => 'Cliente User 2',
            'email' => 'cliente2@example.com',
        ]);

        $response = $this->actingAs($user1)->get('/clients');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Clients/Index')
            ->has('clients', 1)
            ->where('clients.0.name', 'Cliente User 1')
        );
    }

    public function test_usuario_nao_autenticado_nao_pode_criar_cliente(): void
    {
        $response = $this->post('/clients', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_usuario_autenticado_cria_cliente_com_sucesso(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/clients', [
                'name' => 'João Silva',
                'email' => 'joao@example.com',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Cliente criado com sucesso!');

        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    public function test_cliente_e_associado_ao_usuario_logado(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();

        $response = $this
            ->actingAs($user1)
            ->post('/clients', [
                'name' => 'Cliente Teste',
                'email' => 'cliente@example.com',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'user_id' => $user1->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
        ]);

        $this->assertDatabaseMissing('clients', [
            'user_id' => $user2->id,
            'name' => 'Cliente Teste',
        ]);
    }

    public function test_validacao_de_nome_obrigatorio(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/clients', [
                'email' => 'joao@example.com',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('clients', [
            'email' => 'joao@example.com',
        ]);
    }

    public function test_validacao_de_email_obrigatorio(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/clients', [
                'name' => 'João Silva',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('clients', [
            'name' => 'João Silva',
        ]);
    }

    public function test_validacao_de_email_invalido(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/clients', [
                'name' => 'João Silva',
                'email' => 'email-invalido',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('clients', [
            'name' => 'João Silva',
        ]);
    }

    public function test_validacao_de_nome_max_255(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/clients', [
                'name' => str_repeat('a', 256),
                'email' => 'joao@example.com',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('clients', [
            'email' => 'joao@example.com',
        ]);
    }

    public function test_validacao_de_email_max_255(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/clients', [
                'name' => 'João Silva',
                'email' => str_repeat('a', 250) . '@example.com',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('clients', [
            'name' => 'João Silva',
        ]);
    }

    public function test_lista_vazia_quando_usuario_nao_tem_clientes(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clients');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Clients/Index')
            ->has('clients', 0)
        );
    }
}
