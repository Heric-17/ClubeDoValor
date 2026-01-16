<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Repositories\ClientRepositoryInterface;
use App\Services\ClientService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClientServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_deve_retornar_clientes_do_usuario(): void
    {
        $repositoryMock = Mockery::mock(ClientRepositoryInterface::class);
        $service = new ClientService($repositoryMock);

        $clientsCollection = Mockery::mock(Collection::class);

        $repositoryMock
            ->shouldReceive('getByUserId')
            ->once()
            ->with(1)
            ->andReturn($clientsCollection);

        $result = $service->getClientsByUser(1);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_deve_criar_cliente_com_sucesso(): void
    {
        $repositoryMock = Mockery::mock(ClientRepositoryInterface::class);
        $service = new ClientService($repositoryMock);

        $data = [
            'user_id' => 1,
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ];

        $client = Mockery::mock(Client::class);
        $client->shouldIgnoreMissing();
        $client->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $client->shouldReceive('__get')
            ->with('id')
            ->andReturn(1);

        $repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($client);

        $result = $service->createClient($data);

        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(1, $result->id);
    }
}
