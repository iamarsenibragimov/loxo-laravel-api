<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Loxo\LaravelApi\Services\LoxoApiService;
use Loxo\LaravelApi\Exceptions\LoxoApiException;

// Example: Working with Jobs API
// 
// This example demonstrates how to use the Loxo API to:
// 1. Get existing jobs
// 2. Create a new job

echo "📋 Loxo Jobs API Examples\n";
echo "========================\n\n";

try {
    // Initialize the API service
    $loxo = new LoxoApiService();
    
    echo "1. Getting existing jobs...\n";
    
    // Get jobs with filtering
    $jobsParams = [
        'per_page' => 5,
        'published' => true,
        'remote_work_allowed' => true,
        'query' => 'developer'
    ];
    
    $jobs = $loxo->getJobs($jobsParams);
    
    if (isset($jobs['jobs']) && count($jobs['jobs']) > 0) {
        echo "Found " . count($jobs['jobs']) . " jobs:\n";
        foreach ($jobs['jobs'] as $job) {
            echo "  - #{$job['id']}: {$job['title']}\n";
        }
    } else {
        echo "No jobs found matching criteria.\n";
    }
    
    echo "\n2. Creating a new job...\n";
    
    // Create a new job
    $newJobData = [
        'job' => [
            'title' => 'Senior Full Stack Developer',
            'published_name' => 'Senior Full Stack Developer - Remote',
            'description' => 'We are looking for an experienced full stack developer to join our growing team. 
            
Key responsibilities:
- Develop and maintain web applications using modern technologies
- Collaborate with cross-functional teams
- Mentor junior developers
- Participate in code reviews and technical discussions

Required skills:
- 5+ years of experience with JavaScript, React, and Node.js
- Strong knowledge of databases (PostgreSQL, MongoDB)
- Experience with cloud platforms (AWS, GCP, or Azure)
- Excellent problem-solving skills',
            'active' => true,
            'published' => true,
            'published_at' => date('c'), // Current timestamp in ISO 8601 format
            'published_end_date' => date('c', strtotime('+30 days')), // 30 days from now
            'company_id' => 123, // Replace with actual company ID
            'company_hidden' => false,
            'raw_company_name' => 'TechCorp Solutions',
            'job_type_id' => 1, // Replace with actual job type ID
            'job_status_id' => 1, // Replace with actual job status ID
            'job_category_ids' => [1, 2], // Replace with actual category IDs
            'owner_emails' => ['hiring@techcorp.com', 'hr@techcorp.com'],
            'remote_work_allowed' => true,
            'salary' => '$120,000 - $160,000',
            'address' => '123 Innovation Drive',
            'city' => 'San Francisco',
            'state_id' => 1, // Replace with actual state ID
            'country_id' => 1, // Replace with actual country ID
            'zip' => '94105',
            'internal_notes' => 'High priority position - looking for senior level candidates only',
            
            // Custom fields examples (replace with your agency's actual custom fields)
            'priority_level' => 'high',
            'tech_stack' => ['React', 'Node.js', 'PostgreSQL', 'AWS'],
            'seniority_level' => 'senior',
            'department' => 'Engineering'
        ]
    ];
    
    $createdJob = $loxo->createJob($newJobData);
    
    if (isset($createdJob['job'])) {
        $job = $createdJob['job'];
        echo "✅ Successfully created job!\n";
        echo "   Job ID: {$job['id']}\n";
        echo "   Title: {$job['title']}\n";
        echo "   Published: " . ($job['published'] ? 'Yes' : 'No') . "\n";
        echo "   Remote: " . ($job['remote_work_allowed'] ? 'Yes' : 'No') . "\n";
        echo "   Company ID: {$job['company_id']}\n";
        echo "   Created: {$job['created_at']}\n";
    } else {
        echo "❌ Failed to create job - unexpected response format\n";
    }
    
    echo "\n3. Getting job candidates for a specific job...\n";
    
    // Get candidates for the first job we found or the one we just created
    $jobId = isset($createdJob['job']['id']) ? $createdJob['job']['id'] : 
             (isset($jobs['jobs'][0]['id']) ? $jobs['jobs'][0]['id'] : null);
    
    if ($jobId) {
        $candidatesParams = [
            'per_page' => 3
        ];
        
        $candidates = $loxo->getJobCandidates($jobId, $candidatesParams);
        
        if (isset($candidates['candidates']) && count($candidates['candidates']) > 0) {
            echo "Found " . count($candidates['candidates']) . " candidates for job #{$jobId}:\n";
            foreach ($candidates['candidates'] as $candidate) {
                $person = $candidate['person'] ?? [];
                $name = ($person['first_name'] ?? '') . ' ' . ($person['last_name'] ?? '');
                echo "  - {$name} ({$person['email'] ?? 'No email'})\n";
            }
        } else {
            echo "No candidates found for job #{$jobId}.\n";
        }
    } else {
        echo "No job ID available to check candidates.\n";
    }
    
} catch (LoxoApiException $e) {
    echo "❌ Loxo API Error: " . $e->getMessage() . "\n";
    echo "Status Code: " . $e->getStatusCode() . "\n";
    
    if ($e->getResponseData()) {
        echo "Response Data: " . json_encode($e->getResponseData(), JSON_PRETTY_PRINT) . "\n";
    }
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}

echo "\n📝 Notes:\n";
echo "- Make sure to replace placeholder IDs (company_id, job_type_id, etc.) with actual values from your Loxo instance\n";
echo "- Custom field names (priority_level, tech_stack, etc.) should match your agency's configuration\n";
echo "- Published jobs will be visible to candidates if your job board is public\n";
echo "- Use internal_notes for information that should only be visible to your team\n";
