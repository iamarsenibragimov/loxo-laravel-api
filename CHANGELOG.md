# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Simple development testing tools
- Makefile with quick commands (`make quick`, `make help`, etc.)
- Standalone test script (`test-standalone.php`)
- Development bootstrap script (`dev-bootstrap.php`)
- Simple PHPUnit tests without Laravel dependencies
- Developer cheatsheet and simple testing guide

### Changed
- Renamed facade from `LoxoApi` to `Loxo` for simplicity
- Updated all documentation to use shorter facade name

### Developer Experience
- Testing time reduced from 2-3 minutes to 5 seconds
- No need for separate Laravel test project
- 4 different testing methods for different use cases
- Instant feedback during development

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
