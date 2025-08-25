pipeline {
    agent any

    stages {
        stage('Clone Repository') {
            steps {
                echo "📥 Cloning repository..."
                git branch: 'main', url: 'https://github.com/vilas1520/roshni.git'
            }
        }

        stage('Stop Old Containers') {
            steps {
                echo "🛑 Stopping old containers..."
                sh '''
                docker-compose down -v || true
                '''
            }
        }

        stage('Build & Deploy Containers') {
            steps {
                echo "🐳 Building and starting containers..."
                sh '''
                docker-compose up -d --build
                '''
            }
        }

        stage('Verify Deployment') {
            steps {
                echo "🔍 Verifying running containers..."
                sh '''
                docker ps
                curl -I http://localhost:8081 || true
                curl -I http://localhost:8082 || true
                '''
            }
        }
    }

    post {
        always {
            echo "✅ Pipeline finished!"
        }
        failure {
            echo "❌ Pipeline failed!"
        }
    }
}
