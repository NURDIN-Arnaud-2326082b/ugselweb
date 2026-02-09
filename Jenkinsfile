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

        // --- PARTIE BACKEND (Déjà existante) ---
        stage('Backend: Dépendances') {
            agent {
                docker {
                    image 'composer:2'
                    args '-v /var/run/docker.sock:/var/run/docker.sock --entrypoint=""'
                }
            }
            steps {
                dir('backend') {
                    sh 'composer install --no-interaction --ignore-platform-reqs --no-scripts'
                }
            }
        }

        // --- NOUVELLE PARTIE FRONTEND ---
        stage('Frontend: Build') {
            agent {
                docker {
                    // Image Node.js officielle (LTS)
                    image 'node:20-alpine' 
                    // On mappe le socket docker par sécurité, même si moins critique ici
                    args '-v /var/run/docker.sock:/var/run/docker.sock'
                }
            }
            steps {
                dir('frontend') {
                    // 1. Installation des paquets (équivalent de composer install)
                    sh 'npm install'
                    
                    // 2. Vérification que le code compile (crée le dossier dist/build)
                    // Cela échouera si vous avez des erreurs de syntaxe React graves
                    sh 'npm run build'
                }
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('sonarqube-docker') {
                    // Le scanner va maintenant lire les propriétés mises à jour et analyser les 2 dossiers
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