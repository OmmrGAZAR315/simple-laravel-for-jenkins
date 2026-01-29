# Jenkins Setup Guide

This guide provides detailed instructions for setting up Jenkins to run automated tests for this Laravel project.

## Prerequisites

- Jenkins installed and running (version 2.3+ recommended)
- Jenkins plugins:
  - Git Plugin
  - Pipeline Plugin
  - JUnit Plugin
  - Workspace Cleanup Plugin (optional)
- PHP 8.1+ installed on Jenkins server/agent
- Composer installed on Jenkins server/agent

## Jenkins Server Requirements

### Installing PHP on Jenkins Server

If PHP is not installed on your Jenkins server:

**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-mbstring php8.1-xml php8.1-sqlite3 php8.1-curl
```

**CentOS/RHEL:**
```bash
sudo yum install php81 php81-cli php81-mbstring php81-xml php81-sqlite3 php81-curl
```

### Installing Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

Verify installation:
```bash
php -v
composer --version
```

## Required Jenkins Plugins

Install the following plugins via **Manage Jenkins > Manage Plugins**:

1. **Git Plugin** - For SCM integration
2. **Pipeline Plugin** - For pipeline support
3. **JUnit Plugin** - For test result visualization
4. **Workspace Cleanup Plugin** - For cleaning workspace between builds (optional)

## Setting Up the Jenkins Job

### Option 1: Pipeline from SCM (Recommended)

1. **Create New Job**
   - Go to Jenkins Dashboard
   - Click "New Item"
   - Enter job name (e.g., "Laravel-Testing-Project")
   - Select "Pipeline"
   - Click "OK"

2. **Configure Pipeline**
   - In the "Pipeline" section, select "Pipeline script from SCM"
   - Choose "Git" as SCM
   - Enter your repository URL
   - Specify branch (e.g., `*/main` or `*/master`)
   - Set "Script Path" to `Jenkinsfile`

3. **Save Configuration**
   - Click "Save"

### Option 2: Direct Pipeline Script

1. **Create New Job**
   - Follow step 1 from Option 1

2. **Configure Pipeline**
   - In the "Pipeline" section, select "Pipeline script"
   - Copy and paste the contents of `Jenkinsfile` into the script editor

3. **Save Configuration**
   - Click "Save"

## Pipeline Configuration Details

The `Jenkinsfile` contains the following stages:

### 1. Checkout Stage
```groovy
stage('Checkout') {
    steps {
        checkout scm
    }
}
```
Pulls the latest code from your repository.

### 2. Install Dependencies Stage
```groovy
stage('Install Dependencies') {
    steps {
        sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
    }
}
```
Installs all Composer dependencies required for the project.

### 3. Environment Setup Stage
```groovy
stage('Environment Setup') {
    steps {
        sh 'cp .env.testing .env'
        sh 'php artisan key:generate'
    }
}
```
Sets up the environment file and generates the application key.

### 4. Run Tests Stage
```groovy
stage('Run Tests') {
    steps {
        sh './vendor/bin/phpunit --log-junit reports/junit.xml'
    }
}
```
Executes PHPUnit tests and generates JUnit XML report.

### 5. Post Actions
```groovy
post {
    always {
        junit 'reports/junit.xml'
    }
}
```
Archives test results for visualization in Jenkins UI.

## Environment Configuration

### Using .env.testing in Jenkins

The pipeline automatically copies `.env.testing` to `.env` during the Environment Setup stage. This ensures consistent testing configuration.

### Custom Environment Variables

If you need to override environment variables in Jenkins:

1. Go to job configuration
2. Under "Pipeline", click "Advanced"
3. Add environment variables in the "Environment variables" section

Example:
```
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## Test Reports

### Viewing Test Results

After running a build:

1. Go to the build page
2. Click "Test Result" in the left sidebar
3. View detailed test results, including:
   - Total tests run
   - Passed/Failed tests
   - Test execution time
   - Individual test details

### Test Result Trends

Jenkins automatically tracks test trends across builds, showing:
- Pass/fail rate over time
- Test execution time trends
- Flaky test detection

## Troubleshooting

### Issue: PHP Command Not Found

**Solution:** Ensure PHP is in the PATH for Jenkins:

