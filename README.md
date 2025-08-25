# Roshni – Jenkins + Docker Deployment (EC2)

This bundle contains ready-to-use files to build and deploy the repo **vilas1520/roshni** on an Ubuntu EC2 using Jenkins and Docker.

## Files
- `Dockerfile` – PHP 8.1 + Apache, mysqli, rewrite, permissions, DirectoryIndex.
- `docker-compose.yml` – App (port 8081), MySQL 5.7, phpMyAdmin (8082). Uses `.env` for secrets/ports.
- `Jenkinsfile` – Pipeline that checks out from your repo, builds, brings the stack up, and avoids port 8080 conflicts.
- `.env` – Default passwords/ports (change in production).

## Quick Start (once per server)
```bash
# Install
sudo apt update && sudo apt install -y docker.io docker-compose openjdk-17-jre git
sudo systemctl enable docker && sudo systemctl start docker

# Install Jenkins (if not installed)
# ... follow official steps or your previous setup

# Allow Jenkins to use Docker
sudo usermod -aG docker jenkins
sudo systemctl restart jenkins
```

## Run Pipeline
1. Commit these files to your repo root.
2. In Jenkins: New Item → Pipeline → "Pipeline script from SCM" (Git) → URL `https://github.com/vilas1520/roshni.git` → Script Path `Jenkinsfile`.
3. Build the job.
4. Access:
   - App: `http://<EC2-PUBLIC-IP>:8081`
   - phpMyAdmin: `http://<EC2-PUBLIC-IP>:8082` (Host: `db`, user/pass from `.env`)

## Notes
- If the repo keeps index.php in a subfolder (e.g., `public/`), update the Dockerfile COPY path accordingly.
- If Jenkins still says permission denied to Docker, ensure the Jenkins service has the new group membership (reboot the server if needed).
