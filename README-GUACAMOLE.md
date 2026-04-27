# Apache Guacamole Setup untuk SOC Simulator

## Overview

Apache Guacamole digunakan untuk menampilkan Windows Server VM melalui RDP di menu Analyst VM pada SOC Simulator.

## Prerequisites

- Docker & Docker Compose
- Port 8080 available untuk Guacamole web interface
- Port 4822 available untuk guacd daemon
- Port 3306 available untuk MySQL (atau menggunakan port lain)

## Setup Docker Compose

Guacamole sudah dikonfigurasi di `docker-compose.yml`. Untuk menjalankan:

```bash
cd LMS-Lab
docker-compose up -d
```

Tunggu beberapa saat hingga semua container running (terutama MySQL yang perlu inisialisasi database).

## Akses Guacamole

Setelah container berjalan, akses Guacamole web interface di:
- **URL**: http://localhost:8080/guacamole
- **Default credentials**: 
  - Username: `guacadmin`
  - Password: `guacadmin`
  - **PENTING**: Password harus diubah setelah login pertama!

## Setup Connection via Web Interface (Manual)

### Langkah-langkah:

1. **Login ke Guacamole**
   - Buka http://localhost:8080/guacamole
   - Login dengan `guacadmin` / `guacadmin`

2. **Create Connection**
   - Klik "Settings" → "Connections"
   - Klik "New Connection"
   - Isi form dengan konfigurasi berikut:

   **Basic Settings:**
   - **Name**: `Windows Analyst VM`
   - **Protocol**: `RDP`

   **Network Settings:**
   - **Hostname**: `46.4.99.5`
   - **Port**: `13408`

   **Authentication:**
   - **Username**: `administrator`
   - **Password**: `Klapaucius12!`
   - **Domain**: (kosongkan)
   - **Security**: `any`

   **Display Settings:**
   - **Color depth**: `True color (32 bit)`
   - **Resolution**: `Use client resolution`

   **Performance:**
   - **Enable wallpaper**: Yes
   - **Enable theming**: Yes
   - **Enable font smoothing**: Yes
   - **Enable desktop composition**: Yes
   - **Enable menu animations**: Yes

   **Drive/Printing:**
   - **Create drive path**: Yes
   - **Enable drive**: Yes
   - **Enable printing**: Yes

3. **Save Connection**
   - Klik "Save" untuk menyimpan connection

4. **Test Connection**
   - Klik connection name untuk test koneksi
   - Pastikan bisa connect ke Windows Server VM

## Setup Connection via Script (Automated)

Alternatif menggunakan script untuk setup otomatis:

```bash
cd LMS-Lab
chmod +x setup-guacamole-connection.sh
./setup-guacamole-connection.sh
```

Script ini akan:
- Authenticate dengan Guacamole API
- Create connection "Windows Analyst VM" dengan konfigurasi yang tepat
- Return connection ID untuk digunakan di frontend

**Note**: Pastikan `jq` sudah terinstall untuk parsing JSON:
```bash
# macOS
brew install jq

# Ubuntu/Debian
sudo apt-get install jq
```

## Integrasi dengan Frontend

Connection dapat diakses melalui:

1. **Direct URL** (setelah login):
   ```
   http://localhost:8080/guacamole/#/client/<CONNECTION_ID>
   ```

2. **Via Frontend** (SOC Simulator):
   - Klik menu "Analyst VM" di sidebar
   - Klik tombol "Connect to VM"
   - Frontend akan otomatis:
     - Authenticate dengan Guacamole API
     - Cari atau create connection "Windows Analyst VM"
     - Load connection di iframe

### Connection Configuration di Frontend

Konfigurasi RDP ada di `SOCSimulatorSimulation.vue`:

```javascript
const vmConfig = {
  host: '46.4.99.5',
  port: '13408',
  username: 'administrator',
  password: 'Klapaucius12!',
  connectionName: 'Windows Analyst VM'
}
```

## Troubleshooting

### 1. Container tidak berjalan
```bash
# Check container status
docker-compose ps

# Check logs
docker-compose logs guacamole
docker-compose logs guacd
docker-compose logs guacamole-db

# Restart services
docker-compose restart
```

### 2. Connection tidak muncul di frontend

**Problem**: Connection ID tidak ditemukan

**Solutions**:
- Pastikan connection sudah dibuat di Guacamole (via web UI atau script)
- Check connection name harus exact: "Windows Analyst VM"
- Restart Guacamole container: `docker-compose restart guacamole`
- Check browser console untuk error messages

### 3. Cannot connect to RDP

**Problem**: Connection failed saat connect ke Windows Server

**Solutions**:
- Pastikan Windows Server VM accessible dari host
- Check firewall rules untuk port 13408
- Verify RDP credentials (username/password)
- Check Windows Server RDP settings (Allow remote connections)

### 4. CORS Error

**Problem**: CORS error saat frontend call Guacamole API

**Solutions**:
- Guacamole API tidak support CORS untuk browser
- Frontend perlu menggunakan backend proxy untuk API calls
- Atau setup connection manual via web UI, kemudian frontend hanya load iframe

### 5. Iframe tidak load

**Problem**: Iframe blank atau tidak menampilkan desktop

**Solutions**:
- Pastikan sudah login ke Guacamole di browser (same origin)
- Check browser console untuk errors
- Verify connection ID correct
- Try access connection URL directly: `http://localhost:8080/guacamole/#/client/<ID>`

## Security Notes

1. **Change Default Password**: 
   - Login ke Guacamole
   - Settings → Users → guacadmin → Change Password

2. **Use Environment Variables**:
   - Jangan hardcode credentials di docker-compose.yml
   - Gunakan `.env` file untuk sensitive data

3. **Network Security**:
   - Guacamole hanya accessible dari localhost (development)
   - Untuk production, setup reverse proxy dengan SSL/TLS
   - Restrict access ke Guacamole admin panel

## Architecture

```
┌─────────────┐
│   Browser   │
│  (Frontend) │
└──────┬──────┘
       │ HTTP/HTTPS
       │
┌──────▼──────────┐
│   Guacamole     │
│  (Web Server)   │
│  Port: 8080     │
└──────┬──────────┘
       │
       ├─────────► Guacd (Protocol Translation)
       │           Port: 4822
       │
       └─────────► MySQL (Connection Config)
                   Port: 3306
                          │
                          ▼
              ┌──────────────────────┐
              │  Windows Server VM   │
              │  46.4.99.5:13408     │
              │  (RDP Protocol)      │
              └──────────────────────┘
```

## API Reference

Guacamole REST API documentation: https://guacamole.apache.org/doc/gug/guacamole-common.html

### Get Auth Token
```bash
curl -X POST http://localhost:8080/guacamole/api/tokens \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=guacadmin&password=guacadmin"
```

### List Connections
```bash
curl "http://localhost:8080/guacamole/api/session/data/mysql/connections?token=<AUTH_TOKEN>"
```

### Create Connection
```bash
curl -X POST "http://localhost:8080/guacamole/api/session/data/mysql/connections?token=<AUTH_TOKEN>" \
  -H "Content-Type: application/json" \
  -d @connection-config.json
```

## Next Steps

- [ ] Setup backend API proxy untuk Guacamole API calls (avoid CORS)
- [ ] Add connection management UI di frontend
- [ ] Implement connection pooling untuk multiple users
- [ ] Add recording feature untuk session replay
- [ ] Setup monitoring dan logging
