-- =====================================================================
--  Migracion 14: orden de predominancia en el perfil de sabor.
--
--  recipe_flavor_profiles ya guardaba QUE perfiles tiene una receta;
--  ahora tambien guarda en que orden (1 = el mas predominante). Se usa
--  para mostrarlos en ese orden en la ficha de la receta (antes salian
--  en un orden fijo, Dulce/Amargo/Acido/Seco, sin importar cual pesaba
--  mas en cada trago).
--
--    mysql -u USUARIO -p BASE < sql/migracion_14_predominancia_sabor.sql
--
--  Despues correr sql/datos_momento_sabor_2026.sql para cargar la
--  clasificacion real de las recetas existentes.
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

ALTER TABLE recipe_flavor_profiles
    ADD COLUMN position SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER flavor_profile_id;
