-- =====================================================================
--  Migracion 01: clasificaciones de coctel (Clase 6)
--  volumen / momento de consumo / familia
--
--  Correr UNA sola vez sobre la base que ya tiene datos:
--    mysql -u USUARIO -p NOMBRE_BASE < sql/migracion_01_clasificaciones.sql
--
--  Despues correr sql/clasificar_recetas.sql para pre-clasificar las 52.
--  (En una base nueva no hace falta: schema.sql ya trae todo esto.)
-- =====================================================================

SET NAMES utf8mb4;

-- 1) Tabla de familias (clasificacion "por caracteristicas")
CREATE TABLE IF NOT EXISTS families (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name           VARCHAR(60) NOT NULL,
    slug           VARCHAR(80) NOT NULL,
    typical_volume ENUM('short','medium','long') DEFAULT NULL,
    position       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_families_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO families (name, slug, typical_volume, position) VALUES
    ('Dúo',          'duo',          'short',  10),
    ('Trío',         'trio',         'short',  20),
    ('Sour',         'sour',         'short',  30),
    ('Fizz',         'fizz',         'short',  40),
    ('Collins',      'collins',      'long',   50),
    ('Julep',        'julep',        'medium', 60),
    ('Smash',        'smash',        'short',  70),
    ('On the Rocks', 'on-the-rocks', 'short',  80),
    ('Colada',       'colada',       'long',   90),
    ('Cobbler',      'cobbler',      'short', 100),
    ('Cooler',       'cooler',       'long',  110),
    ('Crusta',       'crusta',       'short', 120),
    ('Cup',          'cup',          'long',  130),
    ('Flip',         'flip',         'short', 140),
    ('Sling',        'sling',        'short', 150),
    ('Highball',     'highball',     'long',  160),
    ('Mocktail',     'mocktail',     'long',  170);

-- 2) Columnas nuevas en recipes
--    (si alguna ya existe, MySQL da error: ya estaba migrado, ignoralo)
ALTER TABLE recipes
    ADD COLUMN volume    ENUM('short','medium','long')          DEFAULT NULL AFTER method_detail,
    ADD COLUMN moment    ENUM('aperitivo','digestivo','all_day') DEFAULT NULL AFTER volume,
    ADD COLUMN family_id INT UNSIGNED                            DEFAULT NULL AFTER moment,
    ADD KEY idx_recipes_volume (volume),
    ADD KEY idx_recipes_moment (moment),
    ADD KEY idx_recipes_family (family_id),
    ADD CONSTRAINT fk_recipes_family
        FOREIGN KEY (family_id) REFERENCES families (id) ON DELETE SET NULL;
