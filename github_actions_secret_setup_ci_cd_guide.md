# Setting Up GitHub Encrypted Secrets for Automated Turbify Deployment

Because `.env` is ignored by Git (as it should be), GitHub's cloud runners do not have access to your local `.env` file. We supply those variables securely using **GitHub Encrypted Secrets**.

---

## 1. Add Secrets to Your GitHub Repository

1. Open your browser and go to your repository:
   `https://github.com/trentdouthat/web-labs`
2. Along the top navigation tabs (Code, Issues, Pull requests, Actions, Projects, Wiki, Security, Insights, Settings), click **Settings**.
3. In the left-hand sidebar:
   * Look for the **Security** section.
   * Click **Secrets and variables** $\rightarrow$ select **Actions**.
4. You will see a green button: **"New repository secret"**.
5. Add the following three secrets one by one:

| Secret Name | Recommended Value | Description |
| :--- | :--- | :--- |
| **`TURBIFY_FTP_HOST`** | `ftp.trentdouthat.com` | Your Turbify FTP host endpoint |
| **`TURBIFY_FTP_USER`** | *(your cPanel FTP username)* | The username used in `.env` |
| **`TURBIFY_FTP_PASS`** | *(your alphanumeric password)* | The password used in `.env` |

---

## 2. Update Your Local `.github/workflows/deploy.yml`

On your **HP-Spectre** workstation:

1. Open `.github/workflows/deploy.yml` in VS Code.
2. Replace the empty file with the YAML contents from the generated file block.
3. Save the file.

---

## 3. Commit and Push to Trigger the Runner

In your terminal on the HP-Spectre:

```bash
cd ~/projects/web-labs
git add .github/workflows/deploy.yml
git commit -m "ci: configure automated FTPS deployment to Turbify"
git push origin master
```
*(or `git push origin main` depending on your active branch name).*

---

## 4. Watch the Live Deployment in GitHub

1. Immediately return to your browser on GitHub.
2. Click the **Actions** tab at the top of the repository.
3. You will see a live workflow run labeled **"ci: configure automated FTPS deployment to Turbify"**.
4. Click on it to expand the job and watch the terminal output stream live as the Azure VM boots Ubuntu, installs `lftp`, syncs to Turbify, and validates HTTP 200 on all endpoints.
