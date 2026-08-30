-- =====================================================================
--  Recetario de Cocteles - schema (Fase 1)
--  MySQL 5.7+ / MariaDB 10.2+  -  InnoDB  -  utf8mb4
--
--  Ejecutar una vez sobre una base vacia:
--    mysql -u USUARIO -p NOMBRE_BASE < sql/schema.sql
--
--  Decisiones aplicadas (ver plan-recetario-cocteles-v2.md):
--   - Soft delete: recipes.deleted_at (NULL = activa).
--   - Un solo admin, sin roles.
--   - topics/recipe_topics se crean pero quedan SIN USO en esta etapa.
--   - Destilado = tags, no columna propia.
--   - method: ENUM cerrado + method_other para 'otro'.
--     method_detail guarda la tecnica textual completa del recetario
--     ("Batido y doble colado", "Machacado, batido...") para mostrarla
--     en la ficha sin perder informacion al normalizar el ENUM.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS recipe_tags;
DROP TABLE IF EXISTS recipe_topics;
DROP TABLE IF EXISTS recipe_ingredients;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS topics;
DROP TABLE IF EXISTS admin_users;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
--  admin_users  -  un solo registro en esta etapa (sin roles)
-- ---------------------------------------------------------------------
CREATE TABLE admin_users (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username       VARCHAR(50)  NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  recipes
-- ---------------------------------------------------------------------
CREATE TABLE recipes (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(160) NOT NULL,
    slug          VARCHAR(180) NOT NULL,
    glassware     VARCHAR(160)     DEFAULT NULL,
    ice           VARCHAR(80)      DEFAULT NULL,
    method        ENUM('integrado','refrescado_directo','batido','machacado','frozen','otro')
                                NOT NULL DEFAULT 'otro',
    method_other  VARCHAR(160)     DEFAULT NULL,
    method_detail VARCHAR(255)     DEFAULT NULL,
    garnish       VARCHAR(255)     DEFAULT NULL,
    description   TEXT             DEFAULT NULL,
    image_path    VARCHAR(255)     DEFAULT NULL,
    created_by    INT UNSIGNED     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at    DATETIME         DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_recipes_slug (slug),
    KEY idx_recipes_deleted_at (deleted_at),
    KEY idx_recipes_method (method),
    FULLTEXT KEY ft_recipes_name_desc (name, description),
    CONSTRAINT fk_recipes_admin
        FOREIGN KEY (created_by) REFERENCES admin_users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  recipe_ingredients  -  raw_text es la fuente de verdad;
--  amount/unit son pistas estructuradas y pueden ser NULL.
-- ---------------------------------------------------------------------
CREATE TABLE recipe_ingredients (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    recipe_id  INT UNSIGNED NOT NULL,
    raw_text   VARCHAR(255) NOT NULL,
    amount     DECIMAL(8,2)     DEFAULT NULL,
    unit       VARCHAR(24)      DEFAULT NULL,
    position   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_ingredients_recipe (recipe_id),
    CONSTRAINT fk_ingredients_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  tags  -  libres, los crea el admin al cargar. slug normalizado.
-- ---------------------------------------------------------------------
CREATE TABLE tags (
    id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name  VARCHAR(60) NOT NULL,
    slug  VARCHAR(80) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_tags_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recipe_tags (
    recipe_id INT UNSIGNED NOT NULL,
    tag_id    INT UNSIGNED NOT NULL,
    PRIMARY KEY (recipe_id, tag_id),
    KEY idx_recipe_tags_tag (tag_id),
    CONSTRAINT fk_recipe_tags_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE,
    CONSTRAINT fk_recipe_tags_tag
        FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  topics  -  creadas para no migrar despues, SIN USO en esta etapa.
-- ---------------------------------------------------------------------
CREATE TABLE topics (
    id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name  VARCHAR(80)  NOT NULL,
    slug  VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_topics_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recipe_topics (
    recipe_id INT UNSIGNED NOT NULL,
    topic_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (recipe_id, topic_id),
    KEY idx_recipe_topics_topic (topic_id),
    CONSTRAINT fk_recipe_topics_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE,
    CONSTRAINT fk_recipe_topics_topic
        FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  login_attempts  -  rate limiting del login (Hostinger no da fail2ban)
-- ---------------------------------------------------------------------
CREATE TABLE login_attempts (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ip           VARCHAR(45)  NOT NULL,
    username     VARCHAR(50)      DEFAULT NULL,
    success      TINYINT(1)   NOT NULL DEFAULT 0,
    attempted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_login_ip_time (ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
