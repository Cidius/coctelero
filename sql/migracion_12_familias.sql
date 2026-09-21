-- =====================================================================
--  Migracion 12: familias segun la nomenclatura clasica establecida
--  (Old Fashioned, Aromaticos, Negroni, Sour, Daisy, Fizz, Collins,
--  Highball, Julep, Smash, Punch, Cobbler, Flip, Sparkling, Hot
--  drinks, Tiki), con descripcion de cada una.
--
--  - Los 8 nombres que ya existian y siguen en la lista nueva (Sour,
--    Fizz, Collins, Highball, Julep, Smash, Cobbler, Flip) se
--    actualizan in-place: no pierden su id ni las recetas que ya
--    tenian asignadas.
--  - Se agregan las 8 familias nuevas (Old Fashioned, Aromaticos,
--    Negroni, Daisy, Punch, Sparkling, Hot drinks, Tiki).
--  - Las familias viejas que NO estan en la nomenclatura nueva (Duo,
--    Trio, On the Rocks, Colada, Cooler, Crusta, Cup, Sling,
--    Mocktail) se borran. Antes de borrarlas, a cualquier receta que
--    las tuviera asignadas se le saca esa relacion (family_id = NULL,
--    no se toca la receta en si) -- no correspondia mapearlas "a
--    ojo" a la familia nueva mas parecida, mejor reclasificarlas a
--    mano despues desde el admin.
--
--    mysql -u USUARIO -p BASE < sql/migracion_12_familias.sql
--
--  (Base nueva: schema.sql ya lo trae.)
-- =====================================================================

SET NAMES utf8mb4;

ALTER TABLE families
    ADD COLUMN description TEXT NULL AFTER slug;

-- Familias que ya existian y siguen vigentes: se actualiza nombre (por
-- si difiere en mayusculas/tildes), descripcion y orden; conservan su id.
UPDATE families SET description = 'Base + cítrico + endulzante, se agita.', position = 40
    WHERE slug = 'sour';
UPDATE families SET description = 'Un Sour alargado con soda, se sirve sin hielo.', position = 60
    WHERE slug = 'fizz';
UPDATE families SET description = 'Como el Fizz pero servido con hielo, en vaso alto.', position = 70
    WHERE slug = 'collins';
UPDATE families SET description = 'Espirituoso + alargador simple (soda, tónica, gaseosa), sin proceso de agitado.', position = 80
    WHERE slug = 'highball';
UPDATE families SET description = 'Hierbas machacadas + espirituoso + azúcar, sobre hielo picado.', position = 90
    WHERE slug = 'julep';
UPDATE families SET description = 'Como el Julep pero con fruta fresca machacada además de hierbas.', position = 100
    WHERE slug = 'smash';
UPDATE families SET description = 'Fruta + azúcar + vino o licor sobre hielo picado, con pajita corta.', position = 120
    WHERE slug = 'cobbler';
UPDATE families SET description = 'Espirituoso + huevo entero + azúcar, sin lácteos.', position = 130
    WHERE slug = 'flip';

-- Familias nuevas.
INSERT INTO families (name, slug, description, typical_volume, position) VALUES
    ('Old Fashioned', 'old-fashioned',
        'Espirituoso, azúcar y bitters, sin alargador. Base de la coctelería clásica.', NULL, 10),
    ('Aromáticos (Martini/Manhattan)', 'aromaticos',
        'Base + vermut u otro modificador aromático, se revuelve, no se agita.', NULL, 20),
    ('Negroni', 'negroni',
        'Variante de los aromáticos con tres partes iguales (espirituoso, vermut, amargo). Algunos autores la separan como familia propia.', NULL, 30),
    ('Daisy', 'daisy',
        'Un Sour donde el endulzante es un licor en vez de almíbar.', NULL, 50),
    ('Punch', 'punch',
        'Mezcla en volumen para compartir, históricamente con cinco elementos (fuerte, débil, agrio, dulce, especia).', NULL, 110),
    ('Sparkling', 'sparkling',
        'Armado con vino espumante como componente principal.', NULL, 140),
    ('Hot drinks', 'hot-drinks',
        'Servidos calientes (café, agua caliente, manteca derretida como base).', NULL, 150),
    ('Tiki', 'tiki',
        'Múltiples rones + jugos frescos + jarabes especiados (orgeat, falernum).', NULL, 160);

-- Familias viejas que no entran en la nomenclatura nueva: se desvincula
-- cualquier receta que las tuviera (no se borra la receta, solo la
-- relacion) y despues se borra la familia.
UPDATE recipes r
JOIN families f ON f.id = r.family_id
SET r.family_id = NULL
WHERE f.slug IN ('duo', 'trio', 'on-the-rocks', 'colada', 'cooler', 'crusta', 'cup', 'sling', 'mocktail');

DELETE FROM families
WHERE slug IN ('duo', 'trio', 'on-the-rocks', 'colada', 'cooler', 'crusta', 'cup', 'sling', 'mocktail');
