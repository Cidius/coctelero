-- =====================================================================
--  Migracion 05: contador de vistas por receta.
--
--    mysql -u USUARIO -p BASE < sql/migracion_05_vistas.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

ALTER TABLE recipes
    ADD COLUMN views INT UNSIGNED NOT NULL DEFAULT 0 AFTER image_path,
    ADD KEY idx_recipes_views (views);
