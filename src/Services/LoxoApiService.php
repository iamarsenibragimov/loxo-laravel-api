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
