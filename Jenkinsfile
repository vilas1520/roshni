pipeline {
  agent any

  environment {
    REPO_URL = 'https://github.com/vilas1520/roshni.git'
    APP_PORT = '8081' // Avoids Jenkins port 8080
  }

  stages {
    stage('Checkout') {
      steps {
        echo "📥 Cloning repository..."
        git branch: 'main', url: REPO_URL
      }
    }

    stage('Docker Info') {
      steps {
        sh 'docker version || true'
        sh 'docker compose version || docker-compose --version || true'
        sh 'groups || id'
      }
    }

    stage('Build (Dockerfile)') {
      steps {
        echo "🐳 Building image from Dockerfile..."
        sh '''
          # Clean any old container/image named roshni-app to avoid conflicts
          docker ps -q --filter "name=roshni-app" | xargs -r docker stop
          docker ps -aq --filter "name=roshni-app" | xargs -r docker rm
          docker images -q roshni-app | xargs -r docker rmi

          docker build -t roshni-app .
        '''
      }
    }

    stage('Deploy (docker-compose)') {
      steps {
        echo "🚀 Starting stack with docker-compose..."
        sh '''
          export APP_PORT=''' + '"${APP_PORT}"' + '''
          export MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-rootpass}
          export MYSQL_DATABASE=${MYSQL_DATABASE:-ems_db}
          export MYSQL_USER=${MYSQL_USER:-ems_user}
          export MYSQL_PASSWORD=${MYSQL_PASSWORD:-ems_pass}

          # Bring down any previous stack (ignore errors)
          docker-compose down -v || true

          # Bring up fresh
          docker-compose up -d --build

          # Show status
          docker ps
        '''
      }
    }

    stage('Health Check') {
      steps {
        echo "🔍 Checking container is running..."
        sh 'docker ps --filter "name=roshni-app"'
      }
    }
  }

  post {
    success {
      echo "✅ Deployed! Visit: http://<EC2-PUBLIC-IP>:" + "${APP_PORT}"
    }
    failure {
      echo "❌ Deployment failed. Check console log above."
    }
  }
}
