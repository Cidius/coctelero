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
DROP TABLE IF EXISTS recipe_links;
DROP TABLE IF EXISTS recipe_favorites;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS families;
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
--  families  -  clasificacion "por caracteristicas" (Sour, Julep, ...),
--  segun la nomenclatura clasica de la coctelera (Old Fashioned,
--  Aromaticos, Negroni, Sour, Daisy, Fizz, Collins, Highball, Julep,
--  Smash, Punch, Cobbler, Flip, Sparkling, Hot drinks, Tiki).
--  typical_volume: columna historica, ya no se usa en la UI.
-- ---------------------------------------------------------------------
CREATE TABLE families (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name           VARCHAR(60) NOT NULL,
    slug           VARCHAR(80) NOT NULL,
    description    TEXT             DEFAULT NULL,
    typical_volume ENUM('short','medium','long') DEFAULT NULL,
    position       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_families_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  glassware  -  catalogo de cristaleria, ABM en /admin/cristaleria.php.
-- ---------------------------------------------------------------------
CREATE TABLE glassware (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name     VARCHAR(120) NOT NULL,
    position SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_glassware_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  recipes
-- ---------------------------------------------------------------------
CREATE TABLE recipes (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(160) NOT NULL,
    slug          VARCHAR(180) NOT NULL,
    glassware_id  INT UNSIGNED     DEFAULT NULL,
    ice           VARCHAR(80)      DEFAULT NULL,
    method        ENUM('integrado','directo','batido','machacado','licuado','lanzado','capas','otro')
                                NOT NULL DEFAULT 'otro',
    method_other  VARCHAR(160)     DEFAULT NULL,
    method_detail VARCHAR(255)     DEFAULT NULL,
    steps         TEXT             DEFAULT NULL,
    -- Clasificaciones (Clase 6): uno por receta, opcionales.
    volume        ENUM('short','medium','long')          DEFAULT NULL,
    moment        ENUM('aperitivo','digestivo','all_day') DEFAULT NULL,
    family_id     INT UNSIGNED                            DEFAULT NULL,
    garnish       VARCHAR(255)     DEFAULT NULL,
    description   TEXT             DEFAULT NULL,
    -- Autor (coctel de autor): nombre y apellido juntos + su red social.
    author_name   VARCHAR(120)     DEFAULT NULL,
    author_url    VARCHAR(255)     DEFAULT NULL,
    image_path    VARCHAR(255)     DEFAULT NULL,
    views         INT UNSIGNED NOT NULL DEFAULT 0,
    created_by    INT UNSIGNED     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at    DATETIME         DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_recipes_slug (slug),
    KEY idx_recipes_deleted_at (deleted_at),
    KEY idx_recipes_method (method),
    KEY idx_recipes_volume (volume),
    KEY idx_recipes_moment (moment),
    KEY idx_recipes_family (family_id),
    KEY idx_recipes_views (views),
    FULLTEXT KEY ft_recipes_name_desc (name, description),
    CONSTRAINT fk_recipes_admin
        FOREIGN KEY (created_by) REFERENCES admin_users (id) ON DELETE SET NULL,
    CONSTRAINT fk_recipes_family
        FOREIGN KEY (family_id) REFERENCES families (id) ON DELETE SET NULL,
    CONSTRAINT fk_recipes_glassware
        FOREIGN KEY (glassware_id) REFERENCES glassware (id)
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
--  recipe_links  -  enlaces externos por receta (IBA, Instagram, YouTube...)
-- ---------------------------------------------------------------------
CREATE TABLE recipe_links (
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

-- ---------------------------------------------------------------------
--  tags  -  libres, los crea el admin al cargar. slug normalizado.
-- ---------------------------------------------------------------------
CREATE TABLE tags (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name      VARCHAR(60) NOT NULL,
    slug      VARCHAR(80) NOT NULL,
    -- Tildado desde /admin/tags.php. Controla si el tag aparece en la
    -- linea de bebidas de cada card (destilados/licores), la unica
    -- etiqueta visible en el listado compacto de mobile.
    is_spirit TINYINT(1) NOT NULL DEFAULT 0,
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

-- ---------------------------------------------------------------------
--  users  -  cuentas publicas (rol "coctelero"). Sin contrasena:
--  login solo con Google Sign-In.
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    google_sub    VARCHAR(64)  NOT NULL,
    email         VARCHAR(190) NOT NULL,
    name          VARCHAR(160) NOT NULL,
    -- "admin" solo controla que el menu del sitio muestre el link al panel
    -- /admin; ese panel sigue con su propio login, esto no lo reemplaza.
    role          ENUM('coctelero','admin') NOT NULL DEFAULT 'coctelero',
    avatar_url    VARCHAR(500)     DEFAULT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_google_sub (google_sub)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  recipe_favorites  -  like + favorito: misma accion. El COUNT es el
--  like publico; las filas de un usuario son su lista de favoritos.
-- ---------------------------------------------------------------------
CREATE TABLE recipe_favorites (
    user_id    INT UNSIGNED NOT NULL,
    recipe_id  INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, recipe_id),
    KEY idx_recipe_favorites_recipe (recipe_id),
    CONSTRAINT fk_favorites_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  messages  -  contacto/feedback al coctelero. Solo usuarios logueados.
-- ---------------------------------------------------------------------
CREATE TABLE messages (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED     DEFAULT NULL,
    recipe_id  INT UNSIGNED     DEFAULT NULL,
    name       VARCHAR(160) NOT NULL,
    email      VARCHAR(190) NOT NULL,
    body       TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at    DATETIME         DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_messages_read (read_at),
    CONSTRAINT fk_messages_user
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_messages_recipe
        FOREIGN KEY (recipe_id) REFERENCES recipes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  families: nomenclatura clasica de la coctelera, con su descripcion.
-- ---------------------------------------------------------------------
INSERT INTO families (name, slug, description, position) VALUES
    ('Old Fashioned', 'old-fashioned',
        'Espirituoso, azúcar y bitters, sin alargador. Base de la coctelería clásica.', 10),
    ('Aromáticos (Martini/Manhattan)', 'aromaticos',
        'Base + vermut u otro modificador aromático, se revuelve, no se agita.', 20),
    ('Negroni', 'negroni',
        'Variante de los aromáticos con tres partes iguales (espirituoso, vermut, amargo). Algunos autores la separan como familia propia.', 30),
    ('Sour', 'sour',
        'Base + cítrico + endulzante, se agita.', 40),
    ('Daisy', 'daisy',
        'Un Sour donde el endulzante es un licor en vez de almíbar.', 50),
    ('Fizz', 'fizz',
        'Un Sour alargado con soda, se sirve sin hielo.', 60),
    ('Collins', 'collins',
        'Como el Fizz pero servido con hielo, en vaso alto.', 70),
    ('Highball', 'highball',
        'Espirituoso + alargador simple (soda, tónica, gaseosa), sin proceso de agitado.', 80),
    ('Julep', 'julep',
        'Hierbas machacadas + espirituoso + azúcar, sobre hielo picado.', 90),
    ('Smash', 'smash',
        'Como el Julep pero con fruta fresca machacada además de hierbas.', 100),
    ('Punch', 'punch',
        'Mezcla en volumen para compartir, históricamente con cinco elementos (fuerte, débil, agrio, dulce, especia).', 110),
    ('Cobbler', 'cobbler',
        'Fruta + azúcar + vino o licor sobre hielo picado, con pajita corta.', 120),
    ('Flip', 'flip',
        'Espirituoso + huevo entero + azúcar, sin lácteos.', 130),
    ('Sparkling', 'sparkling',
        'Armado con vino espumante como componente principal.', 140),
    ('Hot drinks', 'hot-drinks',
        'Servidos calientes (café, agua caliente, manteca derretida como base).', 150),
    ('Tiki', 'tiki',
        'Múltiples rones + jugos frescos + jarabes especiados (orgeat, falernum).', 160);
