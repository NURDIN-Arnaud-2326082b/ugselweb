pipeline {
    agent any

    environment {
        SCANNER_HOME = tool 'scanner-cli'
    }

    stages {
        stage('Préparation Code') {
            steps {
                echo 'Récupération du projet...'
            }
        }

        stage('Installation Dépendances') {
            agent {
                docker {
                    image 'composer:2'
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                dir('backend') {
                    sh 'composer install --no-interaction --ignore-platform-reqs'
                }
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('sonarqube-docker') {
                    sh "${SCANNER_HOME}/bin/sonar-scanner"
                }
            }
        }
        
        stage('Quality Gate') {
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }
    }
}