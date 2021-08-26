SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout des activités à exclure lors des cohortes PP
UPDATE configurations SET value_variable = '["EXP", "ETI"]' WHERE lib_variable LIKE 'PlanPauvrete.Cohorte.Activite.Skip';

-- Activation du module du tutoriel
UPDATE configurations SET value_variable = 'true' WHERE lib_variable LIKE 'Module.Tutoriel';

-- Suppression du parent de l'organisme DREES "SPE"
UPDATE dreesorganismes SET parentid = NULL WHERE lib_dreesorganisme = 'SPE';

-- ********************** Nouveaux type d'orientation **************************
-- Mise à jour de l aconfiguration pour avoir la liste des types d'orientation par parent
UPDATE configurations SET value_variable = 'true' WHERE lib_variable LIKE 'with_parentid';

-- ***************** Création des nouveaux type d'orientation ******************

-- Modification des noms des types d'orientations actuels
UPDATE typesorients SET lib_type_orient = 'Sociale - Sociale' WHERE id = 3;
UPDATE typesorients SET lib_type_orient = 'Professionnelle - Professionnelle' WHERE id = 1;

-- Création des nouveaux parents
INSERT INTO typesorients(lib_type_orient, code_type_orient )
SELECT 'Professionnelle', 'EMPLOI' WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Professionnelle');

INSERT INTO typesorients(lib_type_orient, code_type_orient )
SELECT 'Sociale', 'SOCIAL' WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Sociale');

-- Mise à jour des anciens types pour être des enfants
UPDATE typesorients
	SET parentid = to2.id, code_type_orient = ''
	FROM typesorients to2
	WHERE to2.lib_type_orient = 'Professionnelle' AND typesorients.lib_type_orient = 'Professionnelle - Professionnelle';

UPDATE typesorients
	SET parentid = to2.id, code_type_orient = ''
	FROM typesorients to2
	WHERE to2.lib_type_orient = 'Sociale' AND typesorients.lib_type_orient = 'Sociale - Sociale';

-- Ajout des nouveaux types d'orientation
INSERT INTO typesorients(lib_type_orient)
	SELECT 'Professionnelle - Régime général travailleur non salarié'
	WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Professionnelle - Régime général travailleur non salarié');

INSERT INTO typesorients(lib_type_orient)
	SELECT 'Professionnelle - Régime agricole travailleur non salarié'
	WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié');

INSERT INTO typesorients(lib_type_orient)
	SELECT 'Sociale - Régime général travailleur non salarié'
	WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Sociale - Régime général travailleur non salarié');

INSERT INTO typesorients(lib_type_orient)
	SELECT 'Sociale - Régime agricole travailleur non salarié'
	WHERE NOT EXISTS (SELECT id FROM typesorients WHERE lib_type_orient = 'Sociale - Régime agricole travailleur non salarié');

UPDATE typesorients
	SET parentid = to2.id
	FROM typesorients to2
	WHERE
		to2.lib_type_orient = 'Professionnelle'
		AND typesorients.lib_type_orient IN ('Professionnelle - Régime général travailleur non salarié', 'Professionnelle - Régime agricole travailleur non salarié');

UPDATE typesorients
	SET parentid = to2.id
	FROM typesorients to2
	WHERE
		to2.lib_type_orient = 'Sociale'
		AND typesorients.lib_type_orient IN ('Sociale - Régime général travailleur non salarié', 'Sociale - Régime agricole travailleur non salarié');

-- ************************* Reprise des donnnées ******************************
-- table decisionsnonorientationsproscovs58 champ typeorient_id
-- ETI - Pro
UPDATE decisionsnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table proposnonorientationsproscovs58
-- champs typeorient_id

-- ETI - Pro
UPDATE proposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE proposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE proposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE proposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- champs covtypeorient_id
-- ETI - Pro
UPDATE proposnonorientationsproscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE proposnonorientationsproscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE proposnonorientationsproscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE proposnonorientationsproscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table proposorientationscovs58
-- champ typeorient_id

-- ETI - Pro
UPDATE proposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE proposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE proposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE proposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- champs covtypeorient_id
-- ETI - Pro
UPDATE proposorientationscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE proposorientationscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposnonorientationsproscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE proposorientationscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE proposorientationscovs58
SET covtypeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		pp.id
	FROM proposorientationscovs58 pp
	INNER JOIN dossierscovs58 d2 ON d2.id = pp.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = pp.covtypeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table decisionsnonorientationsproseps58 champ typeorient_id

