SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Version du patch en BDD
INSERT INTO versionpatchsql("version", created) VALUES ('4.27.0', CURRENT_TIMESTAMP);

--Ajout d'un colonne pour la structure référente
ALTER TABLE public.exceptionsimpressionstypesorients ADD COLUMN IF NOT EXISTS structurereferente_id integer;
ALTER TABLE public.exceptionsimpressionstypesorients DROP CONSTRAINT IF EXISTS exceptionsimpressionstypesorients_structurereferente_fk;
ALTER TABLE public.exceptionsimpressionstypesorients ADD CONSTRAINT exceptionsimpressionstypesorients_structurereferente_fk FOREIGN KEY (structurereferente_id) REFERENCES public.structuresreferentes(id);

--Création de la table stockant les zones géographiques des exceptions d'impression
CREATE TABLE IF NOT EXISTS public.excepimprtypesorients_zonesgeographiques (
	id serial4 NOT NULL,
    excepimprtypeorient_id int4 NOT NULL,
	zonegeographique_id int4 NOT NULL,
    CONSTRAINT exceptionimpressiontypeorient_zonegeo_pkey PRIMARY KEY (id),
    CONSTRAINT excepimprtypeorient_id_fkey FOREIGN KEY (excepimprtypeorient_id) REFERENCES public.exceptionsimpressionstypesorients(id) ON DELETE CASCADE ON UPDATE cascade,
    CONSTRAINT zonegeographique_id_fkey FOREIGN KEY (zonegeographique_id) REFERENCES public.zonesgeographiques(id) ON DELETE CASCADE ON UPDATE cascade
);

-- Variable de configuration permettant l'impression automatique des orientations validées
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT
'Exceptionsimpressiontypesorient.affichageprincipal',
'["Exceptionimpressiontypeorient.act","Exceptionimpressiontypeorient.porteurprojet"]',
'Colonnes affichées dans le tableau des exceptions, les autres sont dans une bulle au survol de la ligne.
Choix parmi :
"Exceptionimpressiontypeorient.structurereferente_libelle",
"Exceptionimpressiontypeorient.zonesgeo",
"Exceptionimpressiontypeorient.act",
"Exceptionimpressiontypeorient.porteurprojet"',
current_timestamp,
current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Exceptionsimpressiontypesorient.affichageprincipal');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN ('Exceptionsimpressiontypesorient.affichageprincipal');


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
