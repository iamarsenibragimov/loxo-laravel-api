# Loxo API Package Development Makefile

.PHONY: help test test-simple test-mock test-real install clean lint

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	composer install

test: ## Run all tests
	@echo "🧪 Running all tests..."
	@make test-simple
	@make test-mock
	@echo "✅ All tests completed!"

test-simple: ## Run simple PHPUnit tests
	@echo "🔧 Running simple tests..."
	vendor/bin/phpunit tests/SimpleTest.php

test-mock: ## Test with mock data
	@echo "🎭 Running PHPUnit tests..."
	vendor/bin/phpunit tests/SimpleTest.php

test-real: ## Test with real API (requires valid credentials)
	@echo "🌐 Testing with real API..."
	php dev-bootstrap.php

quick: ## Quick test all endpoints
	@echo "⚡ Quick test all endpoints..."
	@php dev-bootstrap.php

test-geography: ## Test all geography endpoints
	@echo "🌍 Testing geography endpoints..."
	@make countries
	@make states
	@make cities
	@make currencies

test-company: ## Test all company endpoints
	@echo "🏢 Testing company endpoints..."
	@make company-global-statuses
	@make company-types

test-data-management: ## Test all data management endpoints
	@echo "📊 Testing data management endpoints..."
	@make merges
	@make question-types
	@make social-profile-types
	@make education-types

test-demographics: ## Test all demographics endpoints
	@echo "👥 Testing demographics endpoints..."
	@make genders
	@make ethnicities
	@make diversity-types
	@make disability-statuses

test-compensation: ## Test all compensation endpoints
	@echo "💰 Testing compensation endpoints..."
	@make fee-types
	@make equity-types
	@make compensation-types

test-communication: ## Test all communication endpoints
	@echo "📧 Testing communication endpoints..."
	@make email-types
	@make email-tracking
	@make sms

test-configuration: ## Test all configuration endpoints
	@echo "⚙️ Testing configuration endpoints..."
	@make seniority-levels
	@make scorecard-types
	@make pronouns
	@make phone-types
	@make person-types
	@make person-share-field-types
	@make person-lists

test-new-endpoints: ## Test all newly implemented endpoints
	@echo "🆕 Testing all new endpoints..."
	@make test-geography
	@make test-company
	@make test-data-management
	@make test-demographics
	@make test-compensation
	@make test-communication
	@make test-configuration

debug-config: ## Show current configuration
	@php -r "require 'dev-bootstrap.php'; debugConfig();"

activity-types: ## Test activity types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testActivityTypes');"

address-types: ## Test address types endpoint  
	@php -r "require 'dev-bootstrap.php'; measureTime('testAddressTypes');"

seniority-levels: ## Test seniority levels endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testSeniorityLevels');"

scorecard-types: ## Test scorecard types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testScorecardTypes');"

pronouns: ## Test pronouns endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testPronouns');"

phone-types: ## Test phone types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testPhoneTypes');"

person-types: ## Test person types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testPersonTypes');"

person-share-field-types: ## Test person share field types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testPersonShareFieldTypes');"

person-lists: ## Test person lists endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testPersonLists');"

sms: ## Test SMS endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testSms');"

jobs: ## Test jobs endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testJobs');"

people: ## Test people endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testPeople');"

create-person: ## Test create person endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCreatePerson');"

apply-job: ## Test apply to job endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testApplyToJob');"

countries: ## Test countries endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCountries');"

states: ## Test states endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testStates');"

cities: ## Test cities endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCities');"

currencies: ## Test currencies endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCurrencies');"

company-global-statuses: ## Test company global statuses endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCompanyGlobalStatuses');"

company-types: ## Test company types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCompanyTypes');"

merges: ## Test merges endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testMerges');"

question-types: ## Test question types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testQuestionTypes');"

social-profile-types: ## Test social profile types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testSocialProfileTypes');"

education-types: ## Test education types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testEducationTypes');"

genders: ## Test genders endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testGenders');"

ethnicities: ## Test ethnicities endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testEthnicities');"

diversity-types: ## Test diversity types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testDiversityTypes');"

fee-types: ## Test fee types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testFeeTypes');"

equity-types: ## Test equity types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testEquityTypes');"

compensation-types: ## Test compensation types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testCompensationTypes');"

email-types: ## Test email types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testEmailTypes');"

email-tracking: ## Test email tracking endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testEmailTracking');"

disability-statuses: ## Test disability statuses endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testDisabilityStatuses');"

lint: ## Check code style
	@if [ -f vendor/bin/phpcs ]; then \
		vendor/bin/phpcs src/ --standard=PSR12; \
	else \
		echo "PHPCodeSniffer not installed. Run: composer require --dev squizlabs/php_codesniffer"; \
	fi

clean: ## Clean vendor directory
	rm -rf vendor/
	rm -f composer.lock

package-info: ## Show package information
	@echo "📦 Loxo Laravel API Package (Unofficial)"
	@echo "========================================"
	@echo "Package: iamarsenibragimov/loxo-laravel-api"
	@echo "Version: dev-main"
	@echo "PHP Version: $$(php -v | head -n 1)"
	@echo "Composer Version: $$(composer --version)"
	@echo ""
	@echo "Files:"
	@find src/ -name "*.php" | wc -l | xargs echo "  PHP files:"
	@find tests/ -name "*.php" | wc -l | xargs echo "  Test files:"