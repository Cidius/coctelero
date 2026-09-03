-- =====================================================================
--  Migracion 03: autor del coctel + enlaces externos
--
--  Correr una vez sobre la base ya cargada:
--    mysql -u USUARIO -p BASE < sql/migracion_03_autor_enlaces.sql
--
--  (En una base nueva no hace falta: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

-- Autor (opcional). Nombre y apellido en un solo campo + su red social.
ALTER TABLE recipes
    ADD COLUMN author_name VARCHAR(120) DEFAULT NULL AFTER description,
    ADD COLUMN author_url   VARCHAR(255) DEFAULT NULL AFTER author_name;

-- Enlaces externos: varios por receta (IBA, Instagram, YouTube, etc.)
CREATE TABLE IF NOT EXISTS recipe_links (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    recipe_id INT UNSIGNED NOT NULL,
    label     VARCHAR(80)  NOT NULL,
    url       VARCHAR(500) NOT NULL,
    position  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_recipe_links_recipe (recipe_id),
    CONSTRAINT fk_recipe_links_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
