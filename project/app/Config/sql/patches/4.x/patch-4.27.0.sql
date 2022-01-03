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

-- Variables de configuration liées au LDAP
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Ldap.enabled', 'false', 'Active le module LDAP',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Ldap.enabled');

INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Ldap.mail_auto', 'false', 'Active le mail automatique lors d''une tentative de connexion d''un utilisateur LDAP identifié mais non présent dans WebRSA',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Ldap.mail_auto');

INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
SELECT 'Module.Ldap.mail_modele', '"Bonjour,\n\nPour information, l''utilisateur %user% ayant l''adresse mail %email% a tenté de se connecter à WebRSA, mais il n''existe pas en base de données.\n\nCordialement,\nWebRSA"', 'Modèle du corps de mail envoyé automatiquement lors d''une tentative de connexion d''un utilisateur LDAP identifié mais non présent dans WebRSA (voir la variable de configuration Module.Ldap.mail_auto pour l''activer).

Les balises suivantes sont disponibles et modifiées automatiquement lors de la création du mail :
- %user% : nom d''utilisateur ayant essayé de se connecter à WebRSA et trouvé dans le LDAP
- %email% : email de l''utilisateur ayant essayé de se connecter à WebRSA et trouvé dans le LDAP',  current_timestamp, current_timestamp
WHERE NOT EXISTS (SELECT id FROM configurations WHERE lib_variable LIKE 'Module.Ldap.mail_modele');

UPDATE public.configurations
SET configurationscategorie_id = configurationscategories.id
FROM configurationscategories
WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable IN (
    'Exceptionsimpressiontypesorient.affichageprincipal',
    'Module.Ldap.enabled',
    'Module.Ldap.mail_auto',
    'Module.Ldap.mail_modele'
);


-- *****************************************************************************
COMMIT;
-- *****************************************************************************
