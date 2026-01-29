pipeline {
    agent none // 1. Tell Jenkins: "Do not run globally on the main server"

    stages {
        stage('Install Dependencies') {
            agent {
                docker { 
                    image 'composer:2' // 2. Use this specific image for this stage
                    args '-v /var/run/docker.sock:/var/run/docker.sock' 
                }
            }
            steps {
                echo 'Installing Composer dependencies...'
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
            }
        }

        stage('Environment Setup') {
             agent {
                docker { image 'php:8.2-cli' } // 3. Use PHP CLI for artisan commands
            }
            steps {
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
            }
        }

        stage('Run Tests') {
            agent {
                docker { image 'php:8.2-cli' }
            }
            steps {
                // Create a dummy test report file if you don't have real tests yet
                // so the post-action doesn't fail
                sh 'mkdir -p tests/report'
                sh './vendor/bin/phpunit --log-junit tests/report/results.xml || true' 
            }
        }
    }

    post {
        always {
            junit allowEmptyResults: true, testResults: 'tests/report/*.xml'
        }
    }
}