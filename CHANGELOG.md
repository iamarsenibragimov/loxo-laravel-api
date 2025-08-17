# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **New SMS API Endpoints:**
  - `getSms()` - Get SMS messages with filtering and pagination support
  - `createSms()` - Send new SMS messages with full parameter support
  - `getSmsById()` - Get specific SMS message by ID
- **Enhanced Communication Support:**
  - Complete SMS messaging functionality for the Loxo platform
  - Support for SMS filtering by date range and pagination
  - Comprehensive SMS creation with job and person associations
- **Documentation:**
  - New SMS example file (`examples/sms_example.php`)
  - Updated README.md with SMS usage examples
  - Updated API coverage from 26 to 29 endpoints (20.1% coverage)

## [1.1.1] - 2025-01-19

### Added
- **New API Endpoint:**
  - `createJob()` - Create new job positions with full parameter support
- **Enhanced Jobs Support:**
  - Complete POST `/api/{agency_slug}/jobs` endpoint implementation
  - Support for all job creation parameters including custom fields
  - Comprehensive validation and error handling
- **Documentation:**
  - New jobs example file (`examples/jobs_example.php`)
  - Updated API coverage documentation (18.8% coverage)
- **Testing:**
  - 5 new comprehensive unit tests for job creation functionality
  - Tests cover basic creation, full data, custom fields, and error scenarios

### Changed
- Jobs endpoint status updated from "partially implemented" to "fully implemented"
- API coverage increased from 26 to 27 implemented endpoints (18.1% → 18.8%)

### API Coverage
- **Total Loxo API Endpoints:** 144
- **Currently Implemented:** 27 (18.8%) - **+1 new endpoint**
- **Remaining:** 117 (81.3%)

## [1.1.0] - 2025-01-15

### Added
- **New API Endpoints (5 new endpoints implemented):**
  - `getUsers()` - Users listing
  - `getPersonEvents()` - Person events listing  
  - `createPersonEvent()` - Create person event
  - `getJobCandidates()` - Job candidates listing
  - `getJobCandidate()` - Get job candidate by ID
  - `getJobs()` - Jobs listing
  - `getPeople()` - People/candidates listing
  - `createPerson()` - Create person/candidate
  - `applyToJob()` - Apply to job
  - `createPersonEducationProfile()` - Create education profile
- Simple development testing tools
- Makefile with quick commands (`make quick`, `make help`, etc.)
- Standalone test script (`test-standalone.php`)
- Development bootstrap script (`dev-bootstrap.php`)
- Simple PHPUnit tests without Laravel dependencies
- Developer cheatsheet and simple testing guide
- New endpoint guide for contributors
- Comprehensive examples for people and education profiles endpoints

### Changed
- Renamed facade from `LoxoApi` to `Loxo` for simplicity
- Updated all documentation to use shorter facade name
- Enhanced API coverage documentation with current implementation status
- Updated `.gitignore` to include `.phpunit.cache` for improved build management

### Developer Experience
- Testing time reduced from 2-3 minutes to 5 seconds
- No need for separate Laravel test project
- 4 different testing methods for different use cases
- Instant feedback during development

### API Coverage
- **Total Loxo API Endpoints:** 144
- **Currently Implemented:** 26 (18.1%) - **+5 new endpoints**
- **Remaining:** 118 (81.9%)

## [1.0.0] - 2025-01-14

### Added
- Initial release of Loxo Laravel API Package (Unofficial)
- Support for Loxo API authentication
- `LoxoApiService` for API interactions
- `LoxoApi` facade for easy access
- Activity types endpoint support
- Address types endpoint support
- Generic HTTP methods (GET, POST, PUT, DELETE)
- Comprehensive error handling with custom exceptions
- Retry logic for failed requests
- Configuration file for customization
- PHPUnit tests
- Complete documentation

### Features
- Easy Laravel integration via ServiceProvider
- Configurable timeout and retry settings
- Type-safe API with contracts
- Comprehensive error handling
- Built-in retry mechanism for reliability
