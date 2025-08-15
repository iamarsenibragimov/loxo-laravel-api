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
        $this->domain = Config::get('loxo.domain');
        $this->agencySlug = Config::get('loxo.agency_slug');
        $this->apiKey = Config::get('loxo.api_key');

        if (empty($this->domain)) {
            throw new ConfigurationException('Loxo domain is not configured. Please set LOXO_DOMAIN in your .env file.');
        }

        if (empty($this->agencySlug)) {
            throw new ConfigurationException('Loxo agency slug is not configured. Please set LOXO_AGENCY_SLUG in your .env file.');
        }

        if (empty($this->apiKey)) {
            throw new ConfigurationException('Loxo API key is not configured. Please set LOXO_API_KEY in your .env file.');
        }

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
