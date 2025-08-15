<?php

namespace Loxo\LaravelApi\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Loxo\LaravelApi\Exceptions\ConfigurationException;
use Loxo\LaravelApi\Exceptions\LoxoApiException;
use Loxo\LaravelApi\Services\LoxoApiService;
use Orchestra\Testbench\TestCase;
use Loxo\LaravelApi\LoxoApiServiceProvider;

class LoxoApiServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LoxoApiServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set default configuration for testing
        config([
            'loxo.domain' => 'test.loxo.co',
            'loxo.agency_slug' => 'test-agency',
            'loxo.api_key' => 'test-api-key',
            'loxo.timeout' => 30,
            'loxo.retry_attempts' => 1,
            'loxo.retry_delay' => 100,
            'loxo.base_url' => 'https://{domain}/api/{agency_slug}',
        ]);
    }

    public function test_it_throws_configuration_exception_when_domain_is_missing()
    {
        config(['loxo.domain' => null]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Loxo domain is not configured');

        new LoxoApiService();
    }

    public function test_it_throws_configuration_exception_when_agency_slug_is_missing()
    {
        config(['loxo.agency_slug' => null]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Loxo agency slug is not configured');

        new LoxoApiService();
    }

    public function test_it_throws_configuration_exception_when_api_key_is_missing()
    {
        config(['loxo.api_key' => null]);

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

    public function test_it_can_get_jobs()
    {
        $mockResponse = [
            'jobs' => [
                ['id' => 1, 'title' => 'Software Engineer', 'published' => true],
                ['id' => 2, 'title' => 'Product Manager', 'published' => false],
            ],
            'meta' => [
                'total' => 2,
                'per_page' => 10,
                'current_page' => 1
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getJobs();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_jobs_with_params()
    {
        $mockResponse = [
            'jobs' => [
                ['id' => 1, 'title' => 'Remote Developer', 'remote_work_allowed' => true]
            ],
            'meta' => [
                'total' => 1,
                'per_page' => 20,
                'current_page' => 1
            ]
        ];

        $params = [
            'per_page' => 20,
            'page' => 1,
            'query' => 'developer',
            'published' => true,
            'remote_work_allowed' => true,
            'job_category_ids' => [1, 2],
            'owned_by_ids' => [5]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getJobs($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_people()
    {
        $mockResponse = [
            'people' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com'],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com'],
            ],
            'meta' => [
                'total' => 2,
                'per_page' => 10,
                'current_page' => 1
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPeople();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_people_with_params()
    {
        $mockResponse = [
            'people' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com']
            ],
            'meta' => [
                'total' => 1,
                'per_page' => 5,
                'current_page' => 1
            ]
        ];

        $params = [
            'per_page' => 5,
            'query' => 'john',
            'person_global_status_id' => 1,
            'include_related_agencies' => true,
            'created_at_sort' => 'desc'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPeople($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_people_with_scroll()
    {
        $mockResponse = [
            'people' => [
                ['id' => 10, 'first_name' => 'Bob', 'last_name' => 'Johnson', 'email' => 'bob@example.com']
            ],
            'scroll_id' => 'next_page_cursor_123',
            'meta' => [
                'total' => 100,
                'per_page' => 1,
                'has_more' => true
            ]
        ];

        $params = [
            'scroll_id' => 'prev_page_cursor_456',
            'per_page' => 1
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPeople($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_person()
    {
        $mockResponse = [
            'person' => [
                'id' => 123456,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'title' => 'Software Engineer',
                'company' => 'Tech Corp',
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $personData = [
            'person' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'title' => 'Software Engineer',
                'company' => 'Tech Corp',
                'location' => 'San Francisco, CA'
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createPerson($personData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_person_with_full_data()
    {
        $mockResponse = [
            'person' => [
                'id' => 123457,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'title' => 'Product Manager',
                'company' => 'Startup Inc',
                'linkedin_url' => 'https://linkedin.com/in/janesmith',
                'compensation' => 120000.0,
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $personData = [
            'person' => [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'title' => 'Product Manager',
                'company' => 'Startup Inc',
                'location' => 'New York, NY',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'United States',
                'linkedin_url' => 'https://linkedin.com/in/janesmith',
                'compensation' => 120000.0,
                'salary' => 120000.0,
                'person_type_id' => 1,
                'source_type_id' => 2,
                'all_raw_tags' => ['javascript', 'product management'],
                'list_ids' => [1, 2, 3]
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createPerson($personData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_create_person_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'person.email' => ['The email field is required.'],
                'person.name' => ['The name field is required.']
            ],
            'message' => 'Validation failed'
        ];

        $personData = [
            'person' => [
                'title' => 'Developer'
                // Missing required fields
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createPerson($personData);
    }

    public function test_it_can_apply_to_job()
    {
        $mockResponse = [
            'person' => [
                'id' => 123456,
                'name' => 'John Doe',
                'candidates' => [
                    [
                        'id' => 789012,
                        'job' => ['id' => 2645723],
                        'latest_activity_type' => ['name' => 'Applied'],
                        'workflow_stage_id' => 267765
                    ]
                ],
                'resumes' => [
                    ['id' => 456789, 'name' => 'resume.pdf']
                ]
            ]
        ];

        // Create a temporary resume file for testing
        $tempResume = tempnam(sys_get_temp_dir(), 'test_resume');
        file_put_contents($tempResume, 'Test resume content');

        $applicationData = [
            'email' => 'john.doe@example.com',
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'linkedin' => 'https://linkedin.com/in/johndoe',
            'resume' => $tempResume
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->applyToJob(2645723, $applicationData);

        $this->assertEquals($mockResponse, $result);

        // Clean up
        unlink($tempResume);
    }

    public function test_it_can_apply_to_job_with_full_data()
    {
        $mockResponse = [
            'person' => [
                'id' => 123457,
                'name' => 'Jane Smith',
                'candidates' => [
                    [
                        'id' => 789013,
                        'job' => ['id' => 2645723],
                        'latest_activity_type' => ['name' => 'Applied'],
                        'workflow_stage_id' => 267765
                    ]
                ],
                'resumes' => [
                    ['id' => 456790, 'name' => 'jane_resume.pdf']
                ]
            ]
        ];

        // Create a temporary resume file for testing
        $tempResume = tempnam(sys_get_temp_dir(), 'test_resume_full');
        file_put_contents($tempResume, 'Advanced test resume content');

        $applicationData = [
            'email' => 'jane.smith@example.com',
            'name' => 'Jane Smith',
            'phone' => '+1987654321',
            'linkedin' => 'https://linkedin.com/in/janesmith',
            'resume' => $tempResume,
            'work_authorization' => true,
            'requires_visa' => false,
            'gender_ids' => [1],
            'ethnicity_ids' => [2, 3],
            'veteran_status_id' => 1,
            'pronoun_id' => 2,
            'disability_status_id' => 1,
            'source_type_id' => 3
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->applyToJob(2645723, $applicationData);

        $this->assertEquals($mockResponse, $result);

        // Clean up
        unlink($tempResume);
    }

    public function test_it_handles_apply_to_job_validation_error()
    {
        $applicationData = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'phone' => '+1234567890',
            'linkedin' => 'https://linkedin.com/in/incomplete',
            'resume' => 'nonexistent_file.pdf'  // This file doesn't exist
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resume must be a file resource or valid file path');

        $service = $this->createServiceWithMockResponse(400, []);
        $service->applyToJob(2645723, $applicationData);
    }

    public function test_it_handles_apply_to_nonexistent_job()
    {
        $errorResponse = [
            'error' => 'Job not found',
            'message' => 'The specified job does not exist or is not published'
        ];

        // Create a temporary resume file for testing
        $tempResume = tempnam(sys_get_temp_dir(), 'test_resume_404');
        file_put_contents($tempResume, 'Test resume for 404 test');

        $applicationData = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'phone' => '+1234567890',
            'resume' => $tempResume
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        
        try {
            $service->applyToJob(999999, $applicationData);
        } finally {
            // Clean up even if test fails
            unlink($tempResume);
        }
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
