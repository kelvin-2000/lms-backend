# API Testing with Jest

This directory contains unit tests for the API routes using Jest and Supertest.

## Setup

The tests are configured to run against a local instance of the API. Before running the tests, make sure you have:

1. All required packages installed:
   ```
   npm install
   ```

2. A running development server:
   ```
   php artisan serve
   ```

## Running Tests

To run all tests:
```
npm test
```

To run tests in watch mode (useful during development):
```
npm run test:watch
```

To generate coverage reports:
```
npm run test:coverage
```

## Test Files

- `auth.test.js` - Tests for authentication routes (register, login, logout)
- `courses.test.js` - Tests for course-related routes (CRUD operations, enrollments)
- `events.test.js` - Tests for event-related routes (CRUD operations, registrations)

## Adding New Tests

To add tests for additional API routes:

1. Create a new test file in this directory with the `.test.js` extension
2. Import necessary utilities from Jest and Supertest
3. Use the same pattern as existing tests for consistency:
   - Group related tests with `describe`
   - Use `beforeEach` for common setup
   - Test both successful and error cases
   - Verify responses with assertions

## Mocking

These tests use a combination of:

- Direct API calls using Supertest
- Mocked dependencies (if needed) using Jest's mocking features
- Authentication tokens obtained by login at the start of tests

## Best Practices

- Test both successful operations and error cases
- Verify status codes and response structures
- Create test data dynamically within tests
- Clean up test data after tests complete
- Group related test cases logically
- Use descriptive test names 