-- SQL to add image column to categories table
ALTER TABLE categories ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER slug;
