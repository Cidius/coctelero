-- =====================================================================
--  Migracion 08: pasos de preparacion (paso a paso), linea por linea.
--
--  Nuevo campo de texto libre en recipes, igual de simple que
--  method_detail o description (no es una tabla aparte como los
--  ingredientes: no necesita cantidad/unidad, solo texto por renglon).
--
--    mysql -u USUARIO -p BASE < sql/migracion_08_pasos.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

ALTER TABLE recipes
    ADD COLUMN steps TEXT NULL AFTER method_detail;
