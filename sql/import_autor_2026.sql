-- =====================================================================
--  Import: cocteles de autor e internacionales clasicos 2026
--  GENERADO por sql/import/build_import_autor.php
--  Seguro de re-correr. Requiere migraciones 01-05 aplicadas.
-- =====================================================================

SET NAMES utf8mb4;
START TRANSACTION;

-- tags que puedan faltar
INSERT IGNORE INTO tags (name, slug) VALUES
  ('Amaretto', 'amaretto'),
  ('Amargo Obrero', 'amargo-obrero'),
  ('Aperol', 'aperol'),
  ('Brandy', 'brandy'),
  ('Cafe', 'cafe'),
  ('Campari', 'campari'),
  ('Cassis', 'cassis'),
  ('Chartreuse', 'chartreuse'),
  ('Citrico', 'citrico'),
  ('Con Huevo', 'con-huevo'),
  ('Cynar', 'cynar'),
  ('Gin', 'gin'),
  ('Hierro Quina', 'hierro-quina'),
  ('Licor Cafe', 'licor-cafe'),
  ('Licor Crema', 'licor-crema'),
  ('Malibu', 'malibu'),
  ('Menta', 'menta'),
  ('Pimms', 'pimms'),
  ('Pomelo', 'pomelo'),
  ('Ron', 'ron'),
  ('Sin Alcohol', 'sin-alcohol'),
  ('Tequila', 'tequila'),
  ('Triple Sec', 'triple-sec'),
  ('Vermut', 'vermut'),
  ('Vodka', 'vodka'),
  ('Whisky', 'whisky');

-- ---------- Coctel Litoraleño ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Coctel Litoraleño', 'coctel-litoraleno', 'Vaso trago largo', NULL, 'integrado', NULL, 'Directo', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'highball'), 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'coctel-litoraleno');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Pombero del Litoral (vermut)' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de gin tónica' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '2 oz de almíbar de cardamomo' AS raw, 2 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 4 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 5 AS pos
) x WHERE r.slug = 'coctel-litoraleno' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vermut', 'gin', 'citrico') WHERE r.slug = 'coctel-litoraleno';

-- ---------- Limonada especiada ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Limonada especiada', 'limonada-especiada', 'Copa Hurricane', NULL, 'integrado', NULL, 'Directo', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'mocktail'), 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'limonada-especiada');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de almíbar de cardamomo' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 3 AS pos
) x WHERE r.slug = 'limonada-especiada' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('sin-alcohol', 'citrico') WHERE r.slug = 'limonada-especiada';

-- ---------- Naranjada especiada ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Naranjada especiada', 'naranjada-especiada', 'Vaso bombé', NULL, 'integrado', NULL, 'Directo', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'mocktail'), 'Media rodaja de naranja', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'naranjada-especiada');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de almíbar especiado (canela, anís, clavo de olor)' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '2 oz de jugo de naranja' AS raw, 2 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 3 AS pos
) x WHERE r.slug = 'naranjada-especiada' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('sin-alcohol', 'citrico') WHERE r.slug = 'naranjada-especiada';

-- ---------- Coctel Fisgona ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Coctel Fisgona', 'coctel-fisgona', 'Copón', NULL, 'integrado', NULL, 'Directo', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'highball'), 'Media rodaja de pomelo rosado', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'coctel-fisgona');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Vodka (Smirnoff Raspberry)' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Hierro Quina' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de almíbar de canela' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '1 oz de jugo de pomelo rosado' AS raw, 1 AS amount, 'oz' AS unit, 4 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 5 AS pos
) x WHERE r.slug = 'coctel-fisgona' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vodka', 'hierro-quina', 'pomelo') WHERE r.slug = 'coctel-fisgona';

-- ---------- Limonada exótica ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Limonada exótica', 'limonada-exotica', 'Vaso trago largo', NULL, 'integrado', NULL, 'Directo', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'mocktail'), 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'limonada-exotica');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de almíbar de coriandro' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 3 AS pos
) x WHERE r.slug = 'limonada-exotica' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('sin-alcohol', 'citrico') WHERE r.slug = 'limonada-exotica';

-- ---------- Special Latte ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Special Latte', 'special-latte', 'Copa Hurricane', NULL, 'batido', NULL, 'Batido y colado simple', 'medium', 'digestivo', NULL, NULL, 'Coctel Nº4 del recetario de autor.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'special-latte');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Ron (Havana Club)' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Licor Baileys' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de almíbar de café' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '1 oz de café espresso' AS raw, 1 AS amount, 'oz' AS unit, 4 AS pos
    UNION ALL
    SELECT '1 oz de leche' AS raw, 1 AS amount, 'oz' AS unit, 5 AS pos
) x WHERE r.slug = 'special-latte' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('ron', 'licor-crema', 'cafe') WHERE r.slug = 'special-latte';

