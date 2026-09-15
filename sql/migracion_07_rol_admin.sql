-- =====================================================================
--  Migracion 07: rol en la tabla de usuarios publicos.
--
--  No reemplaza el login del panel /admin (sigue con su usuario y
--  contrasena propios, sin tocar). Este "role" es solo para que el menu
--  hamburguesa del sitio publico le muestre el link "Panel admin" a la
--  cuenta de Google que vos marques.
--
--    mysql -u USUARIO -p BASE < sql/migracion_07_rol_admin.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

ALTER TABLE users
    ADD COLUMN role ENUM('coctelero','admin') NOT NULL DEFAULT 'coctelero' AFTER name;

-- Para marcarte como admin: primero mira que email quedo guardado con tu
-- cuenta de Google (iniciaste sesion al menos una vez para que exista la fila):
--   SELECT id, email, name FROM users;
--
-- Despues corre esto reemplazando el email por el tuyo real:
--   UPDATE users SET role = 'admin' WHERE email = 'TU_EMAIL_DE_GOOGLE@gmail.com';
