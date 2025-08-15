<?php

namespace Loxo\LaravelApi\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Loxo\LaravelApi\Exceptions\ConfigurationException;
use Loxo\LaravelApi\Exceptions\LoxoApiException;
use Loxo\LaravelApi\Services\LoxoApiService;
use PHPUnit\Framework\TestCase;

class LoxoApiServiceTest extends TestCase
{
    private function mockConfig(): void
    {
        // Mock the Config facade for testing
        if (!class_exists('Illuminate\Support\Facades\Config')) {
            // Create a simple config mock
            $GLOBALS['test_config'] = [
                'loxo.domain' => 'test.loxo.co',
                'loxo.agency_slug' => 'test-agency',
                'loxo.api_key' => 'test-api-key',
                'loxo.timeout' => 30,
                'loxo.retry_attempts' => 1,
                'loxo.retry_delay' => 100,
                'loxo.base_url' => 'https://{domain}/api/{agency_slug}',
            ];
        }
    }

    public function test_it_throws_configuration_exception_when_domain_is_missing()
    {
        Config::set('loxo.domain', null);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Loxo domain is not configured');

        new LoxoApiService();
    }

    public function test_it_throws_configuration_exception_when_agency_slug_is_missing()
    {
        Config::set('loxo.agency_slug', null);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Loxo agency slug is not configured');

        new LoxoApiService();
    }

    public function test_it_throws_configuration_exception_when_api_key_is_missing()
    {
        Config::set('loxo.api_key', null);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Loxo API key is not configured');

        new LoxoApiService();
    }

    public function test_it_can_get_activity_types()
    {
        $mockResponse = [
            'activity_types' => [
                ['id' => 1, 'name' => 'Call'],
                ['id' => 2, 'name' => 'Email'],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getActivityTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_activity_types_with_params()
    {
        $mockResponse = ['activity_types' => []];
        $params = ['workflow_id' => 123, 'show_hidden' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getActivityTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_throws_exception_on_api_error()
    {
        $this->expectException(LoxoApiException::class);

        $service = $this->createServiceWithMockResponse(500, ['error' => 'Server Error']);
        $service->getActivityTypes();
    }

    protected function createServiceWithMockResponse(int $statusCode, array $responseData): LoxoApiService
    {
        $mock = new MockHandler([
            new Response($statusCode, [], json_encode($responseData))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new LoxoApiService();

        // Use reflection to inject the mock client
        $reflection = new \ReflectionClass($service);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $client);

        return $service;
    }
}
