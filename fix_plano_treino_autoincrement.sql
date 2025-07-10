-- FILE: fix_plano_treino_autoincrement.sql
-- Fix the plano_treino table to have AUTO_INCREMENT on ID_Plano

ALTER TABLE plano_treino MODIFY COLUMN ID_Plano INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;
