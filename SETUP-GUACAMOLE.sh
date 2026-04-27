#!/bin/bash

# Setup script for Apache Guacamole with SOC Simulator

echo "Setting up Apache Guacamole for SOC Simulator..."

# Start containers
echo "Starting Docker containers..."
docker-compose up -d guacd guacamole-db

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 15

# Initialize Guacamole database
echo "Initializing Guacamole database..."
docker-compose run --rm guacamole /opt/guacamole/bin/initdb.sh --mysql > initdb.sql
docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db < initdb.sql
rm initdb.sql

# Start Guacamole
echo "Starting Guacamole web application..."
docker-compose up -d guacamole

echo ""
echo "=========================================="
echo "Guacamole setup complete!"
echo ""
echo "Access Guacamole at: http://localhost:8080/guacamole"
echo "Default credentials: guacadmin / guacadmin"
echo ""
echo "Next steps:"
echo "1. Login to Guacamole web interface"
echo "2. Create a new RDP connection:"
echo "   - Name: Windows Analyst VM"
echo "   - Protocol: RDP"
echo "   - Hostname: 46.4.99.5"
echo "   - Port: 13408"
echo "   - Username: administrator"
echo "   - Password: Klapaucius12!"
echo "3. Save the connection"
echo "4. The connection will be available in SOC Simulator"
echo "=========================================="

