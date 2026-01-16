<?php

namespace Tests\Unit;

use App\Models\Investment;
use App\Repositories\InvestmentRepositoryInterface;
use App\Services\InvestmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;

class InvestmentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_deve_criar_investimento_com_sucesso(): void
    {
        $repositoryMock = Mockery::mock(InvestmentRepositoryInterface::class);
        $service = new InvestmentService($repositoryMock);

        $data = [
            'client_id' => 1,
            'asset_id' => 1,
            'amount' => 1000.00,
            'investment_date' => now()->format('Y-m-d'),
        ];

        $investment = Mockery::mock(Investment::class);
        $investment->shouldIgnoreMissing();
        $investment->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $investment->shouldReceive('__get')
            ->with('id')
            ->andReturn(1);

        $repositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($investment);

        $result = $service->createInvestment($data);

        $this->assertInstanceOf(Investment::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_deve_lancar_excecao_se_data_for_futura(): void
    {
        $repositoryMock = Mockery::mock(InvestmentRepositoryInterface::class);
        $service = new InvestmentService($repositoryMock);

        $data = [
            'client_id' => 1,
            'asset_id' => 1,
            'amount' => 1000.00,
            'investment_date' => now()->addDay()->format('Y-m-d'),
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A data do investimento não pode ser futura.');

        $service->createInvestment($data);
    }

    public function test_deve_lancar_excecao_se_valor_for_negativo(): void
    {
        $repositoryMock = Mockery::mock(InvestmentRepositoryInterface::class);
        $service = new InvestmentService($repositoryMock);

        $data = [
            'client_id' => 1,
            'asset_id' => 1,
            'amount' => -100.00,
            'investment_date' => now()->format('Y-m-d'),
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('O valor do investimento deve ser no mínimo R$ 1,00.');

        $service->createInvestment($data);
    }

    public function test_deve_retornar_investimentos_do_usuario_paginados(): void
    {
        $repositoryMock = Mockery::mock(InvestmentRepositoryInterface::class);
        $service = new InvestmentService($repositoryMock);

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);

        $repositoryMock
            ->shouldReceive('getByUserId')
            ->once()
            ->with(1, 15, null)
            ->andReturn($paginatorMock);

        $result = $service->getInvestmentsByUser(1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function test_deve_retornar_investimentos_do_usuario_com_per_page_customizado(): void
    {
        $repositoryMock = Mockery::mock(InvestmentRepositoryInterface::class);
        $service = new InvestmentService($repositoryMock);

        $paginatorMock = Mockery::mock(LengthAwarePaginator::class);

        $repositoryMock
            ->shouldReceive('getByUserId')
            ->once()
            ->with(1, 20, null)
            ->andReturn($paginatorMock);

        $result = $service->getInvestmentsByUser(1, 20);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }
}
