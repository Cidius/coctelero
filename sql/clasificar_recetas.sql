-- =====================================================================
--  Pre-clasifica las 52 recetas del taller: volumen + familia.
--  GENERADO por sql/fuente/build_seed.php - no editar a mano.
--  Correr DESPUES de sql/migracion_01_clasificaciones.sql.
--  Solo toca filas por slug conocido; es seguro re-correrlo.
-- =====================================================================

SET NAMES utf8mb4;

UPDATE recipes SET volume = 'long' WHERE slug = 'mojito';
UPDATE recipes SET volume = 'long' WHERE slug = 'rojito';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'cynar-julep';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'pineral-julep';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'hesperidina-julep';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'cassis-julep';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'julep-del-giardino';
UPDATE recipes SET volume = 'long' WHERE slug = 'fierro-en-la-espalda';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'mint-julep';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'julep') WHERE slug = 'pimm-s-julep';
UPDATE recipes SET volume = 'long' WHERE slug = 'jardin-violeta';
UPDATE recipes SET volume = 'long' WHERE slug = 'mojito-malibu';
UPDATE recipes SET volume = 'short' WHERE slug = 'rob-roy';
UPDATE recipes SET volume = 'short' WHERE slug = 'dry-martini';
UPDATE recipes SET volume = 'short' WHERE slug = 'claridge-cocktail';
UPDATE recipes SET volume = 'short' WHERE slug = 'hanky-panky';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'duo') WHERE slug = 'mi-to';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'trio') WHERE slug = 'negroni';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'duo') WHERE slug = 'black-russian';
UPDATE recipes SET volume = 'long' WHERE slug = 'aperol-spritz';
UPDATE recipes SET volume = 'long' WHERE slug = 'americano';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'trio') WHERE slug = 'negroni-sbagliato';
UPDATE recipes SET volume = 'short' WHERE slug = 'coloradito';
UPDATE recipes SET volume = 'long' WHERE slug = 'ferroviario';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'on-the-rocks') WHERE slug = 'old-fashioned';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'aviation';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'pisco-sour';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'daiquiri';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'daiquiri-hemingway';
UPDATE recipes SET volume = 'short' WHERE slug = 'last-word';
UPDATE recipes SET volume = 'short' WHERE slug = 'playmate-martini';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'collins') WHERE slug = 'tom-collins';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'fizz') WHERE slug = 'french-75';
UPDATE recipes SET volume = 'short' WHERE slug = 'citro-caipiroska';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'bee-s-knees';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'penicillin';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'naked-famous';
UPDATE recipes SET volume = 'long' WHERE slug = 'lucha-de-clases';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'smash') WHERE slug = 'gin-basil-smash';
UPDATE recipes SET volume = 'short' WHERE slug = 'ernest-happel';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'smash') WHERE slug = 'ron-smash';
UPDATE recipes SET volume = 'short' WHERE slug = 'the-broady';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'cherry-brandy-sour';
UPDATE recipes SET volume = 'long' WHERE slug = 'laburante';
UPDATE recipes SET volume = 'short', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'margarita';
UPDATE recipes SET volume = 'short' WHERE slug = 'caipirinha';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'sour') WHERE slug = 'daiquiri-frozen-de-frutilla';
UPDATE recipes SET volume = 'long' WHERE slug = 'straw-baileys-frozen';
UPDATE recipes SET volume = 'long' WHERE slug = 'lemmon-champ';
UPDATE recipes SET volume = 'short' WHERE slug = 'the-coffee-latte-cocktail';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'mocktail') WHERE slug = 'limonada';
UPDATE recipes SET volume = 'long', family_id = (SELECT id FROM families WHERE slug = 'mocktail') WHERE slug = 'jarra-de-limonada';
