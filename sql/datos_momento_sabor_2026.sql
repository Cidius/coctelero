-- =====================================================================
--  Datos: momento + perfil de sabor (con orden de predominancia) para
--  las recetas existentes. GENERADO por sql/fuente/build_momento_sabor.php
--  a partir de sql/fuente/recipe_flavors_2026.csv -- no editar a mano.
--  Correr DESPUES de sql/migracion_14_predominancia_sabor.sql.
-- =====================================================================

SET NAMES utf8mb4;

-- Mojito (mojito)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'mojito';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mojito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'mojito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'mojito';

-- Rojito (rojito)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'rojito';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'rojito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'rojito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'rojito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'rojito';

-- Cynar Julep (cynar-julep)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'cynar-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'cynar-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'cynar-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'cynar-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'cynar-julep';

-- Pineral Julep (pineral-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'pineral-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'pineral-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'pineral-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'pineral-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'pineral-julep';

-- Hesperidina Julep (hesperidina-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'hesperidina-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'hesperidina-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'hesperidina-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'hesperidina-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'hesperidina-julep';

-- Cassis Julep (cassis-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'cassis-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'cassis-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'cassis-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'cassis-julep';

-- Julep del Giardino (julep-del-giardino)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'julep-del-giardino';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'julep-del-giardino';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'julep-del-giardino';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'julep-del-giardino';

-- Fierro en la espalda (fierro-en-la-espalda)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'fierro-en-la-espalda';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'fierro-en-la-espalda';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'fierro-en-la-espalda';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'fierro-en-la-espalda';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'fierro-en-la-espalda';

-- Mint Julep (mint-julep)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'mint-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mint-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'mint-julep';

-- Pimm's Julep (pimm-s-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'pimm-s-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'pimm-s-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'pimm-s-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'pimm-s-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'pimm-s-julep';

-- Jardín Violeta (jardin-violeta)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'jardin-violeta';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'jardin-violeta';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'jardin-violeta';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'jardin-violeta';

-- Mojito Malibú (mojito-malibu)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'mojito-malibu';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mojito-malibu';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'mojito-malibu';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'mojito-malibu';

-- Rob Roy (rob-roy)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'rob-roy';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'rob-roy';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'rob-roy';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'rob-roy';

-- Dry Martini (dry-martini)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'dry-martini';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'dry-martini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'dry-martini';

-- Claridge Cocktail (claridge-cocktail)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'claridge-cocktail';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'claridge-cocktail';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'claridge-cocktail';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'claridge-cocktail';

-- Hanky Panky (hanky-panky)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'hanky-panky';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'hanky-panky';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'hanky-panky';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'hanky-panky';

-- Mi-To (mi-to)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'mi-to';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mi-to';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'mi-to';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'mi-to';

-- Negroni (negroni)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'negroni';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'negroni';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'negroni';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'negroni';

-- Black Russian (black-russian)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'black-russian';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'black-russian';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'black-russian';

-- Aperol Spritz (aperol-spritz)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'aperol-spritz';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'aperol-spritz';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'aperol-spritz';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'aperol-spritz';

-- Americano (americano)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'americano';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'americano';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'americano';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'americano';

-- Negroni Sbagliato (negroni-sbagliato)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'negroni-sbagliato';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'negroni-sbagliato';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'negroni-sbagliato';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'negroni-sbagliato';

-- Coloradito (coloradito)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'coloradito';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'coloradito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'coloradito';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'coloradito';

-- Ferroviario (ferroviario)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'ferroviario';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'ferroviario';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'ferroviario';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'ferroviario';

-- Old Fashioned (old-fashioned)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'old-fashioned';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'old-fashioned';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'old-fashioned';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'old-fashioned';

-- Aviation (aviation)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'aviation';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'aviation';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'aviation';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'aviation';

-- Pisco Sour (pisco-sour)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'pisco-sour';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'pisco-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'pisco-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'pisco-sour';

-- Daiquiri (daiquiri)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'daiquiri';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'daiquiri';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'daiquiri';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'daiquiri';

-- Daiquiri "Hemingway" (daiquiri-hemingway)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'daiquiri-hemingway';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'daiquiri-hemingway';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'daiquiri-hemingway';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'daiquiri-hemingway';

-- Last Word (last-word)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'last-word';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'last-word';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'last-word';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'last-word';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'last-word';

-- Playmate Martini (playmate-martini)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'playmate-martini';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'playmate-martini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'playmate-martini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'playmate-martini';

-- Tom Collins (tom-collins)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'tom-collins';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'tom-collins';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'tom-collins';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'tom-collins';

-- French 75 (french-75)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'french-75';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'french-75';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'french-75';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'french-75';

-- Citro Caipiroska (citro-caipiroska)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'citro-caipiroska';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'citro-caipiroska';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'citro-caipiroska';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'citro-caipiroska';

-- Bee's Knees (bee-s-knees)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'bee-s-knees';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'bee-s-knees';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'bee-s-knees';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'bee-s-knees';

-- Penicillin (penicillin)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'penicillin';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'penicillin';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'penicillin';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'penicillin';

-- Naked & Famous (naked-famous)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'naked-famous';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'naked-famous';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'naked-famous';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'naked-famous';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'naked-famous';

-- Lucha de clases (lucha-de-clases)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'lucha-de-clases';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'lucha-de-clases';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'lucha-de-clases';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'lucha-de-clases';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'lucha-de-clases';

-- Gin Basil Smash (gin-basil-smash)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'gin-basil-smash';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'gin-basil-smash';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'gin-basil-smash';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'gin-basil-smash';

-- Ernest Happel (ernest-happel)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'ernest-happel';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'ernest-happel';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'ernest-happel';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'ernest-happel';

-- Ron Smash (ron-smash)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'ron-smash';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'ron-smash';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'ron-smash';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'ron-smash';

-- The Broady (the-broady)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'the-broady';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'the-broady';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'the-broady';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'the-broady';

-- Cherry Brandy Sour (cherry-brandy-sour)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'cherry-brandy-sour';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'cherry-brandy-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'cherry-brandy-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'cherry-brandy-sour';

-- Laburante (laburante)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'laburante';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'laburante';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'laburante';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'laburante';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'laburante';

-- Margarita (margarita)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'margarita';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'margarita';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'margarita';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'margarita';

-- Caipirinha (caipirinha)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'caipirinha';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'caipirinha';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'caipirinha';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'caipirinha';

-- Daiquiri Frozen de Frutilla (daiquiri-frozen-de-frutilla)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'daiquiri-frozen-de-frutilla';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'daiquiri-frozen-de-frutilla';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'daiquiri-frozen-de-frutilla';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'daiquiri-frozen-de-frutilla';

-- Straw Baileys Frozen (straw-baileys-frozen)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'straw-baileys-frozen';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'straw-baileys-frozen';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'straw-baileys-frozen';

-- Lemmon Champ (lemmon-champ)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'lemmon-champ';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'lemmon-champ';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'lemmon-champ';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'lemmon-champ';

-- The Coffee Latte Cocktail (the-coffee-latte-cocktail)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'the-coffee-latte-cocktail';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'the-coffee-latte-cocktail';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'the-coffee-latte-cocktail';

-- Limonada (limonada)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'limonada';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'limonada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'limonada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'limonada';

-- Jarra de Limonada (jarra-de-limonada)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'jarra-de-limonada';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'jarra-de-limonada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'jarra-de-limonada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'jarra-de-limonada';

-- Bloody Mary (bloody-mary)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'bloody-mary';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'bloody-mary';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'bloody-mary';

-- Coctel Litoraleño (coctel-litoraleno)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'coctel-litoraleno';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'coctel-litoraleno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'coctel-litoraleno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'coctel-litoraleno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'coctel-litoraleno';

-- Limonada especiada (limonada-especiada)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'limonada-especiada';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'limonada-especiada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'limonada-especiada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'limonada-especiada';

-- Naranjada especiada (naranjada-especiada)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'naranjada-especiada';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'naranjada-especiada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'naranjada-especiada';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'naranjada-especiada';

-- Coctel Fisgona (coctel-fisgona)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'coctel-fisgona';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'coctel-fisgona';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'coctel-fisgona';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'coctel-fisgona';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'coctel-fisgona';

-- Limonada exótica (limonada-exotica)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'limonada-exotica';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'limonada-exotica';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'limonada-exotica';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'limonada-exotica';

-- Special Latte (special-latte)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'special-latte';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'special-latte';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'special-latte';

-- Mix Cítrico (mix-citrico)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'mix-citrico';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mix-citrico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'mix-citrico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'mix-citrico';

-- Daiquiri Clásico (daiquiri-clasico)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'daiquiri-clasico';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'daiquiri-clasico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'daiquiri-clasico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'daiquiri-clasico';

-- Cynartini (cynartini)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'cynartini';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'cynartini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'cynartini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'cynartini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'cynartini';

-- Ponche de Primavera Ruso (ponche-de-primavera-ruso)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'ponche-de-primavera-ruso';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'ponche-de-primavera-ruso';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'ponche-de-primavera-ruso';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'ponche-de-primavera-ruso';

-- De Milán a Padua (de-milan-a-padua)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'de-milan-a-padua';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'de-milan-a-padua';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'de-milan-a-padua';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'de-milan-a-padua';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'de-milan-a-padua';

-- Espresso Ferroviario (espresso-ferroviario)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'espresso-ferroviario';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'espresso-ferroviario';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'espresso-ferroviario';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'espresso-ferroviario';

-- Espresso Martini (espresso-martini)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'espresso-martini';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'espresso-martini';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'espresso-martini';

-- Última Palabra (ultima-palabra)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'ultima-palabra';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'ultima-palabra';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'ultima-palabra';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'ultima-palabra';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'ultima-palabra';

-- El Turrón (el-turron)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'el-turron';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'el-turron';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'el-turron';

-- Amaretto Sour (amaretto-sour)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'amaretto-sour';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'amaretto-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'amaretto-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'amaretto-sour';

-- Limoncello Chiara (limoncello-chiara)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'limoncello-chiara';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'limoncello-chiara';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'limoncello-chiara';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'limoncello-chiara';

-- Negroni ahumado (negroni-ahumado)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'negroni-ahumado';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'negroni-ahumado';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'negroni-ahumado';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'negroni-ahumado';

-- Molino Orange (molino-orange)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'molino-orange';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'molino-orange';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'molino-orange';

-- Dry Martini Clásic (dry-martini-clasic)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'dry-martini-clasic';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'dry-martini-clasic';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'dry-martini-clasic';

-- Vesper Litoraleño (vesper-litoraleno)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'vesper-litoraleno';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'vesper-litoraleno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'seco' WHERE r.slug = 'vesper-litoraleno';

-- La Madrina (la-madrina)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'la-madrina';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'la-madrina';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'la-madrina';

-- Amargo Obrero Julep (amargo-obrero-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'amargo-obrero-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'amargo-obrero-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'amargo-obrero-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'amargo-obrero-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'amargo-obrero-julep';

-- Pombero Julep (pombero-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'pombero-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'pombero-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'pombero-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'pombero-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'pombero-julep';

-- Pinn's Julep (pinn-s-julep)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'pinn-s-julep';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'pinn-s-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'pinn-s-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'pinn-s-julep';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'pinn-s-julep';

-- Cynar Julep Clásico (cynar-julep-clasico)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'cynar-julep-clasico';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'cynar-julep-clasico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'cynar-julep-clasico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'cynar-julep-clasico';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'cynar-julep-clasico';

-- Cynar Julep (rediseño) (cynar-julep-rediseno)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'cynar-julep-rediseno';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'cynar-julep-rediseno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'cynar-julep-rediseno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'cynar-julep-rediseno';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'cynar-julep-rediseno';

-- Margarita Evolution (margarita-evolution)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'margarita-evolution';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'margarita-evolution';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'margarita-evolution';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'margarita-evolution';

-- Rodilla de Abeja Clásica (rodilla-de-abeja-clasica)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'rodilla-de-abeja-clasica';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'rodilla-de-abeja-clasica';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'rodilla-de-abeja-clasica';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'rodilla-de-abeja-clasica';

-- Coctel N°1 (coctel-n-1)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'coctel-n-1';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'coctel-n-1';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'coctel-n-1';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'coctel-n-1';

-- Special Latte (special-latte)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'special-latte';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'special-latte';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'special-latte';

-- Coctel Nº7 (coctel-n-7)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'coctel-n-7';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'coctel-n-7';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'coctel-n-7';

-- Coctel Nº9 (coctel-n-9)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'coctel-n-9';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'coctel-n-9';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'coctel-n-9';

-- Mandarina Traicionera (mandarina-traicionera)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'mandarina-traicionera';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mandarina-traicionera';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'mandarina-traicionera';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'mandarina-traicionera';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'mandarina-traicionera';

-- Jardín Amargo (jardin-amargo)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'jardin-amargo';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'jardin-amargo';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'jardin-amargo';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'jardin-amargo';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'jardin-amargo';

-- Menta Amarga (menta-amarga)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'menta-amarga';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'menta-amarga';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'menta-amarga';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'menta-amarga';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'menta-amarga';

-- Mojito Mentolado (mojito-mentolado)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'mojito-mentolado';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'mojito-mentolado';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'mojito-mentolado';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'mojito-mentolado';

-- Palmetto (palmetto)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'palmetto';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'palmetto';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'palmetto';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'palmetto';

-- Boston Sour (boston-sour)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'boston-sour';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'boston-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'boston-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'boston-sour';

-- Garibaldi (garibaldi)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'garibaldi';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'garibaldi';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'garibaldi';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'garibaldi';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'garibaldi';

-- Gin Basil Smash (gin-basil-smash)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'gin-basil-smash';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'gin-basil-smash';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'gin-basil-smash';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'gin-basil-smash';

-- Sex On The Beach (sex-on-the-beach)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'sex-on-the-beach';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'sex-on-the-beach';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'sex-on-the-beach';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'sex-on-the-beach';

-- John Collins (john-collins)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'john-collins';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'john-collins';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'john-collins';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'john-collins';

-- Amaretto Sour by Morgenthaler (amaretto-sour-by-morgenthaler)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'amaretto-sour-by-morgenthaler';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'amaretto-sour-by-morgenthaler';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'amaretto-sour-by-morgenthaler';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'amaretto-sour-by-morgenthaler';

-- New York Sour (new-york-sour)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'new-york-sour';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'new-york-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'new-york-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'new-york-sour';

-- Whiskey Sour (whiskey-sour)
UPDATE recipes SET moment = 'digestivo' WHERE slug = 'whiskey-sour';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'whiskey-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'whiskey-sour';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'whiskey-sour';

-- Missionary's Downfall (missionary-s-downfall)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'missionary-s-downfall';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'missionary-s-downfall';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'missionary-s-downfall';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'missionary-s-downfall';

-- Sanguinello (sanguinello)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'sanguinello';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'sanguinello';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'sanguinello';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'sanguinello';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'sanguinello';

-- Spiked Arnold (spiked-arnold)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'spiked-arnold';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'spiked-arnold';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'spiked-arnold';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'spiked-arnold';

-- Love me do (love-me-do)
UPDATE recipes SET moment = 'all_day' WHERE slug = 'love-me-do';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'love-me-do';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'love-me-do';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'love-me-do';

-- South Beach (south-beach)
UPDATE recipes SET moment = 'aperitivo' WHERE slug = 'south-beach';
DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id WHERE r.slug = 'south-beach';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 1 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'amargo' WHERE r.slug = 'south-beach';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 2 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'dulce' WHERE r.slug = 'south-beach';
INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) SELECT r.id, fp.id, 3 FROM recipes r JOIN flavor_profiles fp ON fp.slug = 'acido' WHERE r.slug = 'south-beach';

