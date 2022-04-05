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

-- Création de la table stockant les critères de l'algorithme d'orientation
CREATE TABLE IF NOT EXISTS public.criteresalgorithmeorientation (
	id serial4 NOT NULL,
	ordre int4 NOT NULL,
	libelle text NOT NULL,
	type_orient_parent_id int4 NOT NULL,
	type_orient_enfant_id int4 NOT NULL,
	valeurtag_id int4 NULL,
	actif bool NOT NULL DEFAULT true,
	age_min varchar(10) NOT NULL DEFAULT 'false',
	age_max varchar(10) NOT NULL DEFAULT 'false',
	nb_enfants varchar(10) NOT NULL DEFAULT 'false',
	nb_mois varchar(10) NOT NULL DEFAULT 'false',
	code varchar(25) not null,
	CONSTRAINT criteresalgorithmeorientation_pkey PRIMARY KEY (id),
	CONSTRAINT criteresalgorithmeorientation_valeurtag_id_fk FOREIGN KEY (valeurtag_id) REFERENCES public.valeurstags(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT criteresalgorithmeorientation_type_orient_enfant_id_fk FOREIGN KEY (type_orient_enfant_id) REFERENCES public.typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT criteresalgorithmeorientation_type_orient_parent_id_fk FOREIGN KEY (type_orient_parent_id) REFERENCES public.typesorients(id) ON DELETE CASCADE ON UPDATE CASCADE
);




-- *****************************************************************************
COMMIT;
-- *****************************************************************************
