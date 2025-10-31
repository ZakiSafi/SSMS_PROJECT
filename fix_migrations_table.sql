-- Fix MySQL tablespace error for migrations table
-- Run this in MySQL command line or phpMyAdmin

USE ssms_project;

-- Option 1: If the table exists but has tablespace issues
-- First, try to discard the tablespace
ALTER TABLE migrations DISCARD TABLESPACE;

-- Option 2: If the table doesn't exist properly, drop it first
DROP TABLE IF EXISTS migrations;

-- After running either option, you can run: php artisan migrate
