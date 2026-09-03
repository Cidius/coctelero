-- =====================================================================
--  Migracion 04: normalizar cristaleria a la lista corta.
--
--  Lista final: Vaso trago largo · Vaso Old Fashioned · Copa Cóctel ·
--  Copa Hurricane · (cualquier otra cosa = "Otro" en el admin).
--  Las variantes de Old Fashioned colapsan en "Vaso Old Fashioned".
--
--  Seguro de re-correr.
--    mysql -u USUARIO -p BASE < sql/migracion_04_cristaleria.sql
-- =====================================================================

SET NAMES utf8mb4;

UPDATE recipes SET glassware = 'Vaso Old Fashioned'
WHERE glassware IN (
    'Vaso corto estilo Old Fashioned',
    'Vaso corto',
    'Old Fashioned con hielo',
    'Old Fashioned',
    'Old Fashioned (con hielo en cubos)'
);

UPDATE recipes SET glassware = 'Copa Cóctel'
WHERE glassware IN (
    'Copa Cocktail',
    'Copa Cocktail / Old Fashioned (con hielo en cubos)'
);

UPDATE recipes SET glassware = 'Vaso trago largo'
WHERE glassware = 'Copón de vino tinto / vaso highball';
