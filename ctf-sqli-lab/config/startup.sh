#!/bin/bash
set -e

# ============================================
# CTF SQL Injection Lab - Startup Script
# ============================================

echo "[*] Starting CTF SQL Injection Lab..."

# Create required directories
mkdir -p /var/log/supervisor
mkdir -p /run/mysqld
mkdir -p /var/run/sshd
chown mysql:mysql /run/mysqld

# ============================================
# MariaDB Initialization
# ============================================
echo "[*] Initializing MariaDB..."

# Initialize data directory if needed
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "[*] First run - installing MariaDB system tables..."
    mariadb-install-db --user=mysql --datadir=/var/lib/mysql 2>/dev/null || \
    mysql_install_db --user=mysql --datadir=/var/lib/mysql 2>/dev/null || true
    echo "[+] System tables installed."
fi

# Start MariaDB temporarily for database init
echo "[*] Starting temporary MariaDB instance..."
/usr/sbin/mariadbd --user=mysql --datadir=/var/lib/mysql \
    --pid-file=/run/mysqld/mysqld.pid \
    --socket=/run/mysqld/mysqld.sock &
MARIADB_PID=$!

# Wait for MariaDB to be ready (max 30 seconds)
COUNTER=0
while ! mariadb -u root -S /run/mysqld/mysqld.sock -e "SELECT 1" &>/dev/null; do
    COUNTER=$((COUNTER + 1))
    if [ $COUNTER -ge 30 ]; then
        echo "[-] ERROR: MariaDB failed to start within 30 seconds"
        exit 1
    fi
    echo "[*] Waiting for MariaDB... ($COUNTER/30)"
    sleep 1
done
echo "[+] MariaDB is ready!"

# Initialize CTF database if not done
if [ ! -f "/var/lib/mysql/.ctf_initialized" ]; then
    echo "[*] Initializing CTF database..."
    mariadb -u root -S /run/mysqld/mysqld.sock < /docker-entrypoint-initdb.d/init.sql
    touch /var/lib/mysql/.ctf_initialized
    echo "[+] CTF database initialized successfully!"
else
    echo "[+] CTF database already initialized, skipping."
fi

# Stop temporary MariaDB cleanly
echo "[*] Stopping temporary MariaDB..."
kill $MARIADB_PID 2>/dev/null
wait $MARIADB_PID 2>/dev/null || true
sleep 2

# Clean up PID and socket
rm -f /run/mysqld/mysqld.pid /run/mysqld/mysqld.sock

# Re-create run directory with correct permissions
mkdir -p /run/mysqld
chown mysql:mysql /run/mysqld

echo ""
echo "============================================"
echo "  CTF SQL Injection Lab - Starting!"
echo "============================================"
echo "  Web App  : http://localhost:80"
echo "  SSH      : ssh ctfadmin@localhost -p 22"
echo "  MySQL    : mysql -h localhost -P 3306"
echo "============================================"
echo ""

# Start all services via supervisor (foreground, nodaemon)
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
