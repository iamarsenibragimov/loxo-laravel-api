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

debug-config: ## Show current configuration
	@php -r "require 'dev-bootstrap.php'; debugConfig();"

activity-types: ## Test activity types endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testActivityTypes');"

address-types: ## Test address types endpoint  
	@php -r "require 'dev-bootstrap.php'; measureTime('testAddressTypes');"

jobs: ## Test jobs endpoint
	@php -r "require 'dev-bootstrap.php'; measureTime('testJobs');"

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