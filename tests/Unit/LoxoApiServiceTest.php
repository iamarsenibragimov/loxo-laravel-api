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

    public function test_it_can_get_address_types()
    {
        $mockResponse = [
            'address_types' => [
                ['id' => 1, 'name' => 'Home'],
                ['id' => 2, 'name' => 'Work'],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getAddressTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_bonus_payment_types()
    {
        $mockResponse = [
            'bonus_payment_types' => [
                ['id' => 1, 'name' => 'Annual'],
                ['id' => 2, 'name' => 'Quarterly'],
                ['id' => 3, 'name' => 'Monthly'],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getBonusPaymentTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_bonus_types()
    {
        $mockResponse = [
            'bonus_types' => [
                ['id' => 1, 'name' => 'Performance Bonus'],
                ['id' => 2, 'name' => 'Signing Bonus'],
                ['id' => 3, 'name' => 'Retention Bonus'],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getBonusTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_companies()
    {
        $mockResponse = [
            'companies' => [
                [
                    'id' => 1,
                    'name' => 'Tech Corp',
                    'url' => 'https://techcorp.com',
                    'description' => 'A leading technology company',
                    'company_type_id' => 1
                ],
                [
                    'id' => 2,
                    'name' => 'StartupXYZ',
                    'url' => 'https://startupxyz.com',
                    'description' => 'Innovative startup',
                    'company_type_id' => 2
                ],
            ],
            'meta' => [
                'total' => 2,
                'has_more' => false
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanies();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_companies_with_params()
    {
        $mockResponse = [
            'companies' => [
                [
                    'id' => 3,
                    'name' => 'Enterprise Inc',
                    'url' => 'https://enterprise.com',
                    'description' => 'Large enterprise company',
                    'company_type_id' => 1
                ]
            ],
            'scroll_id' => 'next_page_cursor_companies_123',
            'meta' => [
                'total' => 50,
                'has_more' => true
            ]
        ];

        $params = [
            'scroll_id' => 'prev_page_cursor_companies_456',
            'query' => 'enterprise',
            'company_type_id' => 1,
            'company_global_status_id' => 1,
            'list_id' => 5
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanies($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_company()
    {
        $mockResponse = [
            'company' => [
                'id' => 12345,
                'name' => 'New Tech Company',
                'url' => 'https://newtechcompany.com',
                'description' => 'A newly created tech company',
                'culture' => 'Innovation-focused culture',
                'company_type_id' => 1,
                'company_global_status_id' => 1,
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $companyData = [
            'company' => [
                'name' => 'New Tech Company',
                'url' => 'https://newtechcompany.com',
                'description' => 'A newly created tech company',
                'culture' => 'Innovation-focused culture',
                'company_type_id' => 1,
                'company_global_status_id' => 1,
                'owner_email' => 'owner@newtechcompany.com'
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createCompany($companyData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_company_with_full_data()
    {
        $mockResponse = [
            'company' => [
                'id' => 12346,
                'name' => 'Full Data Corp',
                'url' => 'https://fulldatacorp.com',
                'description' => 'Company with full data set',
                'culture' => 'Data-driven culture',
                'blocked' => false,
                'fee' => 15.5,
                'fee_type_id' => 1,
                'company_type_id' => 2,
                'company_global_status_id' => 1,
                'emails' => ['contact@fulldatacorp.com', 'info@fulldatacorp.com'],
                'phones' => ['+1234567890', '+0987654321'],
                'addresses' => [
                    ['street' => '123 Tech St', 'city' => 'San Francisco', 'state' => 'CA']
                ],
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $companyData = [
            'company' => [
                'name' => 'Full Data Corp',
                'url' => 'https://fulldatacorp.com',
                'description' => 'Company with full data set',
                'culture' => 'Data-driven culture',
                'blocked' => false,
                'fee' => 15.5,
                'fee_type_id' => 1,
                'company_type_id' => 2,
                'owner_email' => 'owner@fulldatacorp.com',
                'company_global_status_id' => 1,
                'emails' => ['contact@fulldatacorp.com', 'info@fulldatacorp.com'],
                'phones' => ['+1234567890', '+0987654321'],
                'addresses' => [
                    ['street' => '123 Tech St', 'city' => 'San Francisco', 'state' => 'CA']
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createCompany($companyData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_create_company_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'company.name' => ['The name field is required.'],
                'company.company_type_id' => ['The company type id field must be an integer.']
            ],
            'message' => 'Validation failed'
        ];

        $companyData = [
            'company' => [
                'url' => 'https://incomplete.com'
                // Missing required fields
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createCompany($companyData);
    }

    public function test_it_can_get_workflows()
    {
        $mockResponse = [
            'workflows' => [
                [
                    'id' => 1,
                    'name' => 'Hiring Workflow',
                    'description' => 'Standard hiring process workflow',
                    'active' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Client Workflow',
                    'description' => 'Client onboarding workflow',
                    'active' => true
                ],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getWorkflows();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_workflow_stages()
    {
        $mockResponse = [
            'workflow_stages' => [
                [
                    'id' => 1,
                    'name' => 'Application Received',
                    'workflow_id' => 1,
                    'position' => 1,
                    'active' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Phone Screen',
                    'workflow_id' => 1,
                    'position' => 2,
                    'active' => true
                ],
                [
                    'id' => 3,
                    'name' => 'Technical Interview',
                    'workflow_id' => 1,
                    'position' => 3,
                    'active' => true
                ],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getWorkflowStages();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_veteran_statuses()
    {
        $mockResponse = [
            'veteran_statuses' => [
                [
                    'id' => 1,
                    'name' => 'Veteran',
                    'description' => 'Military veteran status'
                ],
                [
                    'id' => 2,
                    'name' => 'Active Duty',
                    'description' => 'Currently serving in military'
                ],
                [
                    'id' => 3,
                    'name' => 'Not Applicable',
                    'description' => 'No military service'
                ],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getVeteranStatuses();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_webhooks()
    {
        $mockResponse = [
            'webhooks' => [
                [
                    'id' => 1,
                    'item_type' => 'candidate',
                    'action' => 'create',
                    'endpoint_url' => 'https://example.com/webhooks/candidate-created',
                    'active' => true,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'item_type' => 'company',
                    'action' => 'update',
                    'endpoint_url' => 'https://example.com/webhooks/company-updated',
                    'active' => true,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getWebhooks();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_webhook()
    {
        $mockResponse = [
            'webhook' => [
                'id' => 1,
                'item_type' => 'candidate',
                'action' => 'create',
                'endpoint_url' => 'https://example.com/webhooks/candidate-created',
                'active' => true,
                'created_at' => '2024-12-19T12:00:00.000Z',
                'updated_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getWebhook(1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_webhook()
    {
        $mockResponse = [
            'webhook' => [
                'id' => 123,
                'item_type' => 'job',
                'action' => 'create',
                'endpoint_url' => 'https://myapp.com/webhooks/job-created',
                'active' => true,
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $webhookData = [
            'webhook' => [
                'item_type' => 'job',
                'action' => 'create',
                'endpoint_url' => 'https://myapp.com/webhooks/job-created'
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createWebhook($webhookData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_webhook_with_all_item_types()
    {
        $itemTypes = [
            'candidate',
            'company',
            'deal',
            'job',
            'person_education_profile',
            'person_event',
            'person_job_profile',
            'person',
            'placement_split',
            'placement'
        ];

        foreach ($itemTypes as $itemType) {
            $mockResponse = [
                'webhook' => [
                    'id' => 124,
                    'item_type' => $itemType,
                    'action' => 'update',
                    'endpoint_url' => "https://myapp.com/webhooks/{$itemType}-updated",
                    'active' => true,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ]
            ];

            $webhookData = [
                'webhook' => [
                    'item_type' => $itemType,
                    'action' => 'update',
                    'endpoint_url' => "https://myapp.com/webhooks/{$itemType}-updated"
                ]
            ];

            $service = $this->createServiceWithMockResponse(201, $mockResponse);
            $result = $service->createWebhook($webhookData);

            $this->assertEquals($mockResponse, $result);
        }
    }

    public function test_it_can_create_webhook_with_all_actions()
    {
        $actions = ['create', 'update', 'destroy'];

        foreach ($actions as $action) {
            $mockResponse = [
                'webhook' => [
                    'id' => 125,
                    'item_type' => 'person',
                    'action' => $action,
                    'endpoint_url' => "https://myapp.com/webhooks/person-{$action}",
                    'active' => true,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ]
            ];

            $webhookData = [
                'webhook' => [
                    'item_type' => 'person',
                    'action' => $action,
                    'endpoint_url' => "https://myapp.com/webhooks/person-{$action}"
                ]
            ];

            $service = $this->createServiceWithMockResponse(201, $mockResponse);
            $result = $service->createWebhook($webhookData);

            $this->assertEquals($mockResponse, $result);
        }
    }

    public function test_it_can_update_webhook()
    {
        $mockResponse = [
            'webhook' => [
                'id' => 1,
                'item_type' => 'candidate',
                'action' => 'update',
                'endpoint_url' => 'https://newapp.com/webhooks/candidate-updated',
                'active' => true,
                'created_at' => '2024-12-19T12:00:00.000Z',
                'updated_at' => '2024-12-19T13:00:00.000Z'
            ]
        ];

        $webhookData = [
            'webhook' => [
                'item_type' => 'candidate',
                'action' => 'update',
                'endpoint_url' => 'https://newapp.com/webhooks/candidate-updated'
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->updateWebhook(1, $webhookData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_delete_webhook()
    {
        $mockResponse = [
            'message' => 'Webhook deleted successfully'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->deleteWebhook(1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_webhook_not_found()
    {
        $errorResponse = [
            'error' => 'Webhook not found',
            'message' => 'The specified webhook does not exist'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getWebhook(999);
    }

    public function test_it_handles_create_webhook_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'webhook.item_type' => ['The item type field is required.'],
                'webhook.action' => ['The action field is required.'],
                'webhook.endpoint_url' => ['The endpoint url field is required.']
            ],
            'message' => 'Validation failed'
        ];

        $webhookData = [
            'webhook' => [
                // Missing required fields
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createWebhook($webhookData);
    }

    public function test_it_handles_update_webhook_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'webhook.endpoint_url' => ['The endpoint url must be a valid URL.']
            ],
            'message' => 'Validation failed'
        ];

        $webhookData = [
            'webhook' => [
                'item_type' => 'candidate',
                'action' => 'create',
                'endpoint_url' => 'invalid-url'
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->updateWebhook(1, $webhookData);
    }

    public function test_it_can_get_users()
    {
        $mockResponse = [
            'users' => [
                [
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john.doe@agency.com',
                    'role' => 'admin',
                    'active' => true,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'email' => 'jane.smith@agency.com',
                    'role' => 'recruiter',
                    'active' => true,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ],
                [
                    'id' => 3,
                    'first_name' => 'Bob',
                    'last_name' => 'Johnson',
                    'email' => 'bob.johnson@agency.com',
                    'role' => 'manager',
                    'active' => false,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ],
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getUsers();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_person_events()
    {
        $mockResponse = [
            'person_events' => [
                [
                    'id' => 1,
                    'activity_type_id' => 1,
                    'person_id' => 123,
                    'job_id' => 456,
                    'company_id' => 789,
                    'notes' => 'Phone interview completed successfully',
                    'pinned' => false,
                    'dragged_and_dropped' => false,
                    'created_at' => '2024-12-19T12:00:00.000Z',
                    'created_by_id' => 1,
                    'updated_at' => '2024-12-19T12:00:00.000Z',
                    'updated_by_id' => 1
                ],
                [
                    'id' => 2,
                    'activity_type_id' => 2,
                    'person_id' => 124,
                    'job_id' => null,
                    'company_id' => 790,
                    'notes' => 'Follow-up email sent',
                    'pinned' => true,
                    'dragged_and_dropped' => false,
                    'created_at' => '2024-12-19T13:00:00.000Z',
                    'created_by_id' => 2,
                    'updated_at' => '2024-12-19T13:00:00.000Z',
                    'updated_by_id' => 2
                ],
            ],
            'meta' => [
                'total' => 2,
                'per_page' => 10,
                'current_page' => 1,
                'has_more' => false
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonEvents();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_person_events_with_filters()
    {
        $mockResponse = [
            'person_events' => [
                [
                    'id' => 3,
                    'activity_type_id' => 1,
                    'person_id' => 125,
                    'job_id' => 460,
                    'notes' => 'Technical interview scheduled',
                    'created_at' => '2024-12-19T14:00:00.000Z',
                    'created_by_id' => 1
                ]
            ],
            'scroll_id' => 'next_cursor_123',
            'meta' => [
                'total' => 15,
                'per_page' => 5,
                'has_more' => true
            ]
        ];

        $params = [
            'scroll_id' => 'prev_cursor_456',
            'query' => 'technical interview',
            'per_page' => 5,
            'page' => 2,
            'activity_type_ids' => [1, 2],
            'created_by_ids' => [1, 3],
            'job_ids' => [460, 461],
            'person_id' => 125,
            'company_id' => 791,
            'created_at_start' => '2024-12-19T00:00:00.000Z',
            'created_at_end' => '2024-12-19T23:59:59.000Z',
            'created_at_sort' => 'desc',
            'serialization_set' => 'full'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonEvents($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_person_event()
    {
        $mockResponse = [
            'person_event' => [
                'id' => 123,
                'activity_type_id' => 1,
                'person_id' => 456,
                'job_id' => 789,
                'company_id' => 101,
                'notes' => 'Initial phone screening conducted',
                'pinned' => false,
                'dragged_and_dropped' => false,
                'created_at' => '2024-12-19T15:00:00.000Z',
                'created_by_id' => 1,
                'updated_at' => '2024-12-19T15:00:00.000Z',
                'updated_by_id' => 1
            ]
        ];

        $personEventData = [
            'person_event' => [
                'activity_type_id' => 1,
                'person_id' => 456,
                'job_id' => 789,
                'company_id' => 101,
                'notes' => 'Initial phone screening conducted',
                'pinned' => false,
                'dragged_and_dropped' => false,
                'created_by_id' => 1
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createPersonEvent($personEventData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_person_event_minimal_data()
    {
        $mockResponse = [
            'person_event' => [
                'id' => 124,
                'activity_type_id' => 2,
                'person_id' => 457,
                'job_id' => null,
                'company_id' => null,
                'notes' => 'Quick follow-up call',
                'pinned' => false,
                'dragged_and_dropped' => false,
                'created_at' => '2024-12-19T16:00:00.000Z',
                'created_by_id' => 2
            ]
        ];

        $personEventData = [
            'person_event' => [
                'activity_type_id' => 2,
                'person_id' => 457,
                'notes' => 'Quick follow-up call',
                'created_by_id' => 2
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createPersonEvent($personEventData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_create_person_event_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'person_event.activity_type_id' => ['The activity type id field is required.'],
                'person_event.person_id' => ['The person id field is required.']
            ],
            'message' => 'Validation failed'
        ];

        $personEventData = [
            'person_event' => [
                'notes' => 'Event without required fields'
                // Missing required fields
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createPersonEvent($personEventData);
    }

    public function test_it_can_get_job_candidates()
    {
        $mockResponse = [
            'candidates' => [
                [
                    'id' => 1,
                    'person_id' => 123,
                    'job_id' => 456,
                    'workflow_stage_id' => 5,
                    'person' => [
                        'id' => 123,
                        'first_name' => 'Alice',
                        'last_name' => 'Johnson',
                        'email' => 'alice.johnson@example.com',
                        'phone' => '+1234567890'
                    ],
                    'latest_activity_type' => [
                        'id' => 1,
                        'name' => 'Phone Screen'
                    ],
                    'created_at' => '2024-12-19T12:00:00.000Z',
                    'updated_at' => '2024-12-19T12:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'person_id' => 124,
                    'job_id' => 456,
                    'workflow_stage_id' => 3,
                    'person' => [
                        'id' => 124,
                        'first_name' => 'Bob',
                        'last_name' => 'Smith',
                        'email' => 'bob.smith@example.com',
                        'phone' => '+0987654321'
                    ],
                    'latest_activity_type' => [
                        'id' => 2,
                        'name' => 'Technical Interview'
                    ],
                    'created_at' => '2024-12-19T11:00:00.000Z',
                    'updated_at' => '2024-12-19T11:00:00.000Z'
                ]
            ],
            'meta' => [
                'total' => 2,
                'per_page' => 10,
                'has_more' => false
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getJobCandidates(456);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_job_candidates_with_filters()
    {
        $mockResponse = [
            'candidates' => [
                [
                    'id' => 3,
                    'person_id' => 125,
                    'job_id' => 456,
                    'workflow_stage_id' => 6,
                    'person' => [
                        'id' => 125,
                        'first_name' => 'Carol',
                        'last_name' => 'Davis',
                        'email' => 'carol.davis@example.com'
                    ],
                    'latest_activity_type' => [
                        'id' => 1,
                        'name' => 'Phone Screen'
                    ]
                ]
            ],
            'scroll_id' => 'next_candidates_cursor_123',
            'meta' => [
                'total' => 25,
                'per_page' => 5,
                'has_more' => true
            ]
        ];

        $params = [
            'per_page' => 5,
            'scroll_id' => 'prev_candidates_cursor_456',
            'query' => 'carol engineer',
            'activity_type_id' => 1,
            'person_id' => 125
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getJobCandidates(456, $params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_job_candidate()
    {
        $mockResponse = [
            'candidate' => [
                'id' => 1,
                'person_id' => 123,
                'job_id' => 456,
                'workflow_stage_id' => 5,
                'person' => [
                    'id' => 123,
                    'first_name' => 'Alice',
                    'last_name' => 'Johnson',
                    'email' => 'alice.johnson@example.com',
                    'phone' => '+1234567890',
                    'title' => 'Senior Software Engineer',
                    'company' => 'TechCorp Inc',
                    'location' => 'San Francisco, CA'
                ],
                'job' => [
                    'id' => 456,
                    'title' => 'Full Stack Developer',
                    'company' => [
                        'id' => 789,
                        'name' => 'StartupXYZ'
                    ]
                ],
                'workflow_stage' => [
                    'id' => 5,
                    'name' => 'Technical Interview',
                    'position' => 3
                ],
                'latest_activity_type' => [
                    'id' => 1,
                    'name' => 'Phone Screen'
                ],
                'created_at' => '2024-12-19T12:00:00.000Z',
                'updated_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getJobCandidate(456, 1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_job_candidate_with_params()
    {
        $mockResponse = [
            'candidate' => [
                'id' => 2,
                'person_id' => 124,
                'job_id' => 456,
                'workflow_stage_id' => 6
            ]
        ];

        $params = [
            'job_id' => 456,
            'id' => 2
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getJobCandidate(456, 2, $params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_job_candidate_not_found()
    {
        $errorResponse = [
            'error' => 'Candidate not found',
            'message' => 'The specified candidate does not exist for this job'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getJobCandidate(456, 999);
    }

    public function test_it_handles_job_not_found_for_candidates()
    {
        $errorResponse = [
            'error' => 'Job not found',
            'message' => 'The specified job does not exist'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getJobCandidates(999);
    }

    public function test_it_can_update_job_candidate()
    {
        $mockResponse = [
            'candidate' => [
                'id' => 1,
                'person_id' => 123,
                'job_id' => 456,
                'workflow_stage_id' => 5,
                'highlights' => 'Updated: Excellent technical skills and great culture fit',
                'person' => [
                    'id' => 123,
                    'first_name' => 'Alice',
                    'last_name' => 'Johnson',
                    'email' => 'alice.johnson@example.com'
                ],
                'updated_at' => '2024-12-19T15:00:00.000Z'
            ]
        ];

        $candidateData = [
            'highlights' => 'Updated: Excellent technical skills and great culture fit'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->updateJobCandidate(456, 1, $candidateData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_update_job_candidate_with_empty_highlights()
    {
        $mockResponse = [
            'candidate' => [
                'id' => 2,
                'person_id' => 124,
                'job_id' => 456,
                'workflow_stage_id' => 3,
                'highlights' => '',
                'updated_at' => '2024-12-19T15:30:00.000Z'
            ]
        ];

        $candidateData = [
            'highlights' => ''
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->updateJobCandidate(456, 2, $candidateData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_update_job_candidate_not_found()
    {
        $errorResponse = [
            'error' => 'Candidate not found',
            'message' => 'The specified candidate does not exist for this job'
        ];

        $candidateData = [
            'highlights' => 'This should fail'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->updateJobCandidate(456, 999, $candidateData);
    }

    public function test_it_handles_update_job_candidate_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'highlights' => ['The highlights field must be a string.']
            ],
            'message' => 'Validation failed'
        ];

        $candidateData = [
            'highlights' => 12345  // Invalid type
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->updateJobCandidate(456, 1, $candidateData);
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

    public function test_it_can_create_job()
    {
        $mockResponse = [
            'job' => [
                'id' => 12345,
                'title' => 'Senior Software Engineer',
                'published_name' => 'Senior Software Engineer',
                'description' => 'We are looking for a senior software engineer...',
                'active' => true,
                'published' => true,
                'company_id' => 123,
                'job_type_id' => 1,
                'job_status_id' => 1,
                'remote_work_allowed' => true,
                'salary' => '$120,000 - $150,000',
                'city' => 'San Francisco',
                'state_id' => 1,
                'country_id' => 1,
                'zip' => '94105',
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $jobData = [
            'job' => [
                'title' => 'Senior Software Engineer',
                'published_name' => 'Senior Software Engineer',
                'description' => 'We are looking for a senior software engineer...',
                'active' => true,
                'published' => true,
                'company_id' => 123,
                'job_type_id' => 1,
                'job_status_id' => 1,
                'remote_work_allowed' => true,
                'salary' => '$120,000 - $150,000',
                'city' => 'San Francisco',
                'state_id' => 1,
                'country_id' => 1,
                'zip' => '94105'
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createJob($jobData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_job_with_full_data()
    {
        $mockResponse = [
            'job' => [
                'id' => 12346,
                'title' => 'Full Stack Developer',
                'published_name' => 'Full Stack Developer - Remote',
                'description' => 'Join our team as a full stack developer...',
                'internal_notes' => 'High priority position',
                'active' => true,
                'published' => true,
                'published_at' => '2024-12-19T12:00:00.000Z',
                'published_end_date' => '2025-01-19T23:59:59.000Z',
                'company_id' => 456,
                'company_hidden' => false,
                'raw_company_name' => 'TechCorp Inc',
                'job_type_id' => 2,
                'job_status_id' => 1,
                'job_category_ids' => [1, 2],
                'owner_emails' => ['hiring@techcorp.com', 'hr@techcorp.com'],
                'remote_work_allowed' => true,
                'salary' => '$100,000 - $130,000',
                'address' => '123 Tech Street',
                'city' => 'Austin',
                'state_id' => 2,
                'country_id' => 1,
                'zip' => '78701',
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $jobData = [
            'job' => [
                'title' => 'Full Stack Developer',
                'published_name' => 'Full Stack Developer - Remote',
                'description' => 'Join our team as a full stack developer...',
                'internal_notes' => 'High priority position',
                'active' => true,
                'published' => true,
                'published_at' => '2024-12-19T12:00:00.000Z',
                'published_end_date' => '2025-01-19T23:59:59.000Z',
                'company_id' => 456,
                'company_hidden' => false,
                'raw_company_name' => 'TechCorp Inc',
                'job_type_id' => 2,
                'job_status_id' => 1,
                'job_category_ids' => [1, 2],
                'owner_emails' => ['hiring@techcorp.com', 'hr@techcorp.com'],
                'remote_work_allowed' => true,
                'salary' => '$100,000 - $130,000',
                'address' => '123 Tech Street',
                'city' => 'Austin',
                'state_id' => 2,
                'country_id' => 1,
                'zip' => '78701'
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createJob($jobData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_job_with_custom_fields()
    {
        $mockResponse = [
            'job' => [
                'id' => 12347,
                'title' => 'DevOps Engineer',
                'description' => 'Looking for an experienced DevOps engineer...',
                'active' => true,
                'published' => false,
                'company_id' => 789,
                'job_type_id' => 3,
                'job_status_id' => 2,
                'remote_work_allowed' => false,
                'priority_level' => 'high',
                'tech_stack' => ['AWS', 'Docker', 'Kubernetes'],
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $jobData = [
            'job' => [
                'title' => 'DevOps Engineer',
                'description' => 'Looking for an experienced DevOps engineer...',
                'active' => true,
                'published' => false,
                'company_id' => 789,
                'job_type_id' => 3,
                'job_status_id' => 2,
                'remote_work_allowed' => false,
                'priority_level' => 'high', // Custom field
                'tech_stack' => ['AWS', 'Docker', 'Kubernetes'] // Custom hierarchy field
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createJob($jobData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_create_job_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'job.title' => ['The title field is required.'],
                'job.company_id' => ['The company id field is required.']
            ],
            'message' => 'Validation failed'
        ];

        $jobData = [
            'job' => [
                'description' => 'Job without required fields'
                // Missing required fields
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createJob($jobData);
    }

    public function test_it_handles_create_job_unauthorized()
    {
        $errorResponse = [
            'error' => 'Unauthorized',
            'message' => 'Insufficient permissions to create jobs'
        ];

        $jobData = [
            'job' => [
                'title' => 'Test Job',
                'company_id' => 123,
                'job_type_id' => 1
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(403, $errorResponse);
        $service->createJob($jobData);
    }

    public function test_it_can_get_people()
    {
        $mockResponse = [
            'people' => [
                ['id' => 1, 'first_name' => 'Donut', 'last_name' => 'Coyote', 'email' => 'windmills@aregiants.com'],
                ['id' => 2, 'first_name' => 'Sherlock', 'last_name' => 'Ohms', 'email' => 'elementary@221b.baker.st'],
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
                ['id' => 1, 'first_name' => 'Donut', 'last_name' => 'Coyote', 'email' => 'windmills@aregiants.com']
            ],
            'meta' => [
                'total' => 1,
                'per_page' => 5,
                'current_page' => 1
            ]
        ];

        $params = [
            'per_page' => 5,
            'query' => 'donut',
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
                ['id' => 10, 'first_name' => 'Forrest', 'last_name' => 'Grump', 'email' => 'life.is.like@aboxofchocolates.com']
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

    public function test_it_can_get_people_with_advanced_filters()
    {
        $mockResponse = [
            'people' => [
                [
                    'id' => 15,
                    'first_name' => 'Alice',
                    'last_name' => 'Anderson',
                    'email' => 'alice.anderson@tech.com',
                    'person_global_status_id' => 1,
                    'person_type_id' => 2,
                    'active_job_stage_id' => 5
                ]
            ],
            'meta' => [
                'total' => 1,
                'per_page' => 10,
                'has_more' => false
            ]
        ];

        $params = [
            'query' => 'Alice Anderson',
            'person_global_status_id' => 1,
            'person_type_id' => 2,
            'active_job_stage_id' => 5,
            'include_related_agencies' => true,
            'include_ids' => [15, 20],
            'exclude_ids' => [10, 11],
            'list_id' => 3,
            'created_at_sort' => 'desc',
            'updated_at_sort' => 'asc',
            'serialization_set' => 'full',
            'fields' => ['name', 'email', 'phone']
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
                'name' => 'Elon Tusk',
                'email' => 'elon.tusk@spacex.com',
                'title' => 'Software Engineer',
                'company' => 'Tech Corp',
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $personData = [
            'person' => [
                'name' => 'Elon Tusk',
                'email' => 'elon.tusk@spacex.com',
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
                'name' => 'Holly Golightly',
                'email' => 'holly.golightly@breakfast.com',
                'title' => 'Product Manager',
                'company' => 'Startup Inc',
                'linkedin_url' => 'https://linkedin.com/in/hollygolightly',
                'compensation' => 120000.0,
                'created_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $personData = [
            'person' => [
                'name' => 'Holly Golightly',
                'email' => 'holly.golightly@breakfast.com',
                'title' => 'Product Manager',
                'company' => 'Startup Inc',
                'location' => 'New York, NY',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'United States',
                'linkedin_url' => 'https://linkedin.com/in/hollygolightly',
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
                'name' => 'Walter Black',
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
            'email' => 'walter.black@badbreaking.com',
            'name' => 'Walter Black',
            'phone' => '+1234567890',
            'linkedin' => 'https://linkedin.com/in/walterblack',
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
                'name' => 'Beatrix Kiddo',
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
            'email' => 'beatrix.kiddo@kill-bill.com',
            'name' => 'Beatrix Kiddo',
            'phone' => '+1987654321',
            'linkedin' => 'https://linkedin.com/in/beatrixkiddo',
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
            'email' => 'error@example.com',
            'name' => 'Error Prone',
            'phone' => '+1234567890',
            'linkedin' => 'https://linkedin.com/in/errorprone',
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
            'email' => 'ghost@example.com',
            'name' => 'Ghost User',
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

    public function test_it_can_get_person()
    {
        $mockResponse = [
            'person' => [
                'id' => 123,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'title' => 'Senior Software Engineer',
                'company' => 'Tech Solutions Inc',
                'location' => 'San Francisco, CA',
                'phone' => '+1234567890',
                'linkedin_url' => 'https://linkedin.com/in/johndoe',
                'person_global_status_id' => 1,
                'person_type_id' => 2,
                'compensation' => 150000.0,
                'created_at' => '2024-12-19T12:00:00.000Z',
                'updated_at' => '2024-12-19T12:00:00.000Z'
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPerson(123);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_get_person_not_found()
    {
        $errorResponse = [
            'error' => 'Person not found',
            'message' => 'The specified person does not exist'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getPerson(999);
    }

    public function test_it_can_update_person()
    {
        $mockResponse = [
            'person' => [
                'id' => 123,
                'name' => 'John Doe Updated',
                'email' => 'john.doe.updated@example.com',
                'title' => 'Principal Software Engineer',
                'company' => 'Tech Solutions Inc',
                'location' => 'San Francisco, CA',
                'phone' => '+1234567890',
                'linkedin_url' => 'https://linkedin.com/in/johndoe',
                'person_global_status_id' => 1,
                'person_type_id' => 2,
                'compensation' => 180000.0,
                'updated_at' => '2024-12-19T15:00:00.000Z'
            ]
        ];

        $personData = [
            'person' => [
                'name' => 'John Doe Updated',
                'email' => 'john.doe.updated@example.com',
                'title' => 'Principal Software Engineer',
                'compensation' => 180000.0
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->updatePerson(123, $personData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_update_person_with_full_data()
    {
        $mockResponse = [
            'person' => [
                'id' => 124,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@newcompany.com',
                'title' => 'VP of Engineering',
                'company' => 'New Company LLC',
                'location' => 'New York, NY',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'United States',
                'phone' => '+1987654321',
                'linkedin_url' => 'https://linkedin.com/in/janesmith',
                'website' => 'https://janesmith.dev',
                'person_global_status_id' => 2,
                'person_type_id' => 1,
                'source_type_id' => 3,
                'blocked' => false,
                'compensation' => 200000.0,
                'salary' => 200000.0,
                'bonus' => 50000,
                'equity' => 0.5,
                'all_raw_tags' => ['leadership', 'engineering', 'startup'],
                'list_ids' => [1, 3, 5],
                'updated_at' => '2024-12-19T16:00:00.000Z'
            ]
        ];

        $personData = [
            'person' => [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@newcompany.com',
                'title' => 'VP of Engineering',
                'company' => 'New Company LLC',
                'location' => 'New York, NY',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'United States',
                'phone' => '+1987654321',
                'linkedin_url' => 'https://linkedin.com/in/janesmith',
                'website' => 'https://janesmith.dev',
                'person_global_status_id' => 2,
                'person_type_id' => 1,
                'source_type_id' => 3,
                'blocked' => false,
                'compensation' => 200000.0,
                'salary' => 200000.0,
                'bonus' => 50000,
                'equity' => 0.5,
                'all_raw_tags' => ['leadership', 'engineering', 'startup'],
                'list_ids' => [1, 3, 5]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->updatePerson(124, $personData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_update_person_not_found()
    {
        $errorResponse = [
            'error' => 'Person not found',
            'message' => 'The specified person does not exist'
        ];

        $personData = [
            'person' => [
                'name' => 'This should fail',
                'email' => 'fail@example.com'
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->updatePerson(999, $personData);
    }

    public function test_it_handles_update_person_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'person.email' => ['The email must be a valid email address.'],
                'person.compensation' => ['The compensation must be a number.']
            ],
            'message' => 'Validation failed'
        ];

        $personData = [
            'person' => [
                'name' => 'Valid Name',
                'email' => 'invalid-email',
                'compensation' => 'not-a-number'
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->updatePerson(123, $personData);
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

    public function test_it_can_get_person_education_profiles()
    {
        $mockResponse = [
            'education_profiles' => [
                [
                    'id' => 1,
                    'degree' => 'Bachelor of Science',
                    'school' => 'Stanford University',
                    'month' => 6,
                    'year' => 2018,
                    'education_type_id' => 1,
                    'description' => 'Computer Science degree with focus on software engineering'
                ],
                [
                    'id' => 2,
                    'degree' => 'Master of Business Administration',
                    'school' => 'Harvard Business School',
                    'month' => 5,
                    'year' => 2020,
                    'education_type_id' => 2,
                    'description' => 'MBA with concentration in technology management'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonEducationProfiles(123);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_person_education_profiles_with_params()
    {
        $mockResponse = [
            'education_profiles' => []
        ];

        $params = [
            'per_page' => 10,
            'page' => 1
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonEducationProfiles(123, $params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_get_person_education_profiles_not_found()
    {
        $errorResponse = [
            'error' => 'Person not found',
            'message' => 'The specified person does not exist'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getPersonEducationProfiles(999);
    }

    public function test_it_can_create_person_education_profile()
    {
        $educationData = [
            'degree' => 'Bachelor of Science',
            'school' => 'MIT',
            'month' => 6,
            'year' => 2022,
            'education_type_id' => 1,
            'description' => 'Computer Science with specialization in AI'
        ];

        $mockResponse = [
            'education_profile' => [
                'id' => 3,
                'degree' => 'Bachelor of Science',
                'school' => 'MIT',
                'month' => 6,
                'year' => 2022,
                'education_type_id' => 1,
                'description' => 'Computer Science with specialization in AI',
                'person_id' => 123
            ]
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createPersonEducationProfile(123, $educationData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_create_person_education_profile_validation_error()
    {
        $educationData = [
            'degree' => '',  // Invalid: empty degree
            'school' => 'MIT'
        ];

        $errorResponse = [
            'error' => 'Validation failed',
            'message' => 'Degree is required',
            'errors' => [
                'degree' => ['Degree cannot be empty']
            ]
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createPersonEducationProfile(123, $educationData);
    }

    public function test_it_handles_create_person_education_profile_unauthorized()
    {
        $educationData = [
            'degree' => 'Bachelor of Science',
            'school' => 'MIT',
            'month' => 6,
            'year' => 2022,
            'education_type_id' => 1
        ];

        $errorResponse = [
            'error' => 'Unauthorized',
            'message' => 'Access denied'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(403, $errorResponse);
        $service->createPersonEducationProfile(123, $educationData);
    }

    public function test_it_can_get_sms()
    {
        $mockResponse = [
            'sms' => [
                [
                    'id' => 1,
                    'from_number' => '+1234567890',
                    'to_number' => '+0987654321',
                    'body' => 'Hello! This is a test SMS message.',
                    'status' => 'sent',
                    'job_id' => 123,
                    'person_id' => 456,
                    'created_at' => '2024-12-19T12:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'from_number' => '+1234567890',
                    'to_number' => '+1111111111',
                    'body' => 'Another SMS message.',
                    'status' => 'delivered',
                    'job_id' => null,
                    'person_id' => 789,
                    'created_at' => '2024-12-19T13:00:00.000Z'
                ]
            ],
            'scroll_id' => 'next_page_cursor'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSms();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_sms_with_params()
    {
        $mockResponse = [
            'sms' => [
                [
                    'id' => 3,
                    'from_number' => '+1234567890',
                    'to_number' => '+2222222222',
                    'body' => 'Filtered SMS message.',
                    'status' => 'sent',
                    'created_at' => '2024-12-20T10:00:00.000Z'
                ]
            ]
        ];

        $params = [
            'per_page' => 10,
            'created_at_start' => '2024-12-20T00:00:00Z',
            'created_at_end' => '2024-12-20T23:59:59Z'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSms($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_sms()
    {
        $mockResponse = [
            'sms' => [
                'id' => 12345,
                'from_number' => '+1234567890',
                'to_number' => '+0987654321',
                'body' => 'Hello! This is a new SMS message.',
                'status' => 'queued',
                'job_id' => 123,
                'person_id' => 456,
                'target_send_time' => null,
                'block_override' => false,
                'created_at' => '2024-12-19T14:00:00.000Z'
            ]
        ];

        $smsData = [
            'from_number' => '+1234567890',
            'to_number' => '+0987654321',
            'body' => 'Hello! This is a new SMS message.',
            'job_id' => 123,
            'person_id' => 456,
            'block_override' => false
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createSms($smsData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_create_sms_with_scheduled_time()
    {
        $mockResponse = [
            'sms' => [
                'id' => 12346,
                'from_number' => '+1234567890',
                'to_number' => '+0987654321',
                'body' => 'Scheduled SMS message.',
                'status' => 'scheduled',
                'job_id' => null,
                'person_id' => 789,
                'target_send_time' => '2024-12-20T09:00:00.000Z',
                'block_override' => true,
                'created_at' => '2024-12-19T14:30:00.000Z'
            ]
        ];

        $smsData = [
            'from_number' => '+1234567890',
            'to_number' => '+0987654321',
            'body' => 'Scheduled SMS message.',
            'person_id' => 789,
            'target_send_time' => '2024-12-20T09:00:00.000Z',
            'block_override' => true
        ];

        $service = $this->createServiceWithMockResponse(201, $mockResponse);
        $result = $service->createSms($smsData);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_create_sms_validation_error()
    {
        $errorResponse = [
            'errors' => [
                'from_number' => ['The from number field is required.'],
                'to_number' => ['The to number field is required.'],
                'body' => ['The body field is required.']
            ],
            'message' => 'Validation failed'
        ];

        $smsData = [
            'body' => '' // Missing required fields
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed');

        $service = $this->createServiceWithMockResponse(422, $errorResponse);
        $service->createSms($smsData);
    }

    public function test_it_can_get_sms_by_id()
    {
        $mockResponse = [
            'sms' => [
                'id' => 123,
                'from_number' => '+1234567890',
                'to_number' => '+0987654321',
                'body' => 'Specific SMS message by ID.',
                'status' => 'delivered',
                'job_id' => 456,
                'person_id' => 789,
                'target_send_time' => null,
                'block_override' => false,
                'created_at' => '2024-12-19T15:00:00.000Z',
                'updated_at' => '2024-12-19T15:01:00.000Z'
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSmsById(123);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_seniority_levels()
    {
        $mockResponse = [
            'seniority_levels' => [
                [
                    'id' => 1,
                    'name' => 'Junior',
                    'description' => 'Entry level position',
                    'level' => 1
                ],
                [
                    'id' => 2,
                    'name' => 'Mid-Level',
                    'description' => 'Intermediate level position',
                    'level' => 2
                ],
                [
                    'id' => 3,
                    'name' => 'Senior',
                    'description' => 'Senior level position',
                    'level' => 3
                ],
                [
                    'id' => 4,
                    'name' => 'Lead',
                    'description' => 'Leadership position',
                    'level' => 4
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSeniorityLevels();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_scorecard_visibility_types()
    {
        $mockResponse = [
            'scorecard_visibility_types' => [
                [
                    'id' => 1,
                    'name' => 'Public',
                    'description' => 'Visible to all users'
                ],
                [
                    'id' => 2,
                    'name' => 'Private',
                    'description' => 'Visible only to creator'
                ],
                [
                    'id' => 3,
                    'name' => 'Team',
                    'description' => 'Visible to team members'
                ],
                [
                    'id' => 4,
                    'name' => 'Manager',
                    'description' => 'Visible to managers only'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getScorecardVisibilityTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_scorecard_types()
    {
        $mockResponse = [
            'scorecard_types' => [
                [
                    'id' => 1,
                    'name' => 'Technical Interview',
                    'description' => 'Technical skills evaluation'
                ],
                [
                    'id' => 2,
                    'name' => 'Cultural Fit',
                    'description' => 'Company culture alignment assessment'
                ],
                [
                    'id' => 3,
                    'name' => 'Phone Screen',
                    'description' => 'Initial phone screening evaluation'
                ],
                [
                    'id' => 4,
                    'name' => 'Final Interview',
                    'description' => 'Final round interview assessment'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getScorecardTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_pronouns()
    {
        $mockResponse = [
            'pronouns' => [
                [
                    'id' => 1,
                    'name' => 'He/Him',
                    'description' => 'Masculine pronouns'
                ],
                [
                    'id' => 2,
                    'name' => 'She/Her',
                    'description' => 'Feminine pronouns'
                ],
                [
                    'id' => 3,
                    'name' => 'They/Them',
                    'description' => 'Gender-neutral pronouns'
                ],
                [
                    'id' => 4,
                    'name' => 'Other',
                    'description' => 'Other pronouns'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPronouns();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_phone_types()
    {
        $mockResponse = [
            'phone_types' => [
                [
                    'id' => 1,
                    'name' => 'Mobile',
                    'description' => 'Mobile phone number'
                ],
                [
                    'id' => 2,
                    'name' => 'Home',
                    'description' => 'Home phone number'
                ],
                [
                    'id' => 3,
                    'name' => 'Work',
                    'description' => 'Work phone number'
                ],
                [
                    'id' => 4,
                    'name' => 'Fax',
                    'description' => 'Fax number'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPhoneTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_person_types()
    {
        $mockResponse = [
            'person_types' => [
                [
                    'id' => 1,
                    'name' => 'Candidate',
                    'description' => 'Job candidate'
                ],
                [
                    'id' => 2,
                    'name' => 'Contact',
                    'description' => 'Company contact'
                ],
                [
                    'id' => 3,
                    'name' => 'Employee',
                    'description' => 'Current employee'
                ],
                [
                    'id' => 4,
                    'name' => 'Contractor',
                    'description' => 'Independent contractor'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_person_share_field_types()
    {
        $mockResponse = [
            'person_share_field_types' => [
                [
                    'id' => 1,
                    'name' => 'Email',
                    'description' => 'Email address sharing'
                ],
                [
                    'id' => 2,
                    'name' => 'Phone',
                    'description' => 'Phone number sharing'
                ],
                [
                    'id' => 3,
                    'name' => 'Resume',
                    'description' => 'Resume document sharing'
                ],
                [
                    'id' => 4,
                    'name' => 'Profile',
                    'description' => 'Full profile sharing'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonShareFieldTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_person_lists()
    {
        $mockResponse = [
            'person_lists' => [
                [
                    'id' => 1,
                    'name' => 'Top Candidates',
                    'description' => 'List of top performing candidates',
                    'person_count' => 25,
                    'created_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Tech Talent Pool',
                    'description' => 'Technology professionals',
                    'person_count' => 150,
                    'created_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Marketing Specialists',
                    'description' => 'Marketing and communications experts',
                    'person_count' => 75,
                    'created_at' => '2024-02-10T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getPersonLists();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 1,
                    'item_type' => 'person',
                    'primary_item_id' => 123,
                    'secondary_item_id' => 456,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'created_by_id' => 1,
                    'notes' => 'Merged duplicate candidates'
                ],
                [
                    'id' => 2,
                    'item_type' => 'company',
                    'primary_item_id' => 789,
                    'secondary_item_id' => 101,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'created_by_id' => 2,
                    'notes' => 'Consolidated company records'
                ]
            ],
            'scroll_id' => 'next_page_cursor_123'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges_with_scroll_id()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 3,
                    'item_type' => 'person',
                    'primary_item_id' => 111,
                    'secondary_item_id' => 222,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'created_by_id' => 3,
                    'notes' => 'Merged candidate profiles'
                ]
            ],
            'scroll_id' => 'next_page_cursor_456'
        ];

        $params = ['scroll_id' => 'previous_page_cursor_123'];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges_with_per_page()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 4,
                    'item_type' => 'company',
                    'primary_item_id' => 333,
                    'secondary_item_id' => 444,
                    'created_at' => '2024-03-15T16:45:00.000Z',
                    'created_by_id' => 4,
                    'notes' => 'Company merge operation'
                ]
            ]
        ];

        $params = ['per_page' => 10];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges_with_item_type_filter()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 5,
                    'item_type' => 'person',
                    'primary_item_id' => 555,
                    'secondary_item_id' => 666,
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'created_by_id' => 5,
                    'notes' => 'Person merge with type filter'
                ]
            ]
        ];

        $params = ['item_type' => 'person'];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges_with_item_ids_filter()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 6,
                    'item_type' => 'company',
                    'primary_item_id' => 777,
                    'secondary_item_id' => 888,
                    'created_at' => '2024-04-15T08:30:00.000Z',
                    'created_by_id' => 6,
                    'notes' => 'Specific company merge'
                ]
            ]
        ];

        $params = ['item_ids' => [777, 888]];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges_with_date_filters()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 7,
                    'item_type' => 'person',
                    'primary_item_id' => 999,
                    'secondary_item_id' => 1000,
                    'created_at' => '2024-05-01T10:00:00.000Z',
                    'created_by_id' => 7,
                    'notes' => 'Recent merge within date range'
                ]
            ]
        ];

        $params = [
            'created_after' => '2024-05-01T00:00:00.000Z',
            'created_before' => '2024-05-31T23:59:59.000Z'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_merges_with_all_parameters()
    {
        $mockResponse = [
            'merges' => [
                [
                    'id' => 8,
                    'item_type' => 'person',
                    'primary_item_id' => 1111,
                    'secondary_item_id' => 1222,
                    'created_at' => '2024-06-01T14:20:00.000Z',
                    'created_by_id' => 8,
                    'notes' => 'Comprehensive filtered merge'
                ]
            ],
            'scroll_id' => 'comprehensive_cursor_789'
        ];

        $params = [
            'scroll_id' => 'start_cursor_123',
            'per_page' => 5,
            'item_type' => 'person',
            'item_ids' => [1111, 1222, 1333],
            'created_after' => '2024-06-01T00:00:00.000Z',
            'created_before' => '2024-06-30T23:59:59.000Z'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_merges_response()
    {
        $mockResponse = [
            'merges' => [],
            'scroll_id' => null
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getMerges();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_merges_api_error()
    {
        $errorResponse = [
            'error' => 'Invalid parameters',
            'message' => 'The specified item_type is not valid'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 400');

        $service = $this->createServiceWithMockResponse(400, $errorResponse);
        $service->getMerges(['item_type' => 'invalid_type']);
    }

    public function test_it_can_get_question_types()
    {
        $mockResponse = [
            'question_types' => [
                [
                    'id' => 1,
                    'name' => 'Multiple Choice',
                    'description' => 'Question with multiple choice answers',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Text Input',
                    'description' => 'Free text input question',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Rating Scale',
                    'description' => 'Numerical rating scale question',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getQuestionTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_question_types_with_params()
    {
        $mockResponse = [
            'question_types' => [
                [
                    'id' => 4,
                    'name' => 'Boolean',
                    'description' => 'Yes/No question type',
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'updated_at' => '2024-04-01T12:00:00.000Z'
                ]
            ]
        ];

        $params = ['active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getQuestionTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_question_types_response()
    {
        $mockResponse = [
            'question_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getQuestionTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_question_types_api_error()
    {
        $errorResponse = [
            'error' => 'Unauthorized',
            'message' => 'Invalid API credentials'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 401');

        $service = $this->createServiceWithMockResponse(401, $errorResponse);
        $service->getQuestionTypes();
    }

    public function test_it_can_get_social_profile_types()
    {
        $mockResponse = [
            'social_profile_types' => [
                [
                    'id' => 1,
                    'name' => 'LinkedIn',
                    'description' => 'LinkedIn professional profile',
                    'url_pattern' => 'https://linkedin.com/in/{username}',
                    'icon' => 'linkedin-icon.png',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'GitHub',
                    'description' => 'GitHub developer profile',
                    'url_pattern' => 'https://github.com/{username}',
                    'icon' => 'github-icon.png',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Twitter',
                    'description' => 'Twitter social profile',
                    'url_pattern' => 'https://twitter.com/{username}',
                    'icon' => 'twitter-icon.png',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSocialProfileTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_social_profile_types_with_params()
    {
        $mockResponse = [
            'social_profile_types' => [
                [
                    'id' => 4,
                    'name' => 'Stack Overflow',
                    'description' => 'Stack Overflow developer profile',
                    'url_pattern' => 'https://stackoverflow.com/users/{user_id}',
                    'icon' => 'stackoverflow-icon.png',
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'updated_at' => '2024-04-01T12:00:00.000Z'
                ]
            ]
        ];

        $params = ['active' => true, 'category' => 'professional'];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSocialProfileTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_social_profile_types_response()
    {
        $mockResponse = [
            'social_profile_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getSocialProfileTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_social_profile_types_api_error()
    {
        $errorResponse = [
            'error' => 'Forbidden',
            'message' => 'Access denied to social profile types'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 403');

        $service = $this->createServiceWithMockResponse(403, $errorResponse);
        $service->getSocialProfileTypes();
    }

    public function test_it_can_get_education_types()
    {
        $mockResponse = [
            'education_types' => [
                [
                    'id' => 1,
                    'name' => 'Bachelor\'s Degree',
                    'description' => 'Undergraduate degree',
                    'level' => 'undergraduate',
                    'duration_years' => 4,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Master\'s Degree',
                    'description' => 'Graduate degree',
                    'level' => 'graduate',
                    'duration_years' => 2,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'PhD',
                    'description' => 'Doctoral degree',
                    'level' => 'doctoral',
                    'duration_years' => 5,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEducationTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_education_types_with_params()
    {
        $mockResponse = [
            'education_types' => [
                [
                    'id' => 4,
                    'name' => 'Certificate',
                    'description' => 'Professional certificate',
                    'level' => 'certificate',
                    'duration_years' => 1,
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'updated_at' => '2024-04-01T12:00:00.000Z'
                ]
            ]
        ];

        $params = ['level' => 'certificate', 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEducationTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_education_types_response()
    {
        $mockResponse = [
            'education_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEducationTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_education_types_api_error()
    {
        $errorResponse = [
            'error' => 'Internal Server Error',
            'message' => 'Unable to retrieve education types'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 500');

        $service = $this->createServiceWithMockResponse(500, $errorResponse);
        $service->getEducationTypes();
    }

    public function test_it_can_get_genders()
    {
        $mockResponse = [
            'genders' => [
                [
                    'id' => 1,
                    'name' => 'Male',
                    'description' => 'Male gender identity',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Female',
                    'description' => 'Female gender identity',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Non-binary',
                    'description' => 'Non-binary gender identity',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ],
                [
                    'id' => 4,
                    'name' => 'Prefer not to say',
                    'description' => 'Prefer not to disclose gender identity',
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'updated_at' => '2024-04-01T12:00:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getGenders();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_genders_with_params()
    {
        $mockResponse = [
            'genders' => [
                [
                    'id' => 1,
                    'name' => 'Male',
                    'description' => 'Male gender identity',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getGenders($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_genders_response()
    {
        $mockResponse = [
            'genders' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getGenders();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_genders_api_error()
    {
        $errorResponse = [
            'error' => 'Bad Request',
            'message' => 'Invalid request parameters'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 400');

        $service = $this->createServiceWithMockResponse(400, $errorResponse);
        $service->getGenders();
    }

    public function test_it_can_get_ethnicities()
    {
        $mockResponse = [
            'ethnicities' => [
                [
                    'id' => 1,
                    'name' => 'White',
                    'description' => 'White or Caucasian',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Black or African American',
                    'description' => 'Black or African American',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Hispanic or Latino',
                    'description' => 'Hispanic or Latino',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ],
                [
                    'id' => 4,
                    'name' => 'Asian',
                    'description' => 'Asian',
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'updated_at' => '2024-04-01T12:00:00.000Z'
                ],
                [
                    'id' => 5,
                    'name' => 'Prefer not to say',
                    'description' => 'Prefer not to disclose ethnicity',
                    'created_at' => '2024-05-01T16:45:00.000Z',
                    'updated_at' => '2024-05-01T16:45:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEthnicities();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_ethnicities_with_params()
    {
        $mockResponse = [
            'ethnicities' => [
                [
                    'id' => 3,
                    'name' => 'Hispanic or Latino',
                    'description' => 'Hispanic or Latino',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $params = ['category' => 'hispanic', 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEthnicities($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_ethnicities_response()
    {
        $mockResponse = [
            'ethnicities' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEthnicities();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_ethnicities_api_error()
    {
        $errorResponse = [
            'error' => 'Unauthorized',
            'message' => 'Access denied to ethnicity data'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 401');

        $service = $this->createServiceWithMockResponse(401, $errorResponse);
        $service->getEthnicities();
    }

    public function test_it_can_get_diversity_types()
    {
        $mockResponse = [
            'diversity_types' => [
                [
                    'id' => 1,
                    'name' => 'Women',
                    'description' => 'Women in the workplace',
                    'category' => 'gender',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Underrepresented Minorities',
                    'description' => 'Underrepresented ethnic and racial minorities',
                    'category' => 'ethnicity',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'LGBTQ+',
                    'description' => 'Lesbian, Gay, Bisexual, Transgender, Queer/Questioning, and other sexual and gender minorities',
                    'category' => 'sexual_orientation',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ],
                [
                    'id' => 4,
                    'name' => 'People with Disabilities',
                    'description' => 'Individuals with physical, mental, or cognitive disabilities',
                    'category' => 'disability',
                    'created_at' => '2024-04-01T12:00:00.000Z',
                    'updated_at' => '2024-04-01T12:00:00.000Z'
                ],
                [
                    'id' => 5,
                    'name' => 'Veterans',
                    'description' => 'Military veterans and service members',
                    'category' => 'veteran_status',
                    'created_at' => '2024-05-01T16:45:00.000Z',
                    'updated_at' => '2024-05-01T16:45:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getDiversityTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_diversity_types_with_params()
    {
        $mockResponse = [
            'diversity_types' => [
                [
                    'id' => 1,
                    'name' => 'Women',
                    'description' => 'Women in the workplace',
                    'category' => 'gender',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['category' => 'gender', 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getDiversityTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_diversity_types_response()
    {
        $mockResponse = [
            'diversity_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getDiversityTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_diversity_types_api_error()
    {
        $errorResponse = [
            'error' => 'Forbidden',
            'message' => 'Access denied to diversity types data'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 403');

        $service = $this->createServiceWithMockResponse(403, $errorResponse);
        $service->getDiversityTypes();
    }

    public function test_it_can_get_fee_types()
    {
        $mockResponse = [
            'fee_types' => [
                [
                    'id' => 1,
                    'name' => 'Percentage',
                    'description' => 'Percentage-based fee structure',
                    'calculation_method' => 'percentage',
                    'default_rate' => 20.0,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Fixed Amount',
                    'description' => 'Fixed dollar amount fee',
                    'calculation_method' => 'fixed',
                    'default_rate' => 5000.0,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Retainer',
                    'description' => 'Retainer-based fee structure',
                    'calculation_method' => 'retainer',
                    'default_rate' => 10000.0,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getFeeTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_fee_types_with_params()
    {
        $mockResponse = [
            'fee_types' => [
                [
                    'id' => 1,
                    'name' => 'Percentage',
                    'description' => 'Percentage-based fee structure',
                    'calculation_method' => 'percentage',
                    'default_rate' => 20.0,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['calculation_method' => 'percentage', 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getFeeTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_fee_types_response()
    {
        $mockResponse = [
            'fee_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getFeeTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_fee_types_api_error()
    {
        $errorResponse = [
            'error' => 'Bad Request',
            'message' => 'Invalid fee type parameters'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 400');

        $service = $this->createServiceWithMockResponse(400, $errorResponse);
        $service->getFeeTypes();
    }

    public function test_it_can_get_equity_types()
    {
        $mockResponse = [
            'equity_types' => [
                [
                    'id' => 1,
                    'name' => 'Stock Options',
                    'description' => 'Employee stock option plan',
                    'vesting_type' => 'time_based',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'RSU',
                    'description' => 'Restricted Stock Units',
                    'vesting_type' => 'time_based',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Phantom Stock',
                    'description' => 'Phantom stock compensation',
                    'vesting_type' => 'performance_based',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEquityTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_equity_types_with_params()
    {
        $mockResponse = [
            'equity_types' => [
                [
                    'id' => 1,
                    'name' => 'Stock Options',
                    'description' => 'Employee stock option plan',
                    'vesting_type' => 'time_based',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['vesting_type' => 'time_based', 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEquityTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_equity_types_response()
    {
        $mockResponse = [
            'equity_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEquityTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_equity_types_api_error()
    {
        $errorResponse = [
            'error' => 'Unauthorized',
            'message' => 'Access denied to equity types'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 401');

        $service = $this->createServiceWithMockResponse(401, $errorResponse);
        $service->getEquityTypes();
    }

    public function test_it_can_get_compensation_types()
    {
        $mockResponse = [
            'compensation_types' => [
                [
                    'id' => 1,
                    'name' => 'Base Salary',
                    'description' => 'Annual base salary compensation',
                    'category' => 'salary',
                    'frequency' => 'annual',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Hourly Wage',
                    'description' => 'Hourly wage compensation',
                    'category' => 'wage',
                    'frequency' => 'hourly',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Commission',
                    'description' => 'Performance-based commission',
                    'category' => 'variable',
                    'frequency' => 'variable',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompensationTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_compensation_types_with_params()
    {
        $mockResponse = [
            'compensation_types' => [
                [
                    'id' => 1,
                    'name' => 'Base Salary',
                    'description' => 'Annual base salary compensation',
                    'category' => 'salary',
                    'frequency' => 'annual',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['category' => 'salary', 'frequency' => 'annual'];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompensationTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_compensation_types_response()
    {
        $mockResponse = [
            'compensation_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompensationTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_compensation_types_api_error()
    {
        $errorResponse = [
            'error' => 'Internal Server Error',
            'message' => 'Unable to retrieve compensation types'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 500');

        $service = $this->createServiceWithMockResponse(500, $errorResponse);
        $service->getCompensationTypes();
    }

    public function test_it_can_get_email_types()
    {
        $mockResponse = [
            'email_types' => [
                [
                    'id' => 1,
                    'name' => 'Work',
                    'description' => 'Work email address',
                    'is_primary' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Personal',
                    'description' => 'Personal email address',
                    'is_primary' => false,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Other',
                    'description' => 'Other email address',
                    'is_primary' => false,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_email_types_with_params()
    {
        $mockResponse = [
            'email_types' => [
                [
                    'id' => 1,
                    'name' => 'Work',
                    'description' => 'Work email address',
                    'is_primary' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['is_primary' => true, 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_email_types_response()
    {
        $mockResponse = [
            'email_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_email_types_api_error()
    {
        $errorResponse = [
            'error' => 'Bad Request',
            'message' => 'Invalid email type parameters'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 400');

        $service = $this->createServiceWithMockResponse(400, $errorResponse);
        $service->getEmailTypes();
    }

    public function test_it_can_get_email_tracking()
    {
        $mockResponse = [
            'email_tracking' => [
                [
                    'id' => 1,
                    'email_id' => 'msg_123456',
                    'person_id' => 789,
                    'subject' => 'Follow up on your application',
                    'sent_at' => '2024-01-15T10:00:00.000Z',
                    'opened_at' => '2024-01-15T10:30:00.000Z',
                    'clicked_at' => '2024-01-15T10:45:00.000Z',
                    'status' => 'delivered',
                    'open_count' => 2,
                    'click_count' => 1,
                    'created_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'email_id' => 'msg_789012',
                    'person_id' => 456,
                    'subject' => 'New job opportunity',
                    'sent_at' => '2024-01-16T14:00:00.000Z',
                    'opened_at' => null,
                    'clicked_at' => null,
                    'status' => 'sent',
                    'open_count' => 0,
                    'click_count' => 0,
                    'created_at' => '2024-01-16T14:00:00.000Z'
                ]
            ],
            'scroll_id' => 'next_page_cursor_456'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTracking();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_email_tracking_with_scroll_id()
    {
        $mockResponse = [
            'email_tracking' => [
                [
                    'id' => 3,
                    'email_id' => 'msg_345678',
                    'person_id' => 123,
                    'subject' => 'Interview invitation',
                    'sent_at' => '2024-01-17T09:00:00.000Z',
                    'opened_at' => '2024-01-17T09:15:00.000Z',
                    'clicked_at' => '2024-01-17T09:20:00.000Z',
                    'status' => 'delivered',
                    'open_count' => 1,
                    'click_count' => 1,
                    'created_at' => '2024-01-17T09:00:00.000Z'
                ]
            ],
            'scroll_id' => 'next_page_cursor_789'
        ];

        $params = ['scroll_id' => 'previous_page_cursor_456'];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTracking($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_email_tracking_with_person_ids()
    {
        $mockResponse = [
            'email_tracking' => [
                [
                    'id' => 4,
                    'email_id' => 'msg_901234',
                    'person_id' => 789,
                    'subject' => 'Thank you for your interest',
                    'sent_at' => '2024-01-18T11:00:00.000Z',
                    'opened_at' => '2024-01-18T11:30:00.000Z',
                    'clicked_at' => null,
                    'status' => 'delivered',
                    'open_count' => 1,
                    'click_count' => 0,
                    'created_at' => '2024-01-18T11:00:00.000Z'
                ]
            ]
        ];

        $params = ['person_ids' => [789, 456]];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTracking($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_email_tracking_with_date_filters()
    {
        $mockResponse = [
            'email_tracking' => [
                [
                    'id' => 5,
                    'email_id' => 'msg_567890',
                    'person_id' => 321,
                    'subject' => 'Weekly update',
                    'sent_at' => '2024-01-19T16:00:00.000Z',
                    'opened_at' => '2024-01-19T16:15:00.000Z',
                    'clicked_at' => '2024-01-19T16:30:00.000Z',
                    'status' => 'delivered',
                    'open_count' => 1,
                    'click_count' => 2,
                    'created_at' => '2024-01-19T16:00:00.000Z'
                ]
            ]
        ];

        $params = [
            'created_at_start' => '2024-01-19T00:00:00.000Z',
            'created_at_end' => '2024-01-19T23:59:59.000Z'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTracking($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_email_tracking_with_all_parameters()
    {
        $mockResponse = [
            'email_tracking' => [
                [
                    'id' => 6,
                    'email_id' => 'msg_678901',
                    'person_id' => 654,
                    'subject' => 'Comprehensive tracking test',
                    'sent_at' => '2024-01-20T12:00:00.000Z',
                    'opened_at' => '2024-01-20T12:05:00.000Z',
                    'clicked_at' => '2024-01-20T12:10:00.000Z',
                    'status' => 'delivered',
                    'open_count' => 3,
                    'click_count' => 1,
                    'created_at' => '2024-01-20T12:00:00.000Z'
                ]
            ],
            'scroll_id' => 'comprehensive_cursor_123'
        ];

        $params = [
            'scroll_id' => 'start_cursor_456',
            'per_page' => 10,
            'person_ids' => [654, 321, 789],
            'created_at_start' => '2024-01-20T00:00:00.000Z',
            'created_at_end' => '2024-01-20T23:59:59.000Z'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTracking($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_email_tracking_response()
    {
        $mockResponse = [
            'email_tracking' => [],
            'scroll_id' => null
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getEmailTracking();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_email_tracking_api_error()
    {
        $errorResponse = [
            'error' => 'Unauthorized',
            'message' => 'Access denied to email tracking data'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 401');

        $service = $this->createServiceWithMockResponse(401, $errorResponse);
        $service->getEmailTracking();
    }

    public function test_it_can_get_disability_statuses()
    {
        $mockResponse = [
            'disability_statuses' => [
                [
                    'id' => 1,
                    'name' => 'No Disability',
                    'description' => 'Individual does not have a disability',
                    'code' => 'NO_DISABILITY',
                    'is_protected' => false,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Has Disability',
                    'description' => 'Individual has a disability as defined by the ADA',
                    'code' => 'HAS_DISABILITY',
                    'is_protected' => true,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Prefer Not to Answer',
                    'description' => 'Individual prefers not to disclose disability status',
                    'code' => 'PREFER_NOT_TO_ANSWER',
                    'is_protected' => true,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getDisabilityStatuses();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_disability_statuses_with_params()
    {
        $mockResponse = [
            'disability_statuses' => [
                [
                    'id' => 2,
                    'name' => 'Has Disability',
                    'description' => 'Individual has a disability as defined by the ADA',
                    'code' => 'HAS_DISABILITY',
                    'is_protected' => true,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ]
            ]
        ];

        $params = ['is_protected' => true, 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getDisabilityStatuses($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_disability_statuses_response()
    {
        $mockResponse = [
            'disability_statuses' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getDisabilityStatuses();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_disability_statuses_api_error()
    {
        $errorResponse = [
            'error' => 'Forbidden',
            'message' => 'Access denied to disability status data'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 403');

        $service = $this->createServiceWithMockResponse(403, $errorResponse);
        $service->getDisabilityStatuses();
    }

    public function test_it_can_get_countries()
    {
        $mockResponse = [
            'countries' => [
                [
                    'id' => 1,
                    'name' => 'United States',
                    'code' => 'US',
                    'iso_code' => 'USA',
                    'phone_code' => '+1',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Canada',
                    'code' => 'CA',
                    'iso_code' => 'CAN',
                    'phone_code' => '+1',
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'United Kingdom',
                    'code' => 'GB',
                    'iso_code' => 'GBR',
                    'phone_code' => '+44',
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCountries();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_countries_with_params()
    {
        $mockResponse = [
            'countries' => [
                [
                    'id' => 1,
                    'name' => 'United States',
                    'code' => 'US',
                    'iso_code' => 'USA',
                    'phone_code' => '+1',
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['query' => 'United', 'per_page' => 10, 'page' => 1];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCountries($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_countries_response()
    {
        $mockResponse = [
            'countries' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCountries();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_countries_api_error()
    {
        $errorResponse = [
            'error' => 'Bad Request',
            'message' => 'Invalid country search parameters'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 400');

        $service = $this->createServiceWithMockResponse(400, $errorResponse);
        $service->getCountries();
    }

    public function test_it_can_get_states()
    {
        $mockResponse = [
            'states' => [
                [
                    'id' => 1,
                    'name' => 'California',
                    'code' => 'CA',
                    'country_id' => 1,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'New York',
                    'code' => 'NY',
                    'country_id' => 1,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Texas',
                    'code' => 'TX',
                    'country_id' => 1,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getStates(1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_states_with_params()
    {
        $mockResponse = [
            'states' => [
                [
                    'id' => 1,
                    'name' => 'California',
                    'code' => 'CA',
                    'country_id' => 1,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['query' => 'California', 'per_page' => 5, 'page' => 1, 'country_id' => 1];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getStates(1, $params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_states_response()
    {
        $mockResponse = [
            'states' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getStates(1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_states_api_error()
    {
        $errorResponse = [
            'error' => 'Not Found',
            'message' => 'Country not found'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 404');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getStates(999);
    }

    public function test_it_can_get_cities()
    {
        $mockResponse = [
            'cities' => [
                [
                    'id' => 1,
                    'name' => 'Los Angeles',
                    'state_id' => 1,
                    'country_id' => 1,
                    'latitude' => 34.0522,
                    'longitude' => -118.2437,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'San Francisco',
                    'state_id' => 1,
                    'country_id' => 1,
                    'latitude' => 37.7749,
                    'longitude' => -122.4194,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ]
            ],
            'scroll_id' => 'next_page_cursor_123'
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCities(1, 1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_cities_with_params()
    {
        $mockResponse = [
            'cities' => [
                [
                    'id' => 1,
                    'name' => 'Los Angeles',
                    'state_id' => 1,
                    'country_id' => 1,
                    'latitude' => 34.0522,
                    'longitude' => -118.2437,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ],
            'scroll_id' => 'next_page_cursor_456'
        ];

        $params = ['scroll_id' => 'start_cursor', 'per_page' => 25, 'country_id' => 1, 'state_id' => 1];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCities(1, 1, $params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_cities_response()
    {
        $mockResponse = [
            'cities' => [],
            'scroll_id' => null
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCities(1, 1);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_cities_api_error()
    {
        $errorResponse = [
            'error' => 'Not Found',
            'message' => 'State not found for the specified country'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 404');

        $service = $this->createServiceWithMockResponse(404, $errorResponse);
        $service->getCities(1, 999);
    }

    public function test_it_can_get_currencies()
    {
        $mockResponse = [
            'currencies' => [
                [
                    'id' => 1,
                    'name' => 'US Dollar',
                    'code' => 'USD',
                    'symbol' => '$',
                    'decimal_places' => 2,
                    'is_default' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Euro',
                    'code' => 'EUR',
                    'symbol' => '€',
                    'decimal_places' => 2,
                    'is_default' => false,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'British Pound',
                    'code' => 'GBP',
                    'symbol' => '£',
                    'decimal_places' => 2,
                    'is_default' => false,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCurrencies();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_currencies_with_params()
    {
        $mockResponse = [
            'currencies' => [
                [
                    'id' => 1,
                    'name' => 'US Dollar',
                    'code' => 'USD',
                    'symbol' => '$',
                    'decimal_places' => 2,
                    'is_default' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['is_default' => true, 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCurrencies($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_currencies_response()
    {
        $mockResponse = [
            'currencies' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCurrencies();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_currencies_api_error()
    {
        $errorResponse = [
            'error' => 'Internal Server Error',
            'message' => 'Unable to retrieve currencies'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 500');

        $service = $this->createServiceWithMockResponse(500, $errorResponse);
        $service->getCurrencies();
    }

    public function test_it_can_get_company_global_statuses()
    {
        $mockResponse = [
            'company_global_statuses' => [
                [
                    'id' => 1,
                    'name' => 'Active',
                    'description' => 'Company is actively recruiting',
                    'color' => '#28a745',
                    'is_default' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Inactive',
                    'description' => 'Company is not currently recruiting',
                    'color' => '#dc3545',
                    'is_default' => false,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'On Hold',
                    'description' => 'Company recruitment is temporarily paused',
                    'color' => '#ffc107',
                    'is_default' => false,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanyGlobalStatuses();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_company_global_statuses_with_params()
    {
        $mockResponse = [
            'company_global_statuses' => [
                [
                    'id' => 1,
                    'name' => 'Active',
                    'description' => 'Company is actively recruiting',
                    'color' => '#28a745',
                    'is_default' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['is_default' => true, 'active' => true];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanyGlobalStatuses($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_company_global_statuses_response()
    {
        $mockResponse = [
            'company_global_statuses' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanyGlobalStatuses();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_company_global_statuses_api_error()
    {
        $errorResponse = [
            'error' => 'Forbidden',
            'message' => 'Access denied to company global statuses'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 403');

        $service = $this->createServiceWithMockResponse(403, $errorResponse);
        $service->getCompanyGlobalStatuses();
    }

    public function test_it_can_get_company_types()
    {
        $mockResponse = [
            'company_types' => [
                [
                    'id' => 1,
                    'name' => 'Client',
                    'description' => 'Companies that hire through our agency',
                    'is_default' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Prospect',
                    'description' => 'Potential client companies',
                    'is_default' => false,
                    'created_at' => '2024-02-01T14:30:00.000Z',
                    'updated_at' => '2024-02-01T14:30:00.000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Partner',
                    'description' => 'Strategic partner companies',
                    'is_default' => false,
                    'created_at' => '2024-03-01T09:15:00.000Z',
                    'updated_at' => '2024-03-01T09:15:00.000Z'
                ],
                [
                    'id' => 4,
                    'name' => 'Vendor',
                    'description' => 'Service provider companies',
                    'is_default' => false,
                    'created_at' => '2024-04-01T11:45:00.000Z',
                    'updated_at' => '2024-04-01T11:45:00.000Z'
                ]
            ]
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanyTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_can_get_company_types_with_params()
    {
        $mockResponse = [
            'company_types' => [
                [
                    'id' => 1,
                    'name' => 'Client',
                    'description' => 'Companies that hire through our agency',
                    'is_default' => true,
                    'created_at' => '2024-01-15T10:00:00.000Z',
                    'updated_at' => '2024-01-15T10:00:00.000Z'
                ]
            ]
        ];

        $params = ['is_default' => true, 'type' => 'client'];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanyTypes($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_empty_company_types_response()
    {
        $mockResponse = [
            'company_types' => []
        ];

        $service = $this->createServiceWithMockResponse(200, $mockResponse);
        $result = $service->getCompanyTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function test_it_handles_company_types_api_error()
    {
        $errorResponse = [
            'error' => 'Internal Server Error',
            'message' => 'Unable to retrieve company types'
        ];

        $this->expectException(LoxoApiException::class);
        $this->expectExceptionMessage('API request failed with status code: 500');

        $service = $this->createServiceWithMockResponse(500, $errorResponse);
        $service->getCompanyTypes();
    }
}
