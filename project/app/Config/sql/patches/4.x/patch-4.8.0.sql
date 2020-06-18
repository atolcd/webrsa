SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
-- Ajout du module de modification de l'état de dossier
INSERT INTO public.configurations(lib_variable, value_variable, comments_variable, created, modified)
	VALUES
	('Module.ModifEtatDossier.enabled', 'true', 'Activation de la modification de l''état du dossier', current_timestamp, current_timestamp),
	('Module.ModifEtatDossier.etatdos', '{"5":"Droit clos","6":"Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure."}', 'Liste des états possibles pour la modification de l''état des dossiers.
"Z" : "Non défini"
"0" : "Nouvelle demande en attente de décision CD pour ouverture du droit"
"1" : "Droit refusé"
"2" : "Droit ouvert et versable"
"3" : "Droit ouvert et suspendu (le montant du droit est calculable, mais l''existence du droit est remis en cause)"
"4" : "Droit ouvert mais versement suspendu (le montant du droit n''est pas calculable)"
"5" : "Droit clos"
"6" : "Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure."', current_timestamp, current_timestamp);


UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.ModifEtatDossier.enabled';
UPDATE public.configurations SET configurationscategorie_id = configurationscategories.id FROM configurationscategories WHERE configurationscategories.lib_categorie = 'webrsa' AND configurations.lib_variable LIKE 'Module.ModifEtatDossier.etatdos';

-- Ajout du paramétrage pour la modification de l'état de dossier
CREATE TABLE motifsetatsdossiers (
	id serial NOT NULL,
	lib_motif varchar(250) NOT NULL,
	actif int2 NOT NULL DEFAULT 0,
	created timestamp NOT NULL,
	modified timestamp NOT NULL,
	CONSTRAINT motifsetatsdossiers_pkey PRIMARY KEY (id),
	CONSTRAINT motifsetatsdossiers_un UNIQUE (lib_motif)
);

-- Modification de la table historiquesdroits
ALTER TABLE public.historiquesdroits ADD nom varchar(50);
ALTER TABLE public.historiquesdroits ADD prenom varchar(50);
ALTER TABLE public.historiquesdroits ADD motif varchar(255);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************