-- =====================================================================
--  Migracion 02: fusionar los tags derivados de brandy en 'brandy'.
--
--  apricot-brandy y cherry-brandy dejan de existir; las recetas que
--  llevan un ingrediente "... brandy" quedan solo con el tag generico
--  'brandy'. Las que tenian cherry-brandy por el maraschino (Aviation,
--  Last Word) no reciben brandy: no corresponde.
--
--  Seguro de re-correr.
--    mysql -u USUARIO -p BASE < sql/migracion_02_tags_brandy.sql
-- =====================================================================

SET NAMES utf8mb4;

-- 1) Asegurar el tag generico
INSERT IGNORE INTO tags (name, slug) VALUES ('Brandy', 'brandy');

-- 2) Toda receta con un ingrediente que contiene "brandy" -> tag brandy
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT DISTINCT ri.recipe_id, (SELECT id FROM tags WHERE slug = 'brandy')
FROM recipe_ingredients ri
WHERE LOWER(ri.raw_text) LIKE '%brandy%';

-- 3) Borrar los tags derivados (recipe_tags se limpia por FK ON DELETE CASCADE)
DELETE FROM tags WHERE slug IN ('apricot-brandy', 'cherry-brandy');
