-- Initialize Guacamole Database
-- This will be run automatically on first startup

CREATE DATABASE IF NOT EXISTS guacamole_db;
USE guacamole_db;

-- The guacamole_init.sql is typically provided by Guacamole image
-- but we can create the connection manually

