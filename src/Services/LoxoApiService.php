<?php

namespace Loxo\LaravelApi\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;
use Loxo\LaravelApi\Contracts\LoxoApiInterface;
use Loxo\LaravelApi\Exceptions\ConfigurationException;
use Loxo\LaravelApi\Exceptions\LoxoApiException;

class LoxoApiService implements LoxoApiInterface
{
    protected Client $client;
    protected string $baseUrl;
    protected string $apiKey;
    protected string $domain;
    protected string $agencySlug;

    public function __construct()
    {
        $this->validateConfiguration();
        $this->initializeClient();
    }

    /**
     * Validate that all required configuration is present
     *
     * @throws ConfigurationException
     */
    protected function validateConfiguration(): void
    {
        $domain = Config::get('loxo.domain');
        $agencySlug = Config::get('loxo.agency_slug');
        $apiKey = Config::get('loxo.api_key');

        if (empty($domain)) {
            throw new ConfigurationException('Loxo domain is not configured');
        }

        if (empty($agencySlug)) {
            throw new ConfigurationException('Loxo agency slug is not configured');
        }

        if (empty($apiKey)) {
            throw new ConfigurationException('Loxo API key is not configured');
        }

        $this->domain = $domain;
        $this->agencySlug = $agencySlug;
        $this->apiKey = $apiKey;

        $this->baseUrl = str_replace(
            ['{domain}', '{agency_slug}'],
            [$this->domain, $this->agencySlug],
            Config::get('loxo.base_url', 'https://{domain}/api/{agency_slug}')
        );
    }