-- ---------- Mix Cítrico ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Mix Cítrico', 'mix-citrico', 'Vaso trago largo', NULL, 'batido', NULL, 'Batido y colado simple', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'mocktail'), NULL, NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'mix-citrico');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de almíbar de coriandro' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de pomelo rosado' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '2 oz de jugo de naranja' AS raw, 2 AS amount, 'oz' AS unit, 4 AS pos
) x WHERE r.slug = 'mix-citrico' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('sin-alcohol', 'citrico', 'pomelo') WHERE r.slug = 'mix-citrico';

-- ---------- Daiquiri Clásico ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Daiquiri Clásico', 'daiquiri-clasico', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y doble colado', 'short', NULL, (SELECT id FROM families WHERE slug = 'sour'), 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'daiquiri-clasico');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Ron blanco (Bacardi)' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
) x WHERE r.slug = 'daiquiri-clasico' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('ron', 'citrico') WHERE r.slug = 'daiquiri-clasico';

-- ---------- Cynartini ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Cynartini', 'cynartini', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y doble colado', 'short', 'aperitivo', (SELECT id FROM families WHERE slug = 'sour'), 'Piel de pomelo rosado', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'cynartini');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Cynar' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de jugo de pomelo rosado' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
) x WHERE r.slug = 'cynartini' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('cynar', 'pomelo') WHERE r.slug = 'cynartini';

-- ---------- Ponche de Primavera Ruso ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Ponche de Primavera Ruso', 'ponche-de-primavera-ruso', 'Vaso trago largo', NULL, 'batido', NULL, 'Batido y doble colado', 'long', NULL, (SELECT id FROM families WHERE slug = 'sour'), NULL, 'También conocido como Russian Spring Punch.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'ponche-de-primavera-ruso');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Vodka' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Licor de Cassis' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de pomelo rosado' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 4 AS pos
) x WHERE r.slug = 'ponche-de-primavera-ruso' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vodka', 'cassis', 'pomelo') WHERE r.slug = 'ponche-de-primavera-ruso';

-- ---------- De Milán a Padua ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'De Milán a Padua', 'de-milan-a-padua', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y doble colado', 'short', 'aperitivo', (SELECT id FROM families WHERE slug = 'sour'), NULL, NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'de-milan-a-padua');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1.5 oz de Campari' AS raw, 1.5 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1.5 oz de Aperol' AS raw, 1.5 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '2 oz de jugo de limón' AS raw, 2 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '2 oz de jugo de naranja' AS raw, 2 AS amount, 'oz' AS unit, 4 AS pos
) x WHERE r.slug = 'de-milan-a-padua' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('campari', 'aperol', 'citrico') WHERE r.slug = 'de-milan-a-padua';

-- ---------- Espresso Ferroviario ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Espresso Ferroviario', 'espresso-ferroviario', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y doble colado', 'short', 'digestivo', NULL, '4 granos de café', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'espresso-ferroviario');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1.5 oz de Whisky' AS raw, 1.5 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Licor Amargo Obrero' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de café espresso' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '0.5 oz de almíbar especiado (anís, canela, clavo de olor)' AS raw, 0.5 AS amount, 'oz' AS unit, 4 AS pos
) x WHERE r.slug = 'espresso-ferroviario' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('whisky', 'amargo-obrero', 'cafe') WHERE r.slug = 'espresso-ferroviario';

-- ---------- Espresso Martini ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Espresso Martini', 'espresso-martini', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y doble colado', 'short', 'digestivo', NULL, '4 granos de café', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'espresso-martini');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1.5 oz de Vodka' AS raw, 1.5 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Licor de café' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de café espresso' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '0.5 oz de almíbar simple' AS raw, 0.5 AS amount, 'oz' AS unit, 4 AS pos
) x WHERE r.slug = 'espresso-martini' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vodka', 'cafe', 'licor-cafe') WHERE r.slug = 'espresso-martini';

-- ---------- Última Palabra ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Última Palabra', 'ultima-palabra', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y doble colado', 'short', NULL, NULL, 'Piel de pomelo', 'Versión local del Last Word.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'ultima-palabra');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '0.75 oz de Dry Gin' AS raw, 0.75 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '0.75 oz de Licor de cereza' AS raw, 0.75 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '0.75 oz de Licor de hierbas' AS raw, 0.75 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '0.75 oz de jugo de limón' AS raw, 0.75 AS amount, 'oz' AS unit, 4 AS pos
) x WHERE r.slug = 'ultima-palabra' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('gin', 'chartreuse', 'citrico') WHERE r.slug = 'ultima-palabra';

-- ---------- El Turrón ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'El Turrón', 'el-turron', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y colado simple', 'short', 'digestivo', NULL, 'Polvo de cacao y canela', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'el-turron');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Ron' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Malibú' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de Amarula' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
) x WHERE r.slug = 'el-turron' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('ron', 'malibu', 'licor-crema') WHERE r.slug = 'el-turron';

-- ---------- Amaretto Sour ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Amaretto Sour', 'amaretto-sour', 'Vaso Old Fashioned', NULL, 'batido', NULL, 'Batido y colado simple', 'short', NULL, (SELECT id FROM families WHERE slug = 'sour'), 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'amaretto-sour');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Amaretto' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '1 clara de huevo' AS raw, 1 AS amount, NULL AS unit, 4 AS pos
) x WHERE r.slug = 'amaretto-sour' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('amaretto', 'con-huevo', 'citrico') WHERE r.slug = 'amaretto-sour';

-- ---------- Limoncello Chiara ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Limoncello Chiara', 'limoncello-chiara', 'Copa Cóctel', NULL, 'integrado', NULL, 'Directo', 'short', 'all_day', NULL, 'Piel de naranja', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'limoncello-chiara');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Limoncello' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '2 oz de jugo de naranja' AS raw, 2 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '0.5 oz de jugo de limón' AS raw, 0.5 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 4 AS pos
) x WHERE r.slug = 'limoncello-chiara' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('triple-sec', 'citrico') WHERE r.slug = 'limoncello-chiara';

-- ---------- Negroni ahumado ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Negroni ahumado', 'negroni-ahumado', 'Vaso Old Fashioned', 'En cubos', 'refrescado_directo', NULL, 'Batido cubano', 'short', 'digestivo', (SELECT id FROM families WHERE slug = 'trio'), 'Canela en rama', 'Ídem Negroni, ahumado con clavo de olor, anís estrellado, canela y cacao.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'negroni-ahumado');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Campari' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Vermut rojo' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de Gin' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
) x WHERE r.slug = 'negroni-ahumado' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('campari', 'vermut', 'gin') WHERE r.slug = 'negroni-ahumado';

-- ---------- Molino Orange ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Molino Orange', 'molino-orange', 'Copa Cóctel', NULL, 'refrescado_directo', NULL, 'Refrescado', 'short', 'digestivo', NULL, 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'molino-orange');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1 oz de Brandy (o coñac)' AS raw, 1 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Licor de Damasco (base brandy)' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de Triple Sec' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
) x WHERE r.slug = 'molino-orange' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('brandy', 'triple-sec') WHERE r.slug = 'molino-orange';

-- ---------- Dry Martini Clásic ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Dry Martini Clásic', 'dry-martini-clasic', 'Copa Cóctel', NULL, 'refrescado_directo', NULL, 'Refrescado', 'short', 'aperitivo', (SELECT id FROM families WHERE slug = 'duo'), 'Piel de limón', 'Gin macerado en cardamomo.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'dry-martini-clasic');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1.5 oz de Gin macerado en cardamomo' AS raw, 1.5 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Vermut seco' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
) x WHERE r.slug = 'dry-martini-clasic' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('gin', 'vermut') WHERE r.slug = 'dry-martini-clasic';

-- ---------- Vesper Litoraleño ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Vesper Litoraleño', 'vesper-litoraleno', 'Copa Cóctel', NULL, 'refrescado_directo', NULL, 'Refrescado', 'short', 'aperitivo', (SELECT id FROM families WHERE slug = 'duo'), 'Piel de limón', 'Vodka macerado en pimienta de Jamaica.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'vesper-litoraleno');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1.5 oz de Vodka macerado en pimienta de Jamaica' AS raw, 1.5 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Vermut blanco' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
) x WHERE r.slug = 'vesper-litoraleno' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vodka', 'vermut') WHERE r.slug = 'vesper-litoraleno';

-- ---------- La Madrina ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'La Madrina', 'la-madrina', 'Copa Cóctel', NULL, 'refrescado_directo', NULL, 'Refrescado', 'short', 'digestivo', (SELECT id FROM families WHERE slug = 'duo'), NULL, 'Godmother con vodka.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'la-madrina');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '1.5 oz de Vodka' AS raw, 1.5 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de Amaretto' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
) x WHERE r.slug = 'la-madrina' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vodka', 'amaretto') WHERE r.slug = 'la-madrina';

