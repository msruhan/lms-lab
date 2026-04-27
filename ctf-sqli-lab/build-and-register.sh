#!/bin/bash
# ============================================================
# CTF SQLi Lab v2 — Build & Register ke LMS
# ============================================================
# Jalankan script ini dari folder ctf-sqli-lab:
#   cd LMS-Lab/ctf-sqli-lab
#   chmod +x build-and-register.sh
#   ./build-and-register.sh
#
# Atau dengan opsi:
#   ./build-and-register.sh --no-seed   (skip seeder Laravel)
#   ./build-and-register.sh --push      (push image ke registry)
# ============================================================

set -e

IMAGE_NAME="lms/ctf-sqli-lab"
IMAGE_TAG="v2"
FULL_IMAGE="${IMAGE_NAME}:${IMAGE_TAG}"

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

log()    { echo -e "${CYAN}[*]${NC} $1"; }
ok()     { echo -e "${GREEN}[+]${NC} $1"; }
warn()   { echo -e "${YELLOW}[!]${NC} $1"; }
error()  { echo -e "${RED}[-]${NC} $1"; }
header() { echo -e "\n${BOLD}${CYAN}$1${NC}"; echo "$(printf '─%.0s' {1..50})"; }

# ── Parse argumen ──────────────────────────────────────────
RUN_SEED=true
PUSH_IMAGE=false
for arg in "$@"; do
  case $arg in
    --no-seed) RUN_SEED=false ;;
    --push)    PUSH_IMAGE=true ;;
  esac
done

header "CTF SQLi Lab v2 — Build & Register"

# ── 1. Cek Docker tersedia ─────────────────────────────────
log "Mengecek Docker..."
if ! command -v docker &>/dev/null; then
  error "Docker tidak ditemukan. Install Docker terlebih dahulu."
  exit 1
fi
if ! docker info &>/dev/null; then
  error "Docker daemon tidak berjalan. Jalankan Docker terlebih dahulu."
  exit 1
fi
ok "Docker tersedia: $(docker --version)"

# ── 2. Pastikan kita di folder yang benar ──────────────────
if [ ! -f "Dockerfile" ]; then
  error "Dockerfile tidak ditemukan. Pastikan Anda berada di folder ctf-sqli-lab."
  exit 1
fi

# ── 3. Build Docker image ──────────────────────────────────
header "Step 1: Build Docker Image"
log "Building ${FULL_IMAGE} ..."
log "Ini mungkin memakan waktu 3-10 menit (download Ubuntu 24.04 + packages)."
echo ""

docker build \
  --tag "${FULL_IMAGE}" \
  --label "maintainer=LMS CTF Lab" \
  --label "version=2.0" \
  --label "built-at=$(date -u +%Y-%m-%dT%H:%M:%SZ)" \
  .

echo ""
ok "Image berhasil di-build: ${FULL_IMAGE}"

# ── 4. Verifikasi image ────────────────────────────────────
IMAGE_SIZE=$(docker image inspect "${FULL_IMAGE}" --format '{{.Size}}' 2>/dev/null | awk '{printf "%.0f MB", $1/1024/1024}')
ok "Ukuran image: ${IMAGE_SIZE}"

# ── 5. (Opsional) Push ke registry ────────────────────────
if [ "$PUSH_IMAGE" = true ]; then
  header "Step 2: Push Image ke Registry"
  log "Pushing ${FULL_IMAGE} ..."
  docker push "${FULL_IMAGE}"
  ok "Image berhasil di-push."
fi

# ── 6. Jalankan seeder Laravel ─────────────────────────────
if [ "$RUN_SEED" = true ]; then
  header "Step 3: Daftarkan ke Database LMS"

  # Cari folder LMS-Backend relatif dari posisi script ini
  SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
  BACKEND_DIR="${SCRIPT_DIR}/../../LMS-Backend"

  if [ -d "$BACKEND_DIR" ] && [ -f "$BACKEND_DIR/artisan" ]; then
    log "Menjalankan seeder di ${BACKEND_DIR} ..."
    cd "$BACKEND_DIR"
    php artisan db:seed --class=LabMachineSeeder --force
    ok "Seeder selesai. CTF SQLi Lab v2 terdaftar di database."
    cd - > /dev/null

    # Aktifkan machine (is_active = false saat seed, aktifkan setelah build)
    log "Mengaktifkan machine di database..."
    php "$BACKEND_DIR/artisan" tinker --execute="
      \$m = App\Models\LabMachine::where('docker_image','lms/ctf-sqli-lab:v2')->first();
      if (\$m) { \$m->update(['is_active'=>true,'pull_status'=>'ready','pulled_at'=>now()]); echo 'Machine diaktifkan: '.\$m->name.PHP_EOL; }
      else { echo 'Machine tidak ditemukan di DB, jalankan seeder dulu.'.PHP_EOL; }
    " 2>/dev/null || warn "Gagal mengaktifkan via tinker. Aktifkan manual di admin panel."
  else
    warn "Folder LMS-Backend tidak ditemukan di ${BACKEND_DIR}."
    warn "Jalankan seeder manual: cd LMS-Backend && php artisan db:seed --class=LabMachineSeeder"
  fi
fi

# ── 7. Ringkasan ───────────────────────────────────────────
header "Selesai!"
echo ""
echo -e "  ${GREEN}✓${NC} Image   : ${BOLD}${FULL_IMAGE}${NC}"
echo -e "  ${GREEN}✓${NC} Port    : 80 (HTTP)"
echo -e "  ${GREEN}✓${NC} Status  : Siap digunakan"
echo ""
echo -e "${YELLOW}Langkah selanjutnya:${NC}"
echo "  1. Buka Admin Panel → Lab Machines"
echo "  2. Pastikan 'CTF SQLi Lab v2' muncul dan statusnya Active"
echo "  3. Buka Admin Panel → Rooms → Edit room Basic Pentest"
echo "  4. Set Lab Machine = 'CTF SQLi Lab v2'"
echo "  5. Pada task yang relevan, centang 'Tampilkan tombol Start Machine'"
echo ""
echo -e "  ${CYAN}Akses langsung (untuk test):${NC}"
echo "  docker run -d --name ctf-sqli-test -p 8080:80 ${FULL_IMAGE}"
echo "  → http://localhost:8080"
echo ""
