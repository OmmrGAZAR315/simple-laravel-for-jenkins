# Laravel Jenkins Testing Project

A simple Laravel 10.x project configured for Jenkins CI/CD testing. This project demonstrates how to set up automated testing for Laravel applications using Jenkins pipelines.

## Features

- **Laravel 10.3.3** - Latest LTS version
- **PHPUnit Testing** - Configured with SQLite in-memory database
- **Jenkins Pipeline** - Complete CI/CD pipeline configuration
- **Sample Tests** - Feature and Unit tests included
- **Fast Testing** - SQLite in-memory database for quick test execution

## Prerequisites

- PHP 8.1 or higher
- Composer
- Jenkins (for CI/CD)
- SQLite extension enabled

## Local Setup

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd <project-directory>
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Run Tests Locally

Run all tests:

```bash
php artisan test
```

Or use PHPUnit directly:

```bash
./vendor/bin/phpunit
```

Run specific test suites:

```bash
# Run only feature tests
./vendor/bin/phpunit --testsuite Feature

# Run only unit tests
./vendor/bin/phpunit --testsuite Unit
```

## Project Structure

```
.
├── app/                    # Application code
├── tests/
│   ├── Feature/           # Feature tests (HTTP, Database)
│   │   ├── ExampleTest.php
│   │   └── UserTest.php
│   └── Unit/              # Unit tests (Pure PHP logic)
│       ├── CalculatorTest.php
│       └── ExampleTest.php
├── Jenkinsfile            # Jenkins pipeline configuration
├── phpunit.xml            # PHPUnit configuration
├── .env.testing          # Testing environment variables
└── .env.example          # Example environment file
```

## Testing Configuration

### PHPUnit Configuration

The project uses `phpunit.xml` with the following key configurations:

- **Database**: SQLite in-memory (`:memory:`)
- **Cache**: Array driver (fast, no external dependencies)
- **Mail**: Array driver (no actual emails sent)
- **Sessions**: Array driver

### Environment Variables

The `.env.testing` file contains test-specific configuration:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
MAIL_MAILER=array
```

## Jenkins Integration

### Pipeline Stages

The `Jenkinsfile` defines the following stages:

1. **Checkout** - Pull code from repository
2. **Install Dependencies** - Run `composer install`
3. **Environment Setup** - Configure `.env` and generate application key
4. **Run Tests** - Execute PHPUnit tests with JUnit reporting

### Setting Up Jenkins

See [JENKINS_SETUP.md](JENKINS_SETUP.md) for detailed Jenkins configuration instructions.

### Quick Jenkins Setup

1. Create a new Pipeline job in Jenkins
2. Configure SCM (Git) to point to your repository
3. Set Pipeline definition to "Pipeline script from SCM"
4. Specify `Jenkinsfile` as the Script Path
5. Save and run the build

## Sample Tests

### Feature Tests

**UserTest.php** - Tests database interactions:
- User creation
- Database assertions
- HTTP responses

### Unit Tests

**CalculatorTest.php** - Tests pure PHP logic:
- Basic arithmetic operations
- String operations
- Assertions

## Adding More Tests

### Create a Feature Test

```bash
php artisan make:test NameTest
```

### Create a Unit Test

```bash
php artisan make:test NameTest --unit
```

## Troubleshooting

### SQLite Extension Not Found

Make sure SQLite is enabled in your `php.ini`:

```ini
extension=sqlite3
extension=pdo_sqlite
```

### Tests Failing in Jenkins

1. Check Jenkins console output for errors
2. Verify Composer dependencies are installed correctly
3. Ensure PHP version matches requirements (8.1+)
4. Check file permissions on the project directory

### Database Migration Issues

For tests requiring migrations:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class YourTest extends TestCase
{
    use RefreshDatabase;
    
    // Your tests...
}
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Jenkins Documentation](https://www.jenkins.io/doc/)
- [Laravel Testing Guide](https://laravel.com/docs/10.x/testing)