-- ---------- Amargo Obrero Julep ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Amargo Obrero Julep', 'amargo-obrero-julep', 'Vaso trago largo', 'Molido', 'integrado', NULL, 'Directo, menta activada', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'julep'), 'Menta', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'amargo-obrero-julep');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Amargo Obrero' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT 'Puñado de menta' AS raw, NULL AS amount, NULL AS unit, 4 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 5 AS pos
) x WHERE r.slug = 'amargo-obrero-julep' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('amargo-obrero', 'menta', 'citrico') WHERE r.slug = 'amargo-obrero-julep';

-- ---------- Pombero Julep ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Pombero Julep', 'pombero-julep', 'Vaso trago largo', 'Molido', 'integrado', NULL, 'Directo, menta activada', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'julep'), 'Menta', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'pombero-julep');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Vermut rojo (Pombero)' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de pomelo' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT 'Puñado de menta' AS raw, NULL AS amount, NULL AS unit, 4 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 5 AS pos
) x WHERE r.slug = 'pombero-julep' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('vermut', 'menta', 'pomelo') WHERE r.slug = 'pombero-julep';

-- ---------- Pinn's Julep ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Pinn''s Julep', 'pinns-julep', 'Vaso trago largo', 'Molido', 'integrado', NULL, 'Directo, menta activada', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'julep'), 'Menta', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'pinns-julep');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Pinn''s' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de pomelo' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT 'Puñado de menta' AS raw, NULL AS amount, NULL AS unit, 4 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 5 AS pos
) x WHERE r.slug = 'pinns-julep' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('pimms', 'menta', 'pomelo') WHERE r.slug = 'pinns-julep';

-- ---------- Cynar Julep Clásico ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Cynar Julep Clásico', 'cynar-julep-clasico', 'Vaso trago largo', 'Molido', 'integrado', NULL, 'Directo, menta activada', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'julep'), 'Menta', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'cynar-julep-clasico');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Cynar' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT 'Completar con jugo de pomelo' AS raw, NULL AS amount, NULL AS unit, 4 AS pos
) x WHERE r.slug = 'cynar-julep-clasico' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('cynar', 'menta', 'pomelo', 'citrico') WHERE r.slug = 'cynar-julep-clasico';

-- ---------- Cynar Julep (rediseño) ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Cynar Julep (rediseño)', 'cynar-julep-rediseno', 'Vaso trago largo', 'Molido', 'integrado', NULL, 'Directo, menta activada', 'long', 'all_day', (SELECT id FROM families WHERE slug = 'julep'), 'Menta', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'cynar-julep-rediseno');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Cynar' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar simple' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT 'Completar con soda' AS raw, NULL AS amount, NULL AS unit, 4 AS pos
) x WHERE r.slug = 'cynar-julep-rediseno' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('cynar', 'menta', 'citrico') WHERE r.slug = 'cynar-julep-rediseno';

-- ---------- Reversión de la Margarita ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Reversión de la Margarita', 'reversion-de-la-margarita', 'Copa Cóctel', NULL, 'batido', NULL, 'Batido y colado simple', 'short', NULL, (SELECT id FROM families WHERE slug = 'sour'), 'Piel de limón', NULL
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'reversion-de-la-margarita');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Tequila' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1.5 oz de almíbar de naranja' AS raw, 1.5 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '0.5 oz de jugo de limón' AS raw, 0.5 AS amount, 'oz' AS unit, 3 AS pos
    UNION ALL
    SELECT '0.75 oz de jugo de naranja' AS raw, 0.75 AS amount, 'oz' AS unit, 4 AS pos
    UNION ALL
    SELECT '2 gotas de tabasco' AS raw, 2 AS amount, 'gota' AS unit, 5 AS pos
) x WHERE r.slug = 'reversion-de-la-margarita' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('tequila', 'citrico') WHERE r.slug = 'reversion-de-la-margarita';

-- ---------- Rodilla de Abeja Clásica ----------
INSERT INTO recipes (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)
SELECT 'Rodilla de Abeja Clásica', 'rodilla-de-abeja-clasica', 'Vaso trago largo', NULL, 'batido', NULL, 'Batido y doble colado', 'long', NULL, (SELECT id FROM families WHERE slug = 'sour'), 'Rodaja de limón', 'Bee''s Knees clásico.'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = 'rodilla-de-abeja-clasica');
INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)
SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (
    SELECT '2 oz de Gin' AS raw, 2 AS amount, 'oz' AS unit, 1 AS pos
    UNION ALL
    SELECT '1 oz de almíbar de miel' AS raw, 1 AS amount, 'oz' AS unit, 2 AS pos
    UNION ALL
    SELECT '1 oz de jugo de limón' AS raw, 1 AS amount, 'oz' AS unit, 3 AS pos
) x WHERE r.slug = 'rodilla-de-abeja-clasica' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);
INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)
SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ('gin', 'citrico') WHERE r.slug = 'rodilla-de-abeja-clasica';

COMMIT;
