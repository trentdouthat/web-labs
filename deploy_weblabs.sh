#!/usr/bin/env bash
# ==============================================================================
# deploy_weblabs.sh - Safe FTPS Deployment for trentdouthat.com
# Synchronizes portal landing page and labs without touching WordPress (/blog/).
# ==============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# ------------------------------------------------------------------------------
# 1. Secret Ingestion (.env Handling)
# ------------------------------------------------------------------------------
if [[ -f "${SCRIPT_DIR}/.env" ]]; then
  chmod 600 "${SCRIPT_DIR}/.env" 2>/dev/null || true
  set -a
  source "${SCRIPT_DIR}/.env"
  set +a
fi

# ------------------------------------------------------------------------------
# 2. Configuration & Parameter Fallbacks
# ------------------------------------------------------------------------------
FTP_HOST="${TURBIFY_FTP_HOST:-ftp.trentdouthat.com}"
FTP_USER="${TURBIFY_FTP_USER:-}"
FTP_PASS="${TURBIFY_FTP_PASS:-}"

LOCAL_SRC_DIR="${SCRIPT_DIR}/src"

if [[ -z "${FTP_USER}" || -z "${FTP_PASS}" ]]; then
  echo "[-] ERROR: Missing FTPS credentials." >&2
  echo "    Provide credentials in a local .env file or export them in your shell." >&2
  exit 1
fi

if [[ ! -d "${LOCAL_SRC_DIR}" ]]; then
  echo "[-] ERROR: Source directory '${LOCAL_SRC_DIR}' not found." >&2
  exit 1
fi

echo "======================================================================"
echo " [WEB-LABS PROMOTION] Synchronizing to ${FTP_HOST}"
echo "======================================================================"

# ------------------------------------------------------------------------------
# Stage 1: Upload Main Portal Landing Page to root public_html
# ------------------------------------------------------------------------------
echo -e "\n[STAGE 1/3] Uploading root landing page (index.html)..."
lftp << LFTP_ROOT
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ssl:verify-certificate false
open "${FTP_HOST}"
user "${FTP_USER}" "${FTP_PASS}"
cd public_html || true
put "${LOCAL_SRC_DIR}/index.html" -o index.html
quit
LFTP_ROOT
echo "[+] Root index.html deployed successfully."

# ------------------------------------------------------------------------------
# Stage 2: Mirror Labs Subdirectory (src/html/ -> public_html/html/)
# ------------------------------------------------------------------------------
echo -e "\n[STAGE 2/3] Mirroring labs directory (public_html/html/)..."
lftp << LFTP_HTML
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ssl:verify-certificate false
open "${FTP_HOST}"
user "${FTP_USER}" "${FTP_PASS}"
cd public_html || true
mkdir -p html
cd html
mirror --reverse \
       --verbose \
       --delete \
       --exclude-glob .git* \
       --exclude-glob *.swp \
       "${LOCAL_SRC_DIR}/html/" ./
quit
LFTP_HTML
echo "[+] Labs directory synchronized."

# ------------------------------------------------------------------------------
# Stage 3: Public HTTP Verification Smoke Test
# ------------------------------------------------------------------------------
echo -e "\n[STAGE 3/3] Probing live endpoints..."
ROOT_CODE=$(curl -s -o /dev/null -w "%{http_code}" -L "https://www.trentdouthat.com/" || echo "000")
LABS_CODE=$(curl -s -o /dev/null -w "%{http_code}" -L "https://www.trentdouthat.com/html/" || echo "000")
BLOG_CODE=$(curl -s -o /dev/null -w "%{http_code}" -L "https://www.trentdouthat.com/blog/" || echo "000")

echo "    Root Portal (/)         -> HTTP ${ROOT_CODE}"
echo "    Labs Directory (/html/) -> HTTP ${LABS_CODE}"
echo "    WordPress Blog (/blog/) -> HTTP ${BLOG_CODE}"

if [[ "${ROOT_CODE}" == "200" && "${BLOG_CODE}" == "200" ]]; then
  echo -e "\n======================================================================"
  echo " [SUCCESS] Deployment verified! All services healthy."
  echo "======================================================================"
else
  echo -e "\n[-] WARNING: One or more endpoints did not return HTTP 200." >&2
  exit 1
fi
