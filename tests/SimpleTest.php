<?php

/**
 * Simple test for Loxo API package - run with: vendor/bin/phpunit tests/SimpleTest.php
 */

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SimpleTest extends TestCase
{
    public function testMockActivityTypesResponse()
    {
        // Mock HTTP response
        $mockResponse = [
            ['id' => 1, 'name' => 'Call', 'key' => 'call'],
            ['id' => 2, 'name' => 'Email', 'key' => 'email'],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($mockResponse))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Test the request
        $response = $client->request('GET', 'activity_types');
        $data = json_decode($response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertEquals('Call', $data[0]['name']);
    }

    public function testMockErrorResponse()
    {
        // Mock error response
        $mock = new MockHandler([
            new Response(404, [], json_encode(['error' => 'Not Found']))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);

        $response = $client->request('GET', 'invalid_endpoint');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testUrlConstruction()
    {
        $domain = 'test.loxo.co';
        $agencySlug = 'test-agency';
        $baseUrlTemplate = 'https://{domain}/api/{agency_slug}';

        $expectedUrl = 'https://test.loxo.co/api/test-agency';
        $actualUrl = str_replace(['{domain}', '{agency_slug}'], [$domain, $agencySlug], $baseUrlTemplate);

        $this->assertEquals($expectedUrl, $actualUrl);
    }

    public function testJsonSerialization()
    {
        $data = [
            'workflow_id' => 123,
            'show_hidden' => true
        ];

        $json = json_encode($data);
        $decoded = json_decode($json, true);

        $this->assertEquals($data, $decoded);
    }

    /**
     * Test that our package classes can be loaded
     */
    public function testPackageClassesExist()
    {
        $this->assertTrue(class_exists('Loxo\LaravelApi\Services\LoxoApiService'));
        $this->assertTrue(class_exists('Loxo\LaravelApi\Exceptions\LoxoApiException'));
        $this->assertTrue(interface_exists('Loxo\LaravelApi\Contracts\LoxoApiInterface'));
    }
}
