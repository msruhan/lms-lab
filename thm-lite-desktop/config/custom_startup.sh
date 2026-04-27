#!/usr/bin/env bash
set -euo pipefail

LOG_DIR="/var/log/thm-lite"
mkdir -p "${LOG_DIR}"

# Keep desktop simple: remove noisy shortcuts if they exist.
DESKTOP_DIR="/home/kasm-user/Desktop"
if [ -d "${DESKTOP_DIR}" ]; then
  find "${DESKTOP_DIR}" -maxdepth 1 -type f -name '*.desktop' \
    ! -iname '*firefox*' \
    ! -iname '*terminal*' \
    -delete || true
fi

exit 0