    /**
     * Initialize the HTTP client
     */
    protected function initializeClient(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl . '/',
            'timeout' => Config::get('loxo.timeout', 30),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Get activity types for the agency
     *
     * @param array $params Query parameters (workflow_id, show_hidden)
     * @return array
     * @throws LoxoApiException
     */
    public function getActivityTypes(array $params = []): array
    {
        return $this->get('activity_types', $params);
    }

    /**
     * Get address types for the agency
     *
     * @param array $params Query parameters
     * @return array
     * @throws LoxoApiException
     */
    public function getAddressTypes(array $params = []): array
    {
        return $this->get('address_types', $params);
    }

    /**
     * Get bonus payment types for the agency
     *
     * @param array $params Query parameters
     * @return array
     * @throws LoxoApiException
     */
    public function getBonusPaymentTypes(array $params = []): array
    {
        return $this->get('bonus_payment_types', $params);
    }

    /**
     * Get bonus types for the agency
     *
     * @param array $params Query parameters
     * @return array
     * @throws LoxoApiException
     */
    public function getBonusTypes(array $params = []): array
    {
        return $this->get('bonus_types', $params);
    }

    /**
     * Get companies for the agency
     *
     * @param array $params Query parameters:
     *  - scroll_id (string): A cursor used to retrieve the next page of results
     *  - query (string): Search query. Supports Lucene query syntax
     *  - company_type_id (int): Filter by company type ID
     *  - list_id (int): Filter by list ID
     *  - company_global_status_id (int): Filter by company global status ID
     *  - fields (object): Fields to include in response
     * @return array
     * @throws LoxoApiException
     */
    public function getCompanies(array $params = []): array
    {
        return $this->get('companies', $params);
    }

    /**
     * Create a new company in the agency
     *
     * @param array $companyData Company data:
     *  - company[name] (string): Company name
     *  - company[url] (string): Company website URL
     *  - company[description] (string): Company description
     *  - company[culture] (string): Company culture description
     *  - company[blocked] (bool): Whether company is blocked
     *  - company[blocked_until] (string): Timestamp until blocked
     *  - company[fee] (float): Fee amount
     *  - company[fee_type_id] (int): Fee type ID
     *  - company[company_type_id] (int): Company type ID
     *  - company[owner_email] (string): Owner email address
     *  - company[company_global_status_id] (int): Company global status ID
     *  - company[emails] (array): Array of email addresses
     *  - company[phones] (array): Array of phone numbers
     *  - company[addresses] (array): Array of address objects
     *  - company[$dynamic_field_key] (string): Custom field values
     *  - company[$hierarchy_dynamic_field_key] (array): Custom hierarchy field values
     * @return array
     * @throws LoxoApiException
     */
    public function createCompany(array $companyData): array
    {
        return $this->post('companies', $companyData);
    }

    /**
     * Get jobs for the agency
     *
     * @param array $params Query parameters:
     *  - per_page (int): Number of results to return in each page
     *  - page (int): Page number to return. Starting at 1
     *  - query (string): Search query. Supports Lucene query syntax
     *  - include_related_agencies (bool): Include results from related agencies
     *  - published_at_sort (string): Sort by published_at
     *  - rank_sort (string): Sort by rank
     *  - updated_at_sort (string): Sort by updated_at
     *  - location_sort (string): Sort by location
     *  - job_title_sort (string): Sort by title
     *  - published (bool): Filter by published status
     *  - job_status_id (int): Filter by job status ID
     *  - job_status_ids (array): Filter by multiple job status IDs
     *  - job_type_id (int): Filter by job type ID
     *  - country_id (int): Filter by country ID
     *  - state_id (int): Filter by state ID
     *  - city (string): Filter by city
     *  - job_category_ids (array): Filter by job category IDs
     *  - owned_by_ids (array): Filter by job owner IDs
     *  - status (string): Filter by status
     *  - job_type (string): Filter by job type
     *  - remote_work_allowed (bool): Filter by remote work allowed
     *  - serialization_set (string): Serialization set
     * @return array
     * @throws LoxoApiException
     */
    public function getJobs(array $params = []): array
    {
        return $this->get('jobs', $params);
    }

    /**
     * Get people (candidates) for the agency
     *
     * @param array $params Query parameters:
     *  - scroll_id (string): A cursor used to retrieve the next page of results
     *  - per_page (int): Number of results to return in each page
     *  - query (string): Search query. Supports Lucene query syntax
     *  - include_related_agencies (bool): Include results from related agencies
     *  - include_ids (array): Array of person IDs to include
     *  - exclude_ids (array): Array of person IDs to exclude
     *  - person_global_status_id (int): Filter by person global status ID
     *  - active_job_stage_id (int): Filter by active job stage ID
     *  - active_workflow_stage_id (int): Filter by active workflow stage ID
     *  - person_type_id (int): Filter by person type ID
     *  - list_id (int): Filter by list ID
     *  - created_at_sort (string): Sort by created_at
     *  - updated_at_sort (string): Sort by updated_at
     *  - serialization_set (string): Serialization set
     *  - fields (object): Fields to include in response
     * @return array
     * @throws LoxoApiException
     */
    public function getPeople(array $params = []): array
    {
        return $this->get('people', $params);
    }

    /**
     * Create a new person (candidate) in the agency
     *
     * @param array $personData Person data:
     *  - person[profile_picture] (file): Profile picture of the person
     *  - person[name] (string): Full name
     *  - person[description] (string): Description
     *  - person[location] (string): Location
     *  - person[address] (string): Address
     *  - person[city] (string): City
     *  - person[state] (string): State
     *  - person[zip] (string): ZIP code
     *  - person[country] (string): Country
     *  - person[person_global_status_id] (int): Global status ID
     *  - person[source_type_id] (int): Source type ID
     *  - person[blocked] (bool): Whether person is blocked
     *  - person[blocked_until] (string): Timestamp until blocked
     *  - person[title] (string): Current job title
     *  - person[company] (string): Current company
     *  - person[email] (string): Email address
     *  - person[emails] (array): Array of email objects
     *  - person[phone] (string): Phone number
     *  - person[phones] (array): Array of phone objects
     *  - person[data_sources] (array): Array of data source objects
     *  - person[linkedin_url] (string): LinkedIn profile URL
     *  - person[website] (string): Website URL
     *  - person[resume] (file): Resume file
     *  - person[resume_skip_parse] (bool): Skip resume parsing
     *  - person[document] (file): Additional documents
     *  - person[all_raw_tags] (array): Array of tags
     *  - person[person_type_id] (int): Person type ID
     *  - person[candidates] (array): Array of candidate objects
     *  - person[list_ids] (array): Array of list IDs
     *  - person[diversity_type_ids] (array): Array of diversity type IDs
     *  - person[compensation] (float): Compensation amount
     *  - person[compensation_notes] (string): Compensation notes
     *  - person[salary] (float): Salary amount
     *  - person[salary_type_id] (int): Salary type ID
     *  - person[compensation_currency_id] (int): Currency ID
     *  - person[bonus] (int): Bonus amount
     *  - person[bonus_type_id] (int): Bonus type ID
     *  - person[bonus_payment_type_id] (int): Bonus payment type ID
     *  - person[equity] (float): Equity amount
     *  - person[equity_type_id] (int): Equity type ID
     *  - person[owned_by_id] (int): Owner user ID
     *  - person[$dynamic_field_key] (string): Custom field values
     *  - person[$hierarchy_dynamic_field_key] (array): Custom hierarchy field values
     * @return array
     * @throws LoxoApiException
     */
    public function createPerson(array $personData): array
    {
        return $this->post('people', $personData);
    }

    /**
     * Apply to a specific job as a candidate
     *
     * @param int $jobId Job ID to apply to
     * @param array $applicationData Application data:
     *  - email (string, required): Email of applying candidate. Will lookup existing people
     *  - name (string, required): Name of applying candidate
     *  - phone (string, required): Phone number of applying candidate
     *  - linkedin (string): LinkedIn profile URL of applying candidate
     *  - resume (file|resource, required): Resume file resource or path to file
     *  - work_authorization (bool): Work authorization status
     *  - requires_visa (bool): Whether candidate requires visa
     *  - gender_ids (array): Array of gender IDs
     *  - ethnicity_ids (array): Array of ethnicity IDs
     *  - veteran_status_id (int): Veteran status ID
     *  - pronoun_id (int): Pronoun ID
     *  - other_pronoun (string): Other pronoun if not in list
     *  - disability_status_id (int): Disability status ID
     *  - form_template_id (int): Form template ID
     *  - source_type_id (int): Source type ID
     * @return array
     * @throws LoxoApiException
     */
    public function applyToJob(int $jobId, array $applicationData): array
    {
        // Convert application data to multipart format
        $multipartData = [];

        foreach ($applicationData as $key => $value) {
            if ($key === 'resume') {
                // Handle resume file
                if (is_resource($value)) {
                    $multipartData[] = ['name' => $key, 'contents' => $value, 'filename' => 'resume.pdf'];
                } elseif (is_string($value) && file_exists($value)) {
                    $multipartData[] = ['name' => $key, 'contents' => fopen($value, 'r'), 'filename' => basename($value)];
                } else {
                    throw new \InvalidArgumentException('Resume must be a file resource or valid file path');
                }
            } else {
                // Handle regular form fields
                if (is_array($value)) {
                    // Handle array values (like gender_ids, ethnicity_ids)
                    foreach ($value as $arrayValue) {
                        $multipartData[] = ['name' => $key . '[]', 'contents' => (string) $arrayValue];
                    }
                } else {
                    $multipartData[] = ['name' => $key, 'contents' => (string) $value];
                }
            }
        }

        return $this->postMultipart("jobs/{$jobId}/apply", $multipartData);
    }

    /**
     * Make a GET request to the Loxo API
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws LoxoApiException
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->makeRequest('GET', $endpoint, ['query' => $params]);
    }

    /**
     * Make a POST request to the Loxo API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws LoxoApiException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make a POST request to the Loxo API with form data
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws LoxoApiException
     */
    public function postForm(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('POST', $endpoint, ['form_params' => $data]);
    }

    /**
     * Make a POST request to the Loxo API with multipart data
     *
     * @param string $endpoint
     * @param array $multipartData
     * @return array
     * @throws LoxoApiException
     */
    public function postMultipart(string $endpoint, array $multipartData = []): array
    {
        return $this->makeRequest('POST', $endpoint, ['multipart' => $multipartData]);
    }

    /**
     * Make a PUT request to the Loxo API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws LoxoApiException
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->makeRequest('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Make a DELETE request to the Loxo API
     *
     * @param string $endpoint
     * @return array
     * @throws LoxoApiException
     */
    public function delete(string $endpoint): array
    {
        return $this->makeRequest('DELETE', $endpoint);
    }

    /**
     * Make a request to the Loxo API with retry logic
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return array
     * @throws LoxoApiException
     */
    protected function makeRequest(string $method, string $endpoint, array $options = []): array
    {
        $retryAttempts = Config::get('loxo.retry_attempts', 3);
        $retryDelay = Config::get('loxo.retry_delay', 1000);

        for ($attempt = 1; $attempt <= $retryAttempts; $attempt++) {
            try {
                $response = $this->client->request($method, $endpoint, $options);

                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                if ($statusCode >= 200 && $statusCode < 300) {
                    return $data ?? [];
                }

                throw new LoxoApiException(
                    "API request failed with status code: {$statusCode}",
                    $statusCode,
                    $data ?? []
                );
            } catch (RequestException $e) {
                $response = $e->getResponse();
                $statusCode = $response ? $response->getStatusCode() : 0;
                $body = $response ? $response->getBody()->getContents() : '';
                $data = json_decode($body, true);

                // Don't retry on client errors (4xx)
                if ($statusCode >= 400 && $statusCode < 500) {
                    throw new LoxoApiException(
                        "API request failed: " . $e->getMessage(),
                        $statusCode,
                        $data ?? []
                    );
                }

                // Retry on server errors (5xx) or network issues
                if ($attempt === $retryAttempts) {
                    throw new LoxoApiException(
                        "API request failed after {$retryAttempts} attempts: " . $e->getMessage(),
                        $statusCode,
                        $data ?? []
                    );
                }

                // Wait before retrying
                usleep($retryDelay * 1000);
            } catch (GuzzleException $e) {
                if ($attempt === $retryAttempts) {
                    throw new LoxoApiException(
                        "API request failed: " . $e->getMessage(),
                        0,
                        []
                    );
                }

                // Wait before retrying
                usleep($retryDelay * 1000);
            }
        }

        // This should never be reached, but just in case
        throw new LoxoApiException('Unexpected error occurred during API request');
    }

    /**
     * Get the base URL for the API
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the agency slug
     *
     * @return string
     */
    public function getAgencySlug(): string
    {
        return $this->agencySlug;
    }

    /**
     * Get the domain
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}
