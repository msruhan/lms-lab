#!/bin/bash

# Script to initialize Guacamole database schema
# This script downloads and runs the Guacamole schema SQL

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

GUACAMOLE_VERSION="1.5.3"
SCHEMA_URL="https://raw.githubusercontent.com/apache/guacamole-client/master/extensions/guacamole-auth-jdbc/modules/guacamole-auth-jdbc-mysql/schema"

echo "Initializing Guacamole database schema..."

# Check if containers are running
if ! docker-compose ps | grep -q "guacamole-db.*Up"; then
  echo "❌ Error: guacamole-db container is not running"
  echo "Start containers first: docker-compose up -d"
  exit 1
fi

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
for i in {1..30}; do
  if docker-compose exec -T guacamole-db mysqladmin -uroot -prootpassword ping > /dev/null 2>&1; then
    echo "✅ MySQL is ready"
    break
  fi
  if [ $i -eq 30 ]; then
    echo "❌ Timeout waiting for MySQL"
    exit 1
  fi
  sleep 2
done

# Download schema files
echo "Downloading Guacamole schema files..."
mkdir -p /tmp/guacamole-schema
cd /tmp/guacamole-schema

for schema_file in 001-create-schema.sql 002-create-admin-user.sql; do
  echo "Downloading $schema_file..."
  curl -s -o "$schema_file" "$SCHEMA_URL/$schema_file"
  if [ $? -ne 0 ]; then
    echo "❌ Failed to download $schema_file"
    exit 1
  fi
done

# Check if schema already exists
echo "Checking if schema already exists..."
EXISTING_TABLES=$(docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db -e "SHOW TABLES;" 2>/dev/null | grep -c "guacamole_" 2>/dev/null || echo "0")
EXISTING_TABLES=$(echo "$EXISTING_TABLES" | tr -d '[:space:]')

if [ -n "$EXISTING_TABLES" ] && [ "$EXISTING_TABLES" -gt "0" ] 2>/dev/null; then
  echo "⚠️  Schema already exists ($EXISTING_TABLES tables found)"
  echo "Skipping schema installation..."
  SCHEMA_SKIPPED=true
else
  # Run schema files
  echo "Installing schema..."
  cd "$SCRIPT_DIR"
  
  docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db < /tmp/guacamole-schema/001-create-schema.sql 2>&1 | grep -v "Warning"
  
  if [ ${PIPESTATUS[0]} -ne 0 ]; then
    # Check if error is because tables already exist
    ERROR_OUTPUT=$(docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db < /tmp/guacamole-schema/001-create-schema.sql 2>&1 | grep -i "already exists" || echo "")
    if [ -n "$ERROR_OUTPUT" ]; then
      echo "⚠️  Schema tables already exist, skipping..."
      SCHEMA_SKIPPED=true
    else
      echo "❌ Failed to install schema"
      exit 1
    fi
  else
    echo "✅ Schema installed successfully"
    SCHEMA_SKIPPED=false
  fi
fi

# Install admin user (default: guacadmin/guacadmin)
if [ "$SCHEMA_SKIPPED" = "true" ]; then
  echo "Checking if admin user exists..."
  ADMIN_EXISTS=$(docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db -e "SELECT COUNT(*) FROM guacamole_entity WHERE name='guacadmin';" 2>/dev/null | tail -1 | tr -d ' ' || echo "0")
  
  if [ "$ADMIN_EXISTS" = "0" ] || [ -z "$ADMIN_EXISTS" ]; then
    echo "Creating default admin user..."
    docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db < /tmp/guacamole-schema/002-create-admin-user.sql 2>&1 | grep -v "Warning"
    
    if [ ${PIPESTATUS[0]} -ne 0 ]; then
      echo "⚠️  Warning: Failed to create admin user (might already exist)"
    else
      echo "✅ Admin user created (guacadmin/guacadmin)"
    fi
  else
    echo "✅ Admin user already exists"
  fi
else
  echo "Creating default admin user..."
  docker-compose exec -T guacamole-db mysql -uroot -prootpassword guacamole_db < /tmp/guacamole-schema/002-create-admin-user.sql 2>&1 | grep -v "Warning"
  
  if [ ${PIPESTATUS[0]} -ne 0 ]; then
    echo "⚠️  Warning: Failed to create admin user (might already exist)"
  else
    echo "✅ Admin user created (guacadmin/guacadmin)"
  fi
fi

# Cleanup
rm -rf /tmp/guacamole-schema

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Database initialization complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Default credentials:"
echo "  Username: guacadmin"
echo "  Password: guacadmin"
echo ""
echo "You can now:"
echo "  1. Login to Guacamole: http://localhost:8080/guacamole"
echo "  2. Run setup script: ./setup-guacamole-connection.sh"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

