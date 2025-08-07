# Quality Testing Guide - Minimal Boot Framework

## 🎯 Current Status

### ✅ Working Well
- **Unit Tests**: 122/125 passing (97.6% success rate)
- **Test Infrastructure**: Robust PHPUnit setup with in-memory SQLite
- **Code Quality Tools**: PHP_CodeSniffer and PHPStan configured

### ⚠️ Areas for Improvement
- **Integration Tests**: 3 failing tests (template expectations)
- **Code Style**: Minor issues in asset files
- **Static Analysis**: 28 type-related issues

## 🧪 Running Tests

### Basic Test Commands
```bash
# Run all unit tests
composer test

# Run unit tests only
./vendor/bin/phpunit --testsuite Unit

# Run integration tests only
./vendor/bin/phpunit --testsuite Integration

# Run with coverage (requires Xdebug)
XDEBUG_MODE=coverage composer test-coverage
```

### Code Quality Checks
```bash
# Check code style
composer cs-check

# Fix code style automatically
composer cs-fix

# Run static analysis
composer static-analysis

# Run all quality checks
composer check-all
```

## 🔧 Test Structure

### Unit Tests (`tests/Unit/`)
- **Core**: Database, configuration, logging tests
- **Page**: Handler and service tests
- **Shared**: Utility and service tests
- **User**: Authentication and user management tests

### Integration Tests (`tests/Integration/`)
- **App**: Full application flow tests
- **Page**: End-to-end page rendering tests

### Test Base Class (`tests/TestCase.php`)
- Provides database setup with in-memory SQLite
- Container configuration for dependency injection
- Helper methods for HTTP requests and responses
- Automatic test data seeding

## 🎯 Quality Metrics

### Current Test Results
```
Tests: 125 total
✅ Passing: 122 (97.6%)
❌ Failing: 3 (integration tests)
⚠️  Warnings: 1 (minor directory issue)
⏭️  Skipped: 1 (intentional)
```

### Code Coverage
- HTML reports: `var/coverage/html/`
- Clover format: `coverage.xml`
- Excludes: Assets, templates, factories, config providers

## 🚀 Improvement Recommendations

### 1. Fix Integration Tests (Priority: High)
The 3 failing integration tests need template expectation updates:

```php
// Current issue: Tests expect simple template data
// Actual: IndexHandler passes debug data

// Fix: Update test expectations to include debug data
$this->template
    ->expects($this->once())
    ->method('render')
    ->with('bootstrap_pages::home', [
        'debug_theme' => 'bootstrap',
        'debug_template' => 'bootstrap_pages::home',
        // ... other expected data
    ]);
```

### 2. Enable Code Coverage (Priority: Medium)
```bash
# Install Xdebug if not available
sudo apt-get install php-xdebug

# Run tests with coverage
XDEBUG_MODE=coverage composer test-coverage

# View HTML coverage report
open var/coverage/html/index.html
```

### 3. Fix Code Style Issues (Priority: Low)
```bash
# Auto-fix most issues
composer cs-fix

# Manual fixes needed for:
# - Long lines in config files
# - Missing docblocks
```

### 4. Address Static Analysis Issues (Priority: Medium)
```bash
# Focus on:
# - Type casting in config files
# - Missing type annotations
# - Remove unused methods
```

## 📊 Continuous Integration

### Pre-commit Checks
```bash
# Run before committing
composer pre-commit
```

### Full CI Pipeline
```bash
# Complete quality check
composer ci
```

## 🎯 Testing Best Practices

### Writing New Tests
1. **Unit Tests**: Test individual classes/methods in isolation
2. **Integration Tests**: Test component interactions
3. **Use Mocks**: For external dependencies
4. **Test Edge Cases**: Empty inputs, null values, exceptions
5. **Descriptive Names**: `testHandleReturns404WhenPageNotFound()`

### Test Data Management
- Use in-memory SQLite for speed
- Seed test data in `TestCase::seedTestData()`
- Clean up after each test automatically

### Assertions
```php
// Use specific assertions
$this->assertInstanceOf(HtmlResponse::class, $response);
$this->assertEquals(200, $response->getStatusCode());
$this->assertStringContainsString('expected', $body);

// Custom assertions available in TestCase
$this->assertResponseStatus(200, $response);
$this->assertResponseContains('content', $response);
$this->assertResponseHeader('Content-Type', 'text/html', $response);
```

## 🔍 Debugging Tests

### Common Issues
1. **Constructor Errors**: Check dependency injection setup
2. **Database Issues**: Verify test database seeding
3. **Mock Failures**: Ensure all expected method calls are defined
4. **Template Issues**: Check template expectations match actual calls

### Debug Commands
```bash
# Run single test with verbose output
./vendor/bin/phpunit --filter testMethodName --verbose

# Debug with var_dump (will show in risky tests)
var_dump($actualData); // In test method
```

## 📈 Next Steps

1. **Immediate**: Fix 3 failing integration tests
2. **Short-term**: Enable code coverage reporting
3. **Medium-term**: Address static analysis issues
4. **Long-term**: Add performance and security tests

---

**Status**: Quality testing infrastructure is solid with 97.6% test success rate. Minor fixes needed for full green build.
