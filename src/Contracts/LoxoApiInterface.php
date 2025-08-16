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
     * Get bonus payment types for the agency
     *
     * @param array $params Query parameters
     * @return array
     */
    public function getBonusPaymentTypes(array $params = []): array;

    /**
     * Get bonus types for the agency
     *
     * @param array $params Query parameters
     * @return array
     */
    public function getBonusTypes(array $params = []): array;

    /**
     * Get companies for the agency
     *
     * @param array $params Query parameters (scroll_id, query, company_type_id, list_id, company_global_status_id, fields)
     * @return array
     */
    public function getCompanies(array $params = []): array;

    /**
     * Create a new company in the agency
     *
     * @param array $companyData Company data for creation
     * @return array
     */
    public function createCompany(array $companyData): array;

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
     * Create a new person (candidate) in the agency
     *
     * @param array $personData Person data for creation
     * @return array
     */
    public function createPerson(array $personData): array;

    /**
     * Apply to a specific job as a candidate
     *
     * @param int $jobId Job ID to apply to
     * @param array $applicationData Application data
     * @return array
     */
    public function applyToJob(int $jobId, array $applicationData): array;

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
