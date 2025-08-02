-- Migration: add_author_column
-- Created: 2025-08-02 09:52:13

-- Add author column to pages table
ALTER TABLE pages ADD COLUMN author VARCHAR(255) DEFAULT 'System';

-- Update existing pages with default author
UPDATE pages SET author = 'Minimal Boot Team' WHERE author = 'System';

