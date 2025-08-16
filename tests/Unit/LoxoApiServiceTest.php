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
