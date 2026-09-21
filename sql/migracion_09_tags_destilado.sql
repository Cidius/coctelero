-- =====================================================================
--  Migracion 09: clasificacion "es destilado/licor" por tag, editable
--  desde el admin (antes era una lista fija en el codigo).
--
--  En el listado (sobre todo en mobile, donde solo entra una linea de
--  etiquetas por card) se muestran primero los tags marcados como
--  destilado/licor -- antes esa lista vivia hardcodeada en
--  spirit_tag_slugs() y habia que tocar codigo para sumar un tag nuevo
--  (paso justo con "limoncello"). Ahora se tilda desde /admin/tags.php.
--
--    mysql -u USUARIO -p BASE < sql/migracion_09_tags_destilado.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

ALTER TABLE tags
    ADD COLUMN is_spirit TINYINT(1) NOT NULL DEFAULT 0 AFTER slug;

-- Migra la lista que antes estaba fija en el codigo, para no perder la
-- clasificacion de los tags que ya existen (incluye "limoncello", que
-- fue el caso que disparo este cambio).
UPDATE tags SET is_spirit = 1 WHERE slug IN (
    'gin', 'ron', 'cachaca', 'vodka', 'campari', 'aperol', 'fernet', 'cynar',
    'pineral', 'hesperidina', 'hierro-quina', 'vermut', 'whisky', 'tequila',
    'mezcal', 'pisco', 'brandy', 'marrasquino', 'espumante', 'triple-sec',
    'amargo-obrero', 'chartreuse', 'strega', 'cassis', 'malibu', 'pimms',
    'licor-cafe', 'licor-crema', 'amaretto', 'limoncello'
);
