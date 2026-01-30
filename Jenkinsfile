pipeline {
    agent none

    stages {
        stage('Install Dependencies') {
            agent {
                docker {
                    image 'composer:2'
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
                docker {
                    image 'php:8.2-cli'
                }
            }
            steps {
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
            }
        }

        stage('Run Tests') {
            agent {
                docker {
                    image 'php:8.2-cli'
                }
            }
            steps {
                sh 'mkdir -p tests/report'
                // The "|| true" ensures the build continues even if tests fail,
                // so the post-action can still record the results.
                sh './vendor/bin/phpunit --log-junit tests/report/results.xml || true'
            }
            // MOVE THE POST BLOCK HERE 👇
            post {
                always {
                    junit allowEmptyResults: true, testResults: 'tests/report/*.xml'
                }
            }
        }
        stage('Build & Tag Image') {
            agent {
                // We use a container that has Docker tools pre-installed
                docker {
                    image 'docker:latest'
                    // Important: We must share the socket again so it can talk to the main engine
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                script {
                    // 1. Build the image
                    // We tag it with the BUILD_NUMBER so every build is unique
                    sh "docker build -t my-laravel-app:${BUILD_NUMBER} ."

                    // 2. (Optional) Tag as 'latest' too
                    sh "docker tag my-laravel-app:${BUILD_NUMBER} my-laravel-app:latest"
                }
            }
        }
    }
}
