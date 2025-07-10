-- Fix AUTO_INCREMENT for avaliacao_fisica table
-- Run this script to fix the ID_Avaliacao field

USE academiabd;

-- Add AUTO_INCREMENT to ID_Avaliacao field
ALTER TABLE avaliacao_fisica MODIFY COLUMN ID_Avaliacao INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;

-- Set the AUTO_INCREMENT starting value to be higher than existing IDs
-- This ensures no conflicts with existing data
SET @max_id = (SELECT COALESCE(MAX(ID_Avaliacao), 0) FROM avaliacao_fisica);
SET @sql = CONCAT('ALTER TABLE avaliacao_fisica AUTO_INCREMENT = ', @max_id + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'AUTO_INCREMENT added to avaliacao_fisica.ID_Avaliacao successfully' as result;
