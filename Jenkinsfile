pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out code...'
                checkout scm
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Installing Composer dependencies...'
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
            }
        }
        
        stage('Environment Setup') {
            steps {
                echo 'Setting up environment...'
                sh 'cp .env.testing .env'
                sh 'php artisan key:generate'
            }
        }
        
        stage('Run Tests') {
            steps {
                echo 'Running PHPUnit tests...'
                sh './vendor/bin/phpunit --log-junit reports/junit.xml'
            }
        }
    }
    
    post {
        always {
            echo 'Archiving test results...'
            junit 'reports/junit.xml'
        }
        success {
            echo 'Pipeline completed successfully!'
        }
        failure {
            echo 'Pipeline failed!'
        }
    }
}