1. Go to **Manage Jenkins > Configure System**
2. Under "Global properties", check "Environment variables"
3. Add `PATH` with value: `/usr/bin:/usr/local/bin:$PATH`

### Issue: Composer Command Not Found

**Solution:** Add Composer to PATH or use full path:

```groovy
sh '/usr/local/bin/composer install --no-interaction --prefer-dist --optimize-autoloader'
```

### Issue: Permission Denied on vendor/bin/phpunit

**Solution:** Ensure execute permissions:

```groovy
sh 'chmod +x vendor/bin/phpunit'
sh './vendor/bin/phpunit --log-junit reports/junit.xml'
```

### Issue: Tests Failing Due to Missing SQLite Extension

**Solution:** Install SQLite extension on Jenkins server:

```bash
sudo apt install php8.1-sqlite3
# or
sudo yum install php81-sqlite3
```

Restart Jenkins after installation.

### Issue: File Permission Errors

**Solution:** Ensure Jenkins has write permissions:

```groovy
stage('Setup Permissions') {
    steps {
        sh 'chmod -R 775 storage bootstrap/cache'
    }
}
```

## Advanced Configuration

### Running Tests in Docker

For better isolation, you can run tests in a Docker container:

```groovy
pipeline {
    agent {
        docker {
            image 'php:8.1-cli'
            args '-v composer-cache:/root/.composer'
        }
    }
    
    stages {
        // Your stages here
    }
}
```

### Parallel Testing

For faster execution, split tests across parallel stages:

```groovy
stage('Run Tests') {
    parallel {
        stage('Unit Tests') {
            steps {
                sh './vendor/bin/phpunit --testsuite Unit'
            }
        }
        stage('Feature Tests') {
            steps {
                sh './vendor/bin/phpunit --testsuite Feature'
            }
        }
    }
}
```

### Code Coverage Reports

To generate code coverage:

1. Install Xdebug on Jenkins server
2. Modify the test command:

```groovy
sh './vendor/bin/phpunit --coverage-html reports/coverage --log-junit reports/junit.xml'
```

3. Publish HTML reports using the HTML Publisher plugin

### Notifications

Add Slack/Email notifications:

```groovy
post {
    success {
        mail to: 'team@example.com',
             subject: "Build Success: ${env.JOB_NAME} - ${env.BUILD_NUMBER}",
             body: "Tests passed successfully!"
    }
    failure {
        mail to: 'team@example.com',
             subject: "Build Failed: ${env.JOB_NAME} - ${env.BUILD_NUMBER}",
             body: "Tests failed. Check console output."
    }
}
```

## Build Triggers

### Automatic Builds on Git Push

1. Go to job configuration
2. Under "Build Triggers", select "GitHub hook trigger for GITScm polling" (for GitHub)
   or "Poll SCM" for scheduled polling
3. For Poll SCM, use cron syntax: `H/5 * * * *` (every 5 minutes)

### Webhook Configuration (GitHub)

1. Go to your GitHub repository settings
2. Click "Webhooks"
3. Add webhook with:
   - Payload URL: `http://your-jenkins-server/github-webhook/`
   - Content type: `application/json`
   - Events: "Just the push event"

## Best Practices

1. **Clean Workspace**: Use workspace cleanup to avoid build pollution
2. **Dependency Caching**: Cache Composer dependencies for faster builds
3. **Timeout**: Set reasonable timeouts to avoid hanging builds
4. **Resource Limits**: Configure memory limits for PHP processes
5. **Security**: Store sensitive credentials in Jenkins credentials store

## Monitoring

### Build Health

Monitor build health metrics:
- Build success rate
- Average build duration
- Test pass rate
- Code coverage trends

### Alerts

Set up alerts for:
- Build failures
- Long-running builds
- Flaky tests

## Additional Resources

- [Jenkins Pipeline Documentation](https://www.jenkins.io/doc/book/pipeline/)
- [Jenkins PHP Guide](https://jenkins.io/solutions/php/)
- [PHPUnit Jenkins Integration](https://phpunit.de/manual/current/en/textui.html)
- [Jenkins Docker Guide](https://www.jenkins.io/doc/book/installing/docker/)
