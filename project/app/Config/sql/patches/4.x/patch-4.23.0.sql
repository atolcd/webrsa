SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.23.0', CURRENT_TIMESTAMP);

-- Création de la variable de configuration permettant d'activer ou non l'algorithme d'orientation
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.AlgorithmeOrientation.enabled', 'false', 'Active le module ''algorithme d''orientation'' spécifique au CD93. @default false',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.enabled');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.AlgorithmeOrientation.enabled';

-- Modification de la valeur par défaut du code_type_orient pour être à NULL
ALTER TABLE public.typesorients ALTER COLUMN code_type_orient SET DEFAULT NULL;

-- Ajout de la colonne capacité maximale dans la table structuresreferentes
ALTER TABLE public.structuresreferentes ADD COLUMN IF NOT EXISTS capacite_max int8 NULL;

-- Création de la variable de configuration permettant de définir les seuils disponibles pour les critères
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.AlgorithmeOrientation.seuils', '', 'Définie les seuils disponibles pour les critères de l''algorithme d''orientation',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.AlgorithmeOrientation.seuils');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.AlgorithmeOrientation.seuils';

-- Création de la table qui contient les liens entre ville / type d'orientation / structure référente
CREATE TABLE IF NOT EXISTS public.structuresreferentes_typesorients_zonesgeographiques (
    id serial4 NOT NULL,
    structurereferente_id int4 NOT NULL,
    typeorient_id int4 NOT NULL,
    zonegeographique_id int4 NOT NULL,
    CONSTRAINT structuresreferentes_typesorients_zonesgeographiques_pkey PRIMARY KEY (id),
    CONSTRAINT structuresreferentes_typesorients_zonesgeographiques_structurereferente_id_fke FOREIGN KEY (structurereferente_id) REFERENCES public.structuresreferentes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT structuresreferentes_typesorients_zonesgeographiques_typeorient_id_fke FOREIGN KEY (typeorient_id) REFERENCES public.typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT structuresreferentes_typesorients_zonesgeographiques_zonegeographique_id_fke FOREIGN KEY (zonegeographique_id) REFERENCES public.zonesgeographiques(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Création de la variable de configuration permettant de stocker l'id du typeorient Association référente
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Typeorient.asso_referente_id', '', 'id du typeorient Association referente',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Typeorient.asso_referente_id');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Typeorient.asso_referente_id';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
