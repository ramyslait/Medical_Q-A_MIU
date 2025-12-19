# Test Suite Setup Guide

## Requirements

- **PHP 8.1 or higher** (required for PHPUnit 10+)
- **Composer** (for dependency management)

## Installation

1. **Install/Update Dependencies**
   ```bash
   composer install
   ```

   If you're using PHP 8.0, PHPUnit 10 won't install. You have two options:
   
   **Option A: Upgrade PHP to 8.1+** (Recommended)
   - PHPUnit 10 requires PHP 8.1+
   - This is the recommended approach for modern PHP development
   
   **Option B: Use PHPUnit 9.6** (Temporary)
   - The tests are compatible with PHPUnit 9.6
   - Update `composer.json` to use `"phpunit/phpunit": "^9.6"`
   - Run `composer update phpunit/phpunit`

2. **Verify Installation**
   ```bash
   vendor/bin/phpunit --version
   ```

## Running Tests

### Run All Tests
```bash
vendor/bin/phpunit
```

### Run Specific Test File
```bash
vendor/bin/phpunit tests/Controllers/LoginControllerTest.php
```

### Run with Coverage Report
```bash
vendor/bin/phpunit --coverage-html coverage
```

### Run with Verbose Output
```bash
vendor/bin/phpunit --verbose
```

## Test Structure

All tests follow PSR-4 autoloading standards:
- Base test class: `Tests\TestCase`
- Controller tests: `Tests\Controllers\*ControllerTest`

## Notes

- Tests use mocked databases by default (no actual database connection needed)
- Session and cookie handling is managed by the `TestCase` base class
- Environment variables for testing are defined in `phpunit.xml`
- The `PHPUNIT_RUNNING` constant prevents actual redirects during tests

## Troubleshooting

### "PHP version does not satisfy requirement"
- Upgrade to PHP 8.1+ for PHPUnit 10
- Or use PHPUnit 9.6 with PHP 8.0

### "Class not found" errors
- Run `composer dump-autoload` to regenerate autoloader

### Session errors
- The `TestCase` class handles session initialization automatically

### Mock method() errors (in IDE/linter)
- These are false positives from static analysis tools
- PHPUnit's mock API is correct and will work at runtime
- The code is valid and will execute properly

