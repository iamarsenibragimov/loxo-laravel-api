<?php

/**
 * Example usage of Loxo Laravel API Package
 * 
 * This file demonstrates how to use the package in a Laravel application
 */

// Using the facade (most common approach)
use Loxo\LaravelApi\Facades\Loxo;
use Loxo\LaravelApi\Exceptions\LoxoApiException;
use Loxo\LaravelApi\Exceptions\ConfigurationException;

class ExampleController extends Controller
{
    /**
     * Get activity types using the facade
     */
    public function getActivityTypes()
    {
        try {
            // Get all activity types
            $activityTypes = Loxo::getActivityTypes();

            return response()->json([
                'success' => true,
                'data' => $activityTypes
            ]);
        } catch (ConfigurationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Configuration error: ' . $e->getMessage()
            ], 500);
        } catch (LoxoApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'api_response' => $e->getResponse()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get activity types with filters
     */
    public function getFilteredActivityTypes(Request $request)
    {
        try {
            $params = [];

            if ($request->has('workflow_id')) {
                $params['workflow_id'] = $request->get('workflow_id');
            }

            if ($request->has('show_hidden')) {
                $params['show_hidden'] = $request->boolean('show_hidden');
            }

            $activityTypes = Loxo::getActivityTypes($params);

            return response()->json([
                'success' => true,
                'data' => $activityTypes,
                'filters' => $params
            ]);
        } catch (LoxoApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Example using dependency injection
     */
    public function getAddressTypes(LoxoApiInterface $loxoApi)
    {
        try {
            $addressTypes = $loxoApi->getAddressTypes();

            return response()->json([
                'success' => true,
                'data' => $addressTypes
            ]);
        } catch (LoxoApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Example of making a custom API call
     */
    public function customApiCall()
    {
        try {
            // Custom GET request
            $data = Loxo::get('custom-endpoint', [
                'param1' => 'value1',
                'param2' => 'value2'
            ]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (LoxoApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Example of error handling and logging
     */
    public function handleApiErrors()
    {
        try {
            $data = Loxo::getActivityTypes();

            // Log successful API call
            Log::info('Loxo API call successful', [
                'endpoint' => 'activity_types',
                'records_count' => count($data)
            ]);

            return response()->json($data);
        } catch (LoxoApiException $e) {
            // Log API errors with context
            Log::error('Loxo API error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->getResponse(),
                'endpoint' => 'activity_types'
            ]);

            return response()->json([
                'error' => 'Failed to fetch activity types',
                'details' => $e->getMessage()
            ], 500);
        } catch (ConfigurationException $e) {
            // Log configuration errors
            Log::critical('Loxo API configuration error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'API configuration error'
            ], 500);
        }
    }
}

/**
 * Example Artisan command using Loxo API
 */
class SyncLoxoDataCommand extends Command
{
    protected $signature = 'loxo:sync {--type=activity_types}';
    protected $description = 'Sync data from Loxo API';

    public function handle()
    {
        try {
            $type = $this->option('type');

            $this->info("Syncing {$type} from Loxo API...");

            switch ($type) {
                case 'activity_types':
                    $data = Loxo::getActivityTypes();
                    break;

                case 'address_types':
                    $data = Loxo::getAddressTypes();
                    break;

                default:
                    $this->error("Unknown type: {$type}");
                    return 1;
            }

            $this->info("Successfully synced " . count($data) . " records");
            $this->table(['Key', 'Value'], [
                ['Records', count($data)],
                ['Domain', Loxo::getDomain()],
                ['Agency', Loxo::getAgencySlug()],
                ['Base URL', Loxo::getBaseUrl()]
            ]);

            return 0;
        } catch (Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
