-- =====================================================================
--  Migracion 10: cristaleria como catalogo propio (antes texto libre en
--  recipes.glassware), con ABM en /admin/cristaleria.php.
--
--  Da de alta automaticamente cada cristaleria que ya este en uso (lo
--  que haya escrito en recipes.glassware hasta ahora) y reasigna cada
--  receta a la fila correspondiente por nombre exacto (recortando
--  espacios). Si dos recetas tenian el mismo nombre con mayus/minus
--  distintas quedan como cristalerias separadas -- se pueden unificar
--  despues a mano desde /admin/cristaleria.php (editar/borrar).
--
--    mysql -u USUARIO -p BASE < sql/migracion_10_cristaleria.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

CREATE TABLE glassware (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name     VARCHAR(120) NOT NULL,
    position SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_glassware_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO glassware (name)
SELECT DISTINCT TRIM(glassware) FROM recipes
WHERE glassware IS NOT NULL AND TRIM(glassware) <> '';

-- Orden alfabetico inicial (se puede reordenar despues, la columna
-- position queda pensada para eso aunque el ABM actual no la edita).
SET @pos = 0;
UPDATE glassware SET position = (@pos := @pos + 1) ORDER BY name ASC;

ALTER TABLE recipes
    ADD COLUMN glassware_id INT UNSIGNED NULL AFTER glassware;

UPDATE recipes r
JOIN glassware g ON g.name = TRIM(r.glassware)
SET r.glassware_id = g.id
WHERE r.glassware IS NOT NULL AND TRIM(r.glassware) <> '';

ALTER TABLE recipes
    DROP COLUMN glassware,
    ADD CONSTRAINT fk_recipes_glassware FOREIGN KEY (glassware_id) REFERENCES glassware (id);
