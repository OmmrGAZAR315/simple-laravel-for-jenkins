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

        //stage('Run Tests') {
        //    agent {
        //        docker {
        //            image 'php:8.2-cli'
        //        }
        //    }
        //    steps {
        //        sh 'mkdir -p tests/report'
        //        // The "|| true" ensures the build continues even if tests fail,
        //        // so the post-action can still record the results.
        //        sh './vendor/bin/phpunit --log-junit tests/report/results.xml || true'
        //    }
        //    post {
        //        always {
        //            junit allowEmptyResults: true, testResults: 'tests/report/*.xml'
        //        }
        //    }
        //}
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
                    echo "Building laravel Image"
                    sh 'docker build -t my-laravel-app:${BUILD_NUMBER} -f .'
                    sh 'docker tag my-laravel-app:${BUILD_NUMBER} my-laravel-app:latest'
                }
            }
        }
        stage('Build & Tag Nginx') {
            agent {
                docker {
                    image 'docker:latest'
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                script {
                    echo "Building Nginx Image"
                    sh 'docker build -t my-nginx:${BUILD_NUMBER} -f Dockerfile.nginx .'
                    sh 'docker tag my-nginx:${BUILD_NUMBER} my-nginx:latest'
                }
            }
        }

        stage('Deploy to Production') {
            agent {
                docker {
                    image 'docker:latest'
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                script {
                    sh 'docker compose down -v || true'
                    sh 'docker compose up -d'

                    sh 'sleep 10'
                    sh 'docker compose exec app -T php artisan mgirate --force'
                }
            }
        }
    }
}
