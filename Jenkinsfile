pipeline {
    agent any

    stages {
        stage('Clone Repository') {
            steps {
                echo "📥 Cloning repository..."
                git branch: 'main', url: 'https://github.com/vilas1520/roshni.git'
            }
        }

        stage('Build Docker Images') {
            steps {
                echo "🐳 Building Docker images..."
                sh 'docker-compose build'
            }
        }

        stage('Deploy Containers') {
            steps {
                echo "🚀 Deploying containers..."
                sh 'docker-compose down'
                sh 'docker-compose up -d'
            }
        }

        stage('Verify Deployment') {
            steps {
                echo "🔍 Checking running containers..."
                sh 'docker ps -a'
            }
        }
    }

    post {
        always {
            echo "✅ Pipeline finished!"
        }
    }
}
