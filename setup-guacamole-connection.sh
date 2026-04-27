#!/bin/bash

# Script to setup Guacamole connection for Windows Analyst VM
# This script creates the connection via Guacamole REST API

GUACAMOLE_URL="http://localhost:8080/guacamole"
GUACAMOLE_USER="guacadmin"
GUACAMOLE_PASS="guacadmin"

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "Current directory: $(pwd)"
echo "Setting up Guacamole connection..."

# Check if Guacamole is accessible
echo "Checking if Guacamole is running..."
if ! curl -s --head --fail "$GUACAMOLE_URL" > /dev/null 2>&1; then
  echo "❌ Error: Guacamole is not accessible at $GUACAMOLE_URL"
  echo ""
  echo "Please start Guacamole first:"
  echo "  cd $(pwd)"
  echo "  docker-compose up -d"
  echo ""
  echo "Wait for services to be ready (check with: docker-compose ps)"
  exit 1
fi

echo "✅ Guacamole is accessible"

# Connection configuration
CONNECTION_NAME="Windows Analyst VM"
PROTOCOL="rdp"
HOSTNAME="46.4.99.5"
PORT="13408"
USERNAME="administrator"
PASSWORD="Klapaucius12!"

echo "Setting up Guacamole connection..."

# Check if jq is installed
if ! command -v jq &> /dev/null; then
  echo "❌ Error: jq is required but not installed"
  echo "Install jq:"
  echo "  macOS: brew install jq"
  echo "  Ubuntu/Debian: sudo apt-get install jq"
  exit 1
fi

# Get auth token
echo "Authenticating with Guacamole..."
AUTH_RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" -X POST "$GUACAMOLE_URL/api/tokens" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=$GUACAMOLE_USER&password=$GUACAMOLE_PASS")

HTTP_CODE=$(echo "$AUTH_RESPONSE" | grep -o 'HTTP_CODE:[0-9]*' | cut -d: -f2)
AUTH_BODY=$(echo "$AUTH_RESPONSE" | sed 's/HTTP_CODE:[0-9]*$//')

if [ "$HTTP_CODE" != "200" ]; then
  echo "❌ Error: Failed to authenticate with Guacamole (HTTP $HTTP_CODE)"
  echo "Response: $AUTH_BODY"
  echo ""
  echo "Possible causes:"
  echo "  1. Guacamole container is still starting (wait a few minutes)"
  echo "  2. Wrong username/password (default: guacadmin/guacadmin)"
  echo "  3. Database not initialized yet"
  echo ""
  echo "Check Guacamole logs: docker-compose logs guacamole"
  exit 1
fi

AUTH_TOKEN=$(echo "$AUTH_BODY" | jq -r '.authToken')

if [ -z "$AUTH_TOKEN" ] || [ "$AUTH_TOKEN" = "null" ]; then
  echo "❌ Error: Failed to get auth token"
  echo "Response: $AUTH_BODY"
  exit 1
fi

echo "✅ Authentication successful"

# Create connection JSON
CONNECTION_JSON=$(cat <<EOF
{
  "name": "$CONNECTION_NAME",
  "protocol": "$PROTOCOL",
  "parameters": {
    "hostname": "$HOSTNAME",
    "port": "$PORT",
    "username": "$USERNAME",
    "password": "$PASSWORD",
    "domain": "",
    "security": "any",
    "ignore-cert": "true",
    "create-drive-path": "true",
    "disable-audio": "false",
    "enable-audio-input": "false",
    "enable-printing": "true",
    "enable-drive": "true",
    "create-printer-query": "true",
    "enable-wallpaper": "true",
    "enable-theming": "true",
    "enable-font-smoothing": "true",
    "enable-full-window-drag": "true",
    "enable-desktop-composition": "true",
    "enable-menu-animations": "true",
    "disable-bitmap-caching": "false",
    "disable-offscreen-caching": "false",
    "disable-glyph-caching": "false"
  },
  "attributes": {
    "max-connections": "1",
    "max-connections-per-user": "1"
  }
}
EOF
)

# Check if connection already exists
echo "Checking for existing connection..."
EXISTING_CONNECTIONS=$(curl -s "$GUACAMOLE_URL/api/session/data/mysql/connections?token=$AUTH_TOKEN")
EXISTING_ID=$(echo "$EXISTING_CONNECTIONS" | jq -r ".[] | select(.name == \"$CONNECTION_NAME\") | .identifier")

if [ -n "$EXISTING_ID" ] && [ "$EXISTING_ID" != "null" ]; then
  echo "✅ Connection '$CONNECTION_NAME' already exists (ID: $EXISTING_ID)"
  CONNECTION_ID=$EXISTING_ID
else
  # Create connection
  echo "Creating connection: $CONNECTION_NAME"
  CREATE_RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" -X POST "$GUACAMOLE_URL/api/session/data/mysql/connections?token=$AUTH_TOKEN" \
    -H "Content-Type: application/json" \
    -d "$CONNECTION_JSON")
  
  CREATE_HTTP_CODE=$(echo "$CREATE_RESPONSE" | grep -o 'HTTP_CODE:[0-9]*' | cut -d: -f2)
  CREATE_BODY=$(echo "$CREATE_RESPONSE" | sed 's/HTTP_CODE:[0-9]*$//')
  
  if [ "$CREATE_HTTP_CODE" != "200" ]; then
    echo "❌ Error: Failed to create connection (HTTP $CREATE_HTTP_CODE)"
    echo "Response: $CREATE_BODY"
    exit 1
  fi
  
  CONNECTION_ID=$(echo "$CREATE_BODY" | jq -r '.identifier')
  
  if [ -z "$CONNECTION_ID" ] || [ "$CONNECTION_ID" = "null" ]; then
    echo "❌ Error: Failed to get connection ID"
    echo "Response: $CREATE_BODY"
    exit 1
  fi
  
  echo "✅ Connection created successfully!"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Setup Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Connection ID: $CONNECTION_ID"
echo "Connection Name: $CONNECTION_NAME"
echo "Connection URL: $GUACAMOLE_URL/#/client/$CONNECTION_ID"
echo ""
echo "You can now use this connection in the SOC Simulator"
echo "Analyst VM menu. The frontend will automatically use"
echo "this connection."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

