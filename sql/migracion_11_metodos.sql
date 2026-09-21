-- =====================================================================
--  Migracion 11: metodos de preparacion mas claros (se agrega el
--  termino en ingles entre parentesis) + dos tecnicas nuevas.
--
--    integrado           -> Integrado (Stir)
--    refrescado_directo  -> Directo (Build)
--    batido              -> Batido (Shake)
--    machacado           -> Machacado (Smash)
--    frozen              -> Licuado (Frozen)
--    (nuevo)             -> Lanzado (Throwing)
--    (nuevo)             -> Capas (Layering)
--    otro                -> Otro
--
--  El ENUM se actualiza en dos pasos porque MySQL no permite renombrar
--  un valor de ENUM que ya esta en uso sin antes agregar el nuevo al
--  lado del viejo, mover los datos, y recien ahi sacar el viejo.
--
--    mysql -u USUARIO -p BASE < sql/migracion_11_metodos.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

-- Paso 1: agrega los slugs nuevos sin sacar los viejos todavia.
ALTER TABLE recipes
    MODIFY COLUMN method ENUM(
        'integrado', 'refrescado_directo', 'batido', 'machacado', 'frozen', 'otro',
        'directo', 'licuado', 'lanzado', 'capas'
    ) NOT NULL DEFAULT 'otro';

UPDATE recipes SET method = 'directo' WHERE method = 'refrescado_directo';
UPDATE recipes SET method = 'licuado' WHERE method = 'frozen';

-- Paso 2: ENUM final, ya sin los slugs viejos.
ALTER TABLE recipes
    MODIFY COLUMN method ENUM(
        'integrado', 'directo', 'batido', 'machacado', 'licuado', 'lanzado', 'capas', 'otro'
    ) NOT NULL DEFAULT 'otro';
