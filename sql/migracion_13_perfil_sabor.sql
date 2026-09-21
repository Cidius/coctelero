-- =====================================================================
--  Migracion 13: clasificacion "Perfil de sabor" (Dulce / Amargo /
--  Acido / Seco), multi-select -- una receta puede tener varios a la
--  vez (ej. un Daiquiri es Acido y Dulce; un Negroni, Amargo y Seco).
--
--  Va en tabla propia + tabla puente (como tags/recipe_tags), no una
--  columna ENUM, justamente para permitir mas de uno por receta.
--
--    mysql -u USUARIO -p BASE < sql/migracion_13_perfil_sabor.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

CREATE TABLE flavor_profiles (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name     VARCHAR(40) NOT NULL,
    slug     VARCHAR(60) NOT NULL,
    position SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_flavor_profiles_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO flavor_profiles (name, slug, position) VALUES
    ('Dulce',  'dulce',  10),
    ('Amargo', 'amargo', 20),
    ('Ácido',  'acido',  30),
    ('Seco',   'seco',   40);

CREATE TABLE recipe_flavor_profiles (
    recipe_id         INT UNSIGNED NOT NULL,
    flavor_profile_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (recipe_id, flavor_profile_id),
    KEY idx_rfp_flavor (flavor_profile_id),
    CONSTRAINT fk_rfp_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE,
    CONSTRAINT fk_rfp_flavor
        FOREIGN KEY (flavor_profile_id) REFERENCES flavor_profiles (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
