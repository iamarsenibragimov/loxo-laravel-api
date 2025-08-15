<?php

namespace Loxo\LaravelApi\Contracts;

interface LoxoApiInterface
{
    /**
     * Get activity types for the agency
     *
     * @param array $params Query parameters (workflow_id, show_hidden)
     * @return array
     */
    public function getActivityTypes(array $params = []): array;

    /**
     * Get address types for the agency
     *
     * @param array $params Query parameters
     * @return array
     */
    public function getAddressTypes(array $params = []): array;

    /**
     * Get jobs for the agency
     *
     * @param array $params Query parameters (per_page, page, query, published, etc.)
     * @return array
     */
    public function getJobs(array $params = []): array;

    /**
     * Get people (candidates) for the agency
     *
     * @param array $params Query parameters (scroll_id, per_page, query, etc.)
     * @return array
     */
    public function getPeople(array $params = []): array;

    /**
     * Make a GET request to the Loxo API
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function get(string $endpoint, array $params = []): array;

    /**
     * Make a POST request to the Loxo API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * Make a PUT request to the Loxo API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function put(string $endpoint, array $data = []): array;

    /**
     * Make a DELETE request to the Loxo API
     *
     * @param string $endpoint
     * @return array
     */
    public function delete(string $endpoint): array;
}
