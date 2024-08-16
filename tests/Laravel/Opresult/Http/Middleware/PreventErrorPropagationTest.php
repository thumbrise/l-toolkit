<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Opresult\Http\Middleware;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Thumbrise\Toolkit\Opresult\Error;
use Thumbrise\Toolkit\Opresult\Http\Middleware\PreventErrorPropagation;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\TestCase;

/**
 * @internal
 */
class PreventErrorPropagationTest extends TestCase
{
    public const ENV_FOR_PREVENTING = 'testing';

    protected function tearDown(): void
    {
        parent::tearDown();
        Error::disableSensitiveDetails(false);
    }

    /**
     * @test
     */
    public function clearingErrorKeys()
    {
        if (! $this->app->environment(self::ENV_FOR_PREVENTING)) {
            $this->markTestSkipped('Окружение должно быть '.self::ENV_FOR_PREVENTING);
        }

        Route::middleware(PreventErrorPropagation::class.':'.self::ENV_FOR_PREVENTING)
            ->get('/api/test', function () {
                return OperationResult::error('deep error', 'deep_error')
                    ->withError('client error', 'client_error')
                ;
            })
        ;

        $response = $this->get('/api/test');

        $response->assertOk();
        $response->assertJsonMissingPath('error_previous');
        $response->assertJsonMissingPath('error_previous');
        $response->assertJsonMissingPath('errorPrevious');
        $response->assertJsonMissingPath('errorContext');
    }

    #[Test]
    public function notMakeEmptyResponseWhenNotOperationResult()
    {
        if (! $this->app->environment(self::ENV_FOR_PREVENTING)) {
            $this->markTestSkipped('Окружение должно быть '.self::ENV_FOR_PREVENTING);
        }
        $expected = ['someNotOperationResultKey' => 'someNotOperationResultValue'];

        Route::middleware(PreventErrorPropagation::class.':'.self::ENV_FOR_PREVENTING)
            ->get('/api/test', function () use ($expected) {
                return $expected;
            })
        ;

        $response = $this->get('/api/test');

        $response->assertOk();
        $response->assertJson($expected);
    }
}
