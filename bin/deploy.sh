#!/usr/bin/env bash
# Horror-Bark production deploy.
#
# This is the canonical way to bring up the prod stack on the server.
# Always invokes both compose files so the Coolify/Traefik integration
# (coolify external network, Traefik labels, restart policies) is applied.
#
# Usage:
#   ./bin/deploy.sh              # pull & up
#   ./bin/deploy.sh --no-pull    # up only (use when deploying uncommitted changes)
#   ./bin/deploy.sh --rebuild    # force a rebuild of nginx/php images
#
# Run from the repo root (or anywhere — the script cds to its own location).

set -euo pipefail

# Resolve repo root regardless of where the script is invoked from.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${REPO_ROOT}"

PULL=1
BUILD_FLAG=()
for arg in "$@"; do
  case "${arg}" in
    --no-pull) PULL=0 ;;
    --rebuild) BUILD_FLAG=(--build) ;;
    -h|--help) sed -n '2,15p' "$0"; exit 0 ;;
    *) echo "unknown flag: ${arg}" >&2; exit 2 ;;
  esac
done

if [[ "${PULL}" -eq 1 ]]; then
  echo "==> git pull"
  git pull --ff-only
fi

echo "==> docker compose up -d"
docker compose \
  -f docker-compose.yml \
  -f docker-compose.prod.yml \
  up -d "${BUILD_FLAG[@]}"

echo "==> health check"
sleep 2
status="$(curl -sS -o /dev/null -w '%{http_code}' --max-time 15 https://horrorbark.rayyanameez.com/ || echo 000)"
if [[ "${status}" == "200" ]]; then
  echo "OK — https://horrorbark.rayyanameez.com/ returned 200"
else
  echo "WARN — health check returned ${status} (expected 200)" >&2
  exit 1
fi
