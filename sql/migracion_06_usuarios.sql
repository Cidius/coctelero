-- =====================================================================
--  Migracion 06: cuentas de usuario (Google Sign-In), favoritos/likes
--  y mensajes de contacto al coctelero.
--
--    mysql -u USUARIO -p BASE < sql/migracion_06_usuarios.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

-- Cuentas publicas (rol "coctelero"). Sin contrasena: login solo con Google.
CREATE TABLE IF NOT EXISTS users (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    google_sub     VARCHAR(64)  NOT NULL,
    email          VARCHAR(190) NOT NULL,
    name           VARCHAR(160) NOT NULL,
    avatar_url     VARCHAR(500)     DEFAULT NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_google_sub (google_sub)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Like + favorito son la misma accion: un tap = cuenta como like publico
-- (COUNT de esta tabla) y queda guardado en la lista del usuario.
CREATE TABLE IF NOT EXISTS recipe_favorites (
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

-- Mensajes de contacto/feedback. Solo usuarios logueados pueden mandarlos;
-- name/email quedan "fotografiados" al momento de enviar (ademas del
-- user_id) para que el mensaje siga siendo legible aunque la cuenta cambie.
CREATE TABLE IF NOT EXISTS messages (
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