-- ETI - Pro
UPDATE decisionsnonorientationsproseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps58 d
	INNER JOIN passagescommissionseps p ON p.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p.dossierep_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsnonorientationsproseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps58 d
	INNER JOIN passagescommissionseps p ON p.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p.dossierep_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsnonorientationsproseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps58 d
	INNER JOIN passagescommissionseps p ON p.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p.dossierep_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsnonorientationsproseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsnonorientationsproseps58 d
	INNER JOIN passagescommissionseps p ON p.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p.dossierep_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p2.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table orientsstructs champ typeorient_id
-- ETI - Pro
UPDATE orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN personnes p ON p.id = o.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = o.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN personnes p ON p.id = o.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = o.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN personnes p ON p.id = o.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = o.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE orientsstructs
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		o.id
	FROM orientsstructs o
	INNER JOIN personnes p ON p.id = o.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = o.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table decisionsproposorientationscovs58 typeorient_id
-- ETI - Pro
UPDATE decisionsproposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientationscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsproposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientationscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsproposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientationscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsproposorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientationscovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table decisionsproposorientssocialescovs58 champ typeorient_id
-- ETI - Pro
UPDATE decisionsproposorientssocialescovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientssocialescovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsproposorientssocialescovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientssocialescovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsproposorientssocialescovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientssocialescovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsproposorientssocialescovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposorientssocialescovs58 d
	INNER JOIN passagescovs58 p ON p.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p.dossiercov58_id
	INNER JOIN personnes p2 ON p2.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table regressionsorientationscovs58 champ typeorient_id
-- ETI - Pro
UPDATE regressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationscovs58 r
	INNER JOIN dossierscovs58 d2 ON d2.id = r.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE regressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationscovs58 r
	INNER JOIN dossierscovs58 d2 ON d2.id = r.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE regressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationscovs58 r
	INNER JOIN dossierscovs58 d2 ON d2.id = r.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE regressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationscovs58 r
	INNER JOIN dossierscovs58 d2 ON d2.id = r.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table decisionsregressionsorientationscovs58 typeorient_id
-- ETI - Pro
UPDATE decisionsregressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsregressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsregressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsregressionsorientationscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table regressionsorientationseps58 typeorient_id

-- ETI - Pro
UPDATE regressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationseps58 r
	INNER JOIN dossierseps d ON d.id = r.dossierep_id
	INNER JOIN personnes p ON p.id = d.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE regressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationseps58 r
	INNER JOIN dossierseps d ON d.id = r.dossierep_id
	INNER JOIN personnes p ON p.id = d.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE regressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationseps58 r
	INNER JOIN dossierseps d ON d.id = r.dossierep_id
	INNER JOIN personnes p ON p.id = d.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE regressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		r.id
	FROM regressionsorientationseps58 r
	INNER JOIN dossierseps d ON d.id = r.dossierep_id
	INNER JOIN personnes p ON p.id = d.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = r.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table decisionsregressionsorientationseps58 typeorient_id
-- ETI - Pro
UPDATE decisionsregressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationseps58 d
	INNER JOIN passagescommissionseps p2 ON p2.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p2.dossierep_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsregressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationseps58 d
	INNER JOIN passagescommissionseps p2 ON p2.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p2.dossierep_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsregressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationseps58 d
	INNER JOIN passagescommissionseps p2 ON p2.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p2.dossierep_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsregressionsorientationseps58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsregressionsorientationseps58 d
	INNER JOIN passagescommissionseps p2 ON p2.id = d.passagecommissionep_id
	INNER JOIN dossierseps d2 ON d2.id = p2.dossierep_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- table decisionsproposnonorientationsproscovs58 typeorient_id
-- ETI - Pro
UPDATE decisionsproposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- EXP - Pro
UPDATE decisionsproposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Professionnelle - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Professionnelle - Professionnelle'
);

-- ETI - Social
UPDATE decisionsproposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime général travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'ETI' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- EXP - Social
UPDATE decisionsproposnonorientationsproscovs58
SET typeorient_id = (SELECT id FROM typesorients t WHERE t.lib_type_orient = 'Sociale - Régime agricole travailleur non salarié')
WHERE id IN (
	SELECT
		d.id
	FROM decisionsproposnonorientationsproscovs58 d
	INNER JOIN passagescovs58 p2 ON p2.id = d.passagecov58_id
	INNER JOIN dossierscovs58 d2 ON d2.id = p2.dossiercov58_id
	INNER JOIN personnes p ON p.id = d2.personne_id
	INNER JOIN activites a ON a.personne_id = p.id
	INNER JOIN typesorients t ON t.id = d.typeorient_id
	WHERE a.act = 'EXP' AND t.lib_type_orient = 'Sociale - Sociale'
);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************