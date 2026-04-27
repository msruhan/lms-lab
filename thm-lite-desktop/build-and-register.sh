#!/bin/bash
set -e

IMAGE_NAME="lms/thm-lite-desktop:v1"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKEND_DIR="${SCRIPT_DIR}/../../LMS-Backend"

echo "[*] Building ${IMAGE_NAME}"
docker build -t "${IMAGE_NAME}" "${SCRIPT_DIR}"
echo "[+] Build done"

if [ -d "${BACKEND_DIR}" ] && [ -f "${BACKEND_DIR}/artisan" ]; then
  echo "[*] Seeding LabMachine presets"
  (cd "${BACKEND_DIR}" && php artisan db:seed --class=LabMachineSeeder --force)

  echo "[*] Mark machine active and ready"
  php "${BACKEND_DIR}/artisan" tinker --execute="
    \$m = App\Models\LabMachine::where('docker_image','${IMAGE_NAME}')->first();
    if (\$m) { \$m->update(['is_active'=>true,'pull_status'=>'ready','pulled_at'=>now()]); echo 'Machine activated: '.\$m->name.PHP_EOL; }
    else { echo 'Machine not found. Seeder may have failed.'.PHP_EOL; }
  " 2>/dev/null || true
fi

echo ""
echo "[+] Finished"
echo "    Image: ${IMAGE_NAME}"
echo "    Next: Admin -> Lab Machines -> verify status ready"
