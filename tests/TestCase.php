<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Vite;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock Vite para evitar erro de manifest não encontrado nos testes
        Vite::shouldReceive('__invoke')
            ->andReturn('');

        Vite::shouldReceive('preloadedAssets')
            ->andReturn([]);

        Vite::shouldReceive('useCspNonce')
            ->andReturnSelf();

        Vite::shouldReceive('useIntegrityKey')
            ->andReturnSelf();

        Vite::shouldReceive('useScriptTagAttributes')
            ->andReturnSelf();

        Vite::shouldReceive('useStyleTagAttributes')
            ->andReturnSelf();
    }
}
