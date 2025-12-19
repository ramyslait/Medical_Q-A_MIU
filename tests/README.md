# PHPUnit Test Suite

This directory contains the complete PHPUnit test suite for the Medical Q&A application.

## Structure

```
tests/
├── TestCase.php                    # Base test case with common utilities
├── Controllers/
│   ├── LoginControllerTest.php
│   ├── RegisterControllerTest.php
│   ├── QuestionControllerTest.php
│   ├── ForgotPasswordControllerTest.php
│   ├── ResetPasswordControllerTest.php
│   ├── SubmitResetCodeControllerTest.php
│   ├── VerifyEmailControllerTest.php
│   ├── ApproveAiAnswerControllerTest.php
│   └── LogoutControllerTest.php
└── README.md
```

## Setup

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Update PHPUnit** (if needed)
   ```bash
   composer require --dev phpunit/phpunit:^10.5
   ```

3. **Configure Environment**
   - The `phpunit.xml` file includes test environment variables
   - For actual database tests, you may need to set up a test database

## Running Tests

### Run All Tests
```bash
vendor/bin/phpunit
```

### Run Specific Test Suite
```bash
vendor/bin/phpunit tests/Controllers/LoginControllerTest.php
```

### Run with Coverage
```bash
vendor/bin/phpunit --coverage-html coverage
```

### Run with Verbose Output
```bash
vendor/bin/phpunit --verbose
```

## Test Coverage

The test suite covers all controllers in the application:

1. **LoginControllerTest** - Tests user authentication, session management, cookie encryption, and role-based redirects
2. **RegisterControllerTest** - Tests user registration, validation, email verification, and role assignment
3. **QuestionControllerTest** - Tests question submission, AI answer generation, and validation
4. **ForgotPasswordControllerTest** - Tests password reset request flow
5. **ResetPasswordControllerTest** - Tests password reset completion
6. **SubmitResetCodeControllerTest** - Tests reset code verification
7. **VerifyEmailControllerTest** - Tests email verification flow
8. **ApproveAiAnswerControllerTest** - Tests AI answer approval/rejection by admins/doctors
9. **LogoutControllerTest** - Tests session and cookie cleanup on logout

## Test Features

### Base TestCase Class

The `TestCase` class provides:
- Mock PDO and PDOStatement setup
- Session management helpers
- Request simulation (GET/POST)
- Cookie management
- Database result mocking
- Output capturing
- Session assertion helpers

### Common Test Patterns

Each test follows these patterns:
- **Arrange**: Set up test data and mocks
- **Act**: Execute the code being tested
- **Assert**: Verify expected behavior

### Mocking Strategy

- Database operations are mocked using PHPUnit's mock objects
- External API calls (Groq) would need to be mocked in integration tests
- Email sending is tested through function return values

## Notes

### Controller Testing Limitations

The current controllers use procedural code with direct includes and global state. For more comprehensive testing, consider:

1. **Refactoring controllers** to use dependency injection
2. **Extracting business logic** into service classes
3. **Using a test database** for integration tests
4. **Mocking external services** (email, APIs) more thoroughly

### Environment Variables

Test environment variables are set in `phpunit.xml`. These are separate from your `.env` file and are used only during testing.

### Session Handling

Tests use PHP's native session functions. The `TestCase` class manages session state between tests to prevent interference.

## Best Practices

1. **Isolation**: Each test should be independent and not rely on other tests
2. **Naming**: Test methods should clearly describe what they're testing
3. **Assertions**: Use specific assertions (assertEquals, assertTrue, etc.) rather than generic ones
4. **Comments**: Each test includes comments explaining its purpose
5. **Coverage**: Aim for high code coverage, especially for critical paths

## Troubleshooting

### Tests Fail with "Headers Already Sent"
- Ensure output buffering is properly managed
- Check that controllers don't output before headers

### Session Errors
- Make sure `session_start()` is called before accessing `$_SESSION`
- The `TestCase` class handles session initialization

### Database Connection Errors
- Tests use mocked databases by default
- For integration tests, set up a separate test database

## Contributing

When adding new controllers:
1. Create a corresponding test file in `tests/Controllers/`
2. Extend the `TestCase` class
3. Follow the existing test patterns
4. Add comments explaining each test case
5. Ensure all public methods are tested

