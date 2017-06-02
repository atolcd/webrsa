SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************
SELECT add_missing_table_field ('public', 'traitementspcgs66', 'reversedo', 'TYPE_BOOLEANNUMBER');
ALTER TABLE traitementspcgs66 ALTER COLUMN reversedo SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE traitementspcgs66 SET reversedo = '0'::TYPE_BOOLEANNUMBER WHERE reversedo IS NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN reversedo SET NOT NULL;


SELECT add_missing_table_field ('public', 'bilansparcours66', 'personne_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'bilansparcours66', 'bilansparcours66_personne_id_fkey', 'personnes', 'personne_id');
UPDATE bilansparcours66
	SET personne_id = (
		SELECT orientsstructs.personne_id
			FROM orientsstructs
			WHERE orientsstructs.id = orientstruct_id
	) WHERE personne_id IS NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN personne_id SET NOT NULL;

-------------------------------------------------------------------------------------------------------------
-- 20120220 : Ajout de la clé primaire decisiondefautinsertionep66 dans le dossier PCGs
--				une fois ce dernier généré par l'avis émis par l'EP
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'dossierspcgs66', 'decisiondefautinsertionep66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'dossierspcgs66', 'dossierspcgs66_decisiondefautinsertionep66_id_fkey', 'decisionsdefautsinsertionseps66', 'decisiondefautinsertionep66_id');
DROP INDEX IF EXISTS dossierspcgs66_decisiondefautinsertionep66_id_idx;
CREATE UNIQUE INDEX dossierspcgs66_decisiondefautinsertionep66_id_idx ON dossierspcgs66 (decisiondefautinsertionep66_id);

SELECT alter_table_drop_column_if_exists('public', 'questionspcgs66', 'descriptionpdo_id');
SELECT add_missing_table_field ('public', 'questionspcgs66', 'decisionpcg66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'questionspcgs66', 'questionspcgs66_decisionpcg66_id_fkey', 'decisionspcgs66', 'decisionpcg66_id');

SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'defautinsertion', 'TYPE_DEFAUTINSERTIONPCG66');
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'recidive', 'TYPE_NO');
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'phase', 'TYPE_PHASEPCG66');
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'compofoyerpcg66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_compofoyerpcg66_id_fkey', 'composfoyerspcgs66', 'compofoyerpcg66_id');

-------------------------------------------------------------------------------------------------------------
-- 20120222 : Ajout d'une valeur activ/inactif pour les informations paramétrables des dossiers PCGs 66
--			dont on n'aurait plus besoin apr la suite
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'composfoyerspcgs66', 'actif', 'TYPE_NO');
ALTER TABLE composfoyerspcgs66 ALTER COLUMN actif SET DEFAULT 'O';
UPDATE composfoyerspcgs66 SET actif = 'O' WHERE actif IS NULL;
ALTER TABLE composfoyerspcgs66 ALTER COLUMN actif SET NOT NULL;

SELECT add_missing_table_field ('public', 'decisionspcgs66', 'actif', 'TYPE_NO');
ALTER TABLE decisionspcgs66 ALTER COLUMN actif SET DEFAULT 'O';
UPDATE decisionspcgs66 SET actif = 'O' WHERE actif IS NULL;
ALTER TABLE decisionspcgs66 ALTER COLUMN actif SET NOT NULL;

-------------------------------------------------------------------------------------------------------------
-- 20120223 : Ajout d'une clé primaire pointant sur la table des décisions PCGs paramétrabless
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'decisionsdossierspcgs66', 'decisionpcg66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'decisionsdossierspcgs66', 'decisionsdossierspcgs66_decisionpcg66_id_fkey', 'decisionspcgs66', 'decisionpcg66_id');

-- Correction: les valeurs "suspensiondefaut" et "suspensionnonrespect" étaient inversées avec les traductions.
SELECT public.alter_enumtype ( 'TYPE_DECISIONDEFAUTINSERTIONEP66', ARRAY['suspensionnonrespect','suspensiondefaut','maintien','reorientationprofverssoc','reorientationsocversprof','annule','reporte', 'suspensionnonrespecttmp','suspensiondefauttmp'] );

UPDATE decisionsdefautsinsertionseps66 SET decision = 'suspensionnonrespecttmp' WHERE decision = 'suspensiondefaut';
UPDATE decisionsdefautsinsertionseps66 SET decision = 'suspensiondefauttmp' WHERE decision = 'suspensionnonrespect';
UPDATE decisionsdefautsinsertionseps66 SET decision = 'suspensiondefaut' WHERE decision = 'suspensiondefauttmp';
UPDATE decisionsdefautsinsertionseps66 SET decision = 'suspensionnonrespect' WHERE decision = 'suspensionnonrespecttmp';

SELECT public.alter_enumtype ( 'TYPE_DECISIONDEFAUTINSERTIONEP66', ARRAY['suspensionnonrespect','suspensiondefaut','maintien','reorientationprofverssoc','reorientationsocversprof','annule','reporte'] );

-- Correction: ...
SELECT public.alter_enumtype ( 'TYPE_DEFAUTINSERTIONPCG66', ARRAY['nc_cg','nc_pe','nr_cg','nr_pe','nc_no', 'suspensiondefaut_audition_orientation', 'suspensiondefaut_auditionpe', 'suspensionnonrespect_audition', 'suspensionnonrespect_auditionpe', 'suspensiondefaut_audition_nonorientation'] );

UPDATE questionspcgs66 SET defautinsertion = 'suspensiondefaut_audition_orientation' WHERE defautinsertion = 'nc_cg';
UPDATE questionspcgs66 SET defautinsertion = 'suspensiondefaut_auditionpe' WHERE defautinsertion = 'nc_pe';
UPDATE questionspcgs66 SET defautinsertion = 'suspensionnonrespect_audition' WHERE defautinsertion = 'nr_cg';
UPDATE questionspcgs66 SET defautinsertion = 'suspensionnonrespect_auditionpe' WHERE defautinsertion = 'nr_pe';
UPDATE questionspcgs66 SET defautinsertion = 'suspensiondefaut_audition_nonorientation' WHERE defautinsertion = 'nc_no';

SELECT public.alter_enumtype ( 'TYPE_DEFAUTINSERTIONPCG66', ARRAY['suspensiondefaut_audition_orientation', 'suspensiondefaut_auditionpe', 'suspensionnonrespect_audition', 'suspensionnonrespect_auditionpe', 'suspensiondefaut_audition_nonorientation'] );


ALTER TABLE bilansparcours66 ALTER COLUMN orientstruct_id DROP NOT NULL;

-------------------------------------------------------------------------------------------------------------
-- 20120229 : Ajout de tables supplémentaires afin de mettre en place le module Courriers
--              dans les traitementspcgs66
-------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS piecestypescourrierspcgs66 CASCADE;
DROP TABLE IF EXISTS typescourrierspcgs66 CASCADE;


CREATE TABLE typescourrierspcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	name                            VARCHAR(250) NOT NULL,
        created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE typescourrierspcgs66 IS 'Types de courriers liés à un traitement PCG (cg66)';
DROP INDEX IF EXISTS typescourrierspcgs66_name_idx;
CREATE INDEX typescourrierspcgs66_name_idx ON typescourrierspcgs66(name);

CREATE TABLE piecestypescourrierspcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	name                            VARCHAR(250) NOT NULL,
        typecourrierpcg66_id            INTEGER NOT NULL REFERENCES typescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
        created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE piecestypescourrierspcgs66 IS 'Pièces pour les courriers liés à un traitement PCG (cg66)';
DROP INDEX IF EXISTS piecestypescourrierspcgs66_typecourrierpcg66_id_idx;
DROP INDEX IF EXISTS piecestypescourrierspcgs66_name_idx;
CREATE INDEX piecestypescourrierspcgs66_typecourrierpcg66_id_idx ON piecestypescourrierspcgs66(typecourrierpcg66_id);
CREATE INDEX piecestypescourrierspcgs66_name_idx ON piecestypescourrierspcgs66(name);

-------------------------------------------------------------------------------------------------------------
-- 20120301 : Ajout d'une clé manquante dans la table traitementspcgs66
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'traitementspcgs66', 'typecourrierpcg66_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'traitementspcgs66', 'traitementspcgs66_typecourrierpcg66_id_fkey', 'typescourrierspcgs66', 'typecourrierpcg66_id');
DROP INDEX IF EXISTS traitementspcgs66_typecourrierpcg66_id_idx;

-------------------------------------------------------------------------------------------------------------
-- 20120301 : Ajout d'une table de liaison entre la table traitementspcgs66 et la table piecestypescourrierspcgs66
-------------------------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS piecestraitementspcgs66 CASCADE;
CREATE TABLE piecestraitementspcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	traitementpcg66_id              INTEGER NOT NULL REFERENCES traitementspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	piecetypecourrierpcg66_id       INTEGER NOT NULL REFERENCES piecestypescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire                     TEXT DEFAULT NULL,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE piecestraitementspcgs66 IS 'Table de liaison entre les traitements PCG et les pièces liées à un type de courrier PCG (cg66)';
DROP INDEX IF EXISTS piecestraitementspcgs66_piecetypecourrierpcg66_id_idx;
DROP INDEX IF EXISTS piecestraitementspcgs66_traitementpcg66_id_idx;
CREATE INDEX piecestraitementspcgs66_piecetypecourrierpcg66_id_idx ON piecestraitementspcgs66(piecetypecourrierpcg66_id);
CREATE INDEX piecestraitementspcgs66_traitementpcg66_id_idx ON piecestraitementspcgs66(traitementpcg66_id);

-- 20120319: une entrée de aidesapres66 possède une et une seule entrée de fraisdeplacements66

DROP INDEX IF EXISTS fraisdeplacements66_aideapre66_id_idx;
CREATE UNIQUE INDEX fraisdeplacements66_aideapre66_id_idx ON fraisdeplacements66(aideapre66_id);


-------------------------------------------------------------------------------------------------------------
-- 20120321 : Ajout d'une table pour les propositions de décision du CER du CG66
-------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS proposdecisionscers66 CASCADE;
CREATE TABLE proposdecisionscers66 (
  	id 								SERIAL NOT NULL PRIMARY KEY,
    contratinsertion_id             INTEGER NOT NULL REFERENCES contratsinsertion(id) ON DELETE CASCADE ON UPDATE CASCADE,
    isvalidcer						TYPE_NO NOT NULL DEFAULT 'N',
    datevalidcer					DATE NOT NULL,
    motifficheliaison               TEXT DEFAULT NULL,
    motifnotifnonvalid              TEXT DEFAULT NULL,
    created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE proposdecisionscers66 IS 'Table de proposition de décisiondu CER (cg66)';
DROP INDEX IF EXISTS proposdecisionscers66_contratinsertion_id_idx;
DROP INDEX IF EXISTS proposdecisionscers66_isvalidcer_idx;
CREATE UNIQUE INDEX proposdecisionscers66_contratinsertion_id_idx ON proposdecisionscers66(contratinsertion_id);
CREATE INDEX proposdecisionscers66_isvalidcer_idx ON proposdecisionscers66(isvalidcer);

DROP TABLE IF EXISTS motifscersnonvalids66 CASCADE;
CREATE TABLE motifscersnonvalids66 (
  	id 								SERIAL NOT NULL PRIMARY KEY,
    name							VARCHAR(250) NOT NULL,
    created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifscersnonvalids66 IS 'Table de paramétrage des motifs de non validation d''un CET(cg66)';
DROP INDEX IF EXISTS motifscersnonvalids66_name_idx;
CREATE UNIQUE INDEX motifscersnonvalids66_name_idx ON motifscersnonvalids66(name);


DROP TABLE IF EXISTS motifscersnonvalids66_proposdecisionscers66 CASCADE;
CREATE TABLE motifscersnonvalids66_proposdecisionscers66 (
  	id 								SERIAL NOT NULL PRIMARY KEY,
	propodecisioncer66_id           INTEGER NOT NULL REFERENCES proposdecisionscers66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	motifcernonvalid66_id       	INTEGER NOT NULL REFERENCES motifscersnonvalids66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifscersnonvalids66_proposdecisionscers66 IS 'Table de liaison entre les propositions de décisions du CER et les motifs en cas de non validation (cg66)';
DROP INDEX IF EXISTS motifscersnonvalids66_proposdecisionscers66_propodecisioncer66_id_idx;
DROP INDEX IF EXISTS motifscersnonvalids66_proposdecisionscers66_motifcernonvalid66_id_idx;
CREATE INDEX motifscersnonvalids66_proposdecisionscers66_propodecisioncer66_id_idx ON motifscersnonvalids66_proposdecisionscers66(propodecisioncer66_id);
CREATE INDEX motifscersnonvalids66_proposdecisionscers66_motifcernonvalid66_id_idx ON motifscersnonvalids66_proposdecisionscers66(motifcernonvalid66_id);


-------------------------------------------------------------------------------------------------------------
-- 20120322 : Ajout d'une valeur dans l'enum de position du CER
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'contratsinsertion', 'datenotification', 'DATE');

 SELECT public.alter_enumtype ( 'TYPE_POSITIONCER', ARRAY['encours', 'attvalid', 'annule', 'fincontrat', 'encoursbilan', 'attrenouv', 'perime', 'nonvalide', 'attsignature', 'valid', 'nonvalid', 'validnotifie', 'nonvalidnotifie'] );



-------------------------------------------------------------------------------------------------------------
-- 20120402 : Ajout d'une table supplémentaire pour la liste des modèles liés aux types de courrier PCG66
-------------------------------------------------------------------------------------------------------------

-------------------------------------------------------------------------------------------------------------
-- 20120402 : Ajout d'une table supplémentaire pour la liste des modèles liés aux types de courrier PCG66
-------------------------------------------------------------------------------------------------------------

DROP TABLE IF EXISTS modelestypescourrierspcgs66 CASCADE;
CREATE TABLE modelestypescourrierspcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	name                            VARCHAR(250) NOT NULL,
	typecourrierpcg66_id            INTEGER NOT NULL REFERENCES typescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	modeleodt			VARCHAR(250) NOT NULL,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE modelestypescourrierspcgs66 IS 'Modèles de courriers PCG (cg66)';
DROP INDEX IF EXISTS modelestypescourrierspcgs66_typecourrierpcg66_id_idx;
DROP INDEX IF EXISTS modelestypescourrierspcgs66_name_idx;
DROP INDEX IF EXISTS modelestypescourrierspcgs66_modeleodt_idx;
CREATE INDEX modelestypescourrierspcgs66_typecourrierpcg66_id_idx ON modelestypescourrierspcgs66(typecourrierpcg66_id);
CREATE INDEX modelestypescourrierspcgs66_name_idx ON modelestypescourrierspcgs66(name);
CREATE INDEX modelestypescourrierspcgs66_modeleodt_idx ON modelestypescourrierspcgs66(modeleodt);


DROP TABLE IF EXISTS piecestypescourrierspcgs66 CASCADE;
DROP TABLE IF EXISTS piecesmodelestypescourrierspcgs66 CASCADE;
CREATE TABLE piecesmodelestypescourrierspcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	name                            VARCHAR(250) NOT NULL,
	modeletypecourrierpcg66_id            INTEGER NOT NULL REFERENCES modelestypescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE piecesmodelestypescourrierspcgs66 IS 'Pièces pour les modèles de courriers PCG (cg66)';
DROP INDEX IF EXISTS piecesmodelestypescourrierspcgs66_modeletypecourrierpcg66_id_idx;
DROP INDEX IF EXISTS piecesmodelestypescourrierspcgs66_name_idx;
CREATE INDEX piecesmodelestypescourrierspcgs66_modeletypecourrierpcg66_id_idx ON piecesmodelestypescourrierspcgs66(modeletypecourrierpcg66_id);
CREATE INDEX piecesmodelestypescourrierspcgs66_name_idx ON piecesmodelestypescourrierspcgs66(name);


DROP TABLE IF EXISTS piecestraitementspcgs66 CASCADE;
DROP TABLE IF EXISTS modelestraitementspcgs66 CASCADE;
CREATE TABLE modelestraitementspcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	traitementpcg66_id              INTEGER NOT NULL REFERENCES traitementspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	modeletypecourrierpcg66_id       INTEGER NOT NULL REFERENCES modelestypescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	commentaire                     TEXT DEFAULT NULL,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE modelestraitementspcgs66 IS 'Table de liaison entre les traitements PCG et les modèles de courrier PCG (cg66)';
DROP INDEX IF EXISTS modelestraitementspcgs66_modeletypecourrierpcg66_id_idx;
DROP INDEX IF EXISTS modelestraitementspcgs66_traitementpcg66_id_idx;
CREATE INDEX modelestraitementspcgs66_modeletypecourrierpcg66_id_idx ON modelestraitementspcgs66(modeletypecourrierpcg66_id);
CREATE INDEX modelestraitementspcgs66_traitementpcg66_id_idx ON modelestraitementspcgs66(traitementpcg66_id);



DROP TABLE IF EXISTS modelestraitementspcgs66_piecesmodelestypescourrierspcgs66 CASCADE;
DROP TABLE IF EXISTS mtpcgs66_pmtcpcgs66 CASCADE;
CREATE TABLE mtpcgs66_pmtcpcgs66 (
  	id 				SERIAL NOT NULL PRIMARY KEY,
	modeletraitementpcg66_id              INTEGER NOT NULL REFERENCES modelestraitementspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	piecemodeletypecourrierpcg66_id       INTEGER NOT NULL REFERENCES piecesmodelestypescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE mtpcgs66_pmtcpcgs66 IS 'Table de liaison pour stocker les pièces liées à un modèle d''un traitement  PCG (cg66) (NB normalement devrait porter le nom de modelestraitementspcgs66_piecesmodelestypescourrierspcgs66)';
DROP INDEX IF EXISTS mtpcgs66_pmtcpcgs66_modeletraitementpcg66_id_idx;
DROP INDEX IF EXISTS mtpcgs66_pmtcpcgs66_piecemodeletypecourrierpcg66_id_idx;
CREATE INDEX mtpcgs66_pmtcpcgs66_modeletraitementpcg66_id_idx ON mtpcgs66_pmtcpcgs66(modeletraitementpcg66_id);
CREATE INDEX mtpcgs66_pmtcpcgs66_piecemodeletypecourrierpcg66_id_idx ON mtpcgs66_pmtcpcgs66(piecemodeletypecourrierpcg66_id);

DROP INDEX IF EXISTS modelestraitementspcgs66_piecesmodelestypescourrierspcgs66_piecemodeletypecourrierpcg66_id_idx;
DROP INDEX IF EXISTS modelestraitementspcgs66_piecesmodelestypescourrierspcgs66_modeletraitementpcg66_id_idx;

-------------------------------------------------------------------------------------------------------------
-- 20120402 : Ajout d'une table supplémentaire pour la liste des modèles liés aux types de courrier PCG66
-------------------------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS modelestypescourrierspcgs66_situationspdos CASCADE;
CREATE TABLE modelestypescourrierspcgs66_situationspdos (
  	id 								SERIAL NOT NULL PRIMARY KEY,
	modeletypecourrierpcg66_id           INTEGER NOT NULL REFERENCES modelestypescourrierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	situationpdo_id       	INTEGER NOT NULL REFERENCES situationspdos(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created							TIMESTAMP WITHOUT TIME ZONE,
	modified						TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE modelestypescourrierspcgs66_situationspdos IS 'Table de liaison entre les modèles de courriers PCGs et les motifs concernant la personne (PCG cg66)';
DROP INDEX IF EXISTS modelestypescourrierspcgs66_situationspdos_modeletypecourrierpcg66_id_idx;
DROP INDEX IF EXISTS modelestypescourrierspcgs66_situationspdos_situationpdo_id_idx;
CREATE INDEX modelestypescourrierspcgs66_situationspdos_modeletypecourrierpcg66_id_idx ON modelestypescourrierspcgs66_situationspdos(modeletypecourrierpcg66_id);
CREATE INDEX modelestypescourrierspcgs66_situationspdos_situationpdo_id_idx ON modelestypescourrierspcgs66_situationspdos(situationpdo_id);

-------------------------------------------------------------------------------------------------------------
-- 20120503 : Ajout d'une contrainte d'unicité dans la table modelestraitementspcgs66
--	Il n'y a qu'un seul modèle de traitement pour un traitement donné
-------------------------------------------------------------------------------------------------------------
DROP INDEX IF EXISTS modelestraitementspcgs66_traitementpcg66_id_idx;
CREATE UNIQUE INDEX modelestraitementspcgs66_traitementpcg66_id_idx ON modelestraitementspcgs66(traitementpcg66_id);

/* -----------------------------------------------------------------------------
	Nouveau Gedooo
	1°) FIXME: il faudrait remplacer, mais une mise à jour sur acos casserait l'arbre:
		- Gedooos:contratinsertion par Contratsinsertion:impression
		- Gedooos:apre par Apres:impression
		- Gedooos:relanceapre par Relancesapres:impression

	2°) FIXME: faire les traductions (pour la page de droits)

	3°) Nettoyage du code:
		a°) la tables montantsconsommes (et son modèle) n'ont pas l'air d'être utilisés -> grep -nr "\(Montantconsomme\|montantsconsommes\)" app | grep -v "\(\.svn\|\.sql\|/tests/\)"

	4°) Dans les modèles odt suivants, on a à présent de vraies dates / heures (revoir les documents):
		- Contratinsertion/notificationop.odt
			* Personne.dtnai
			* Contratinsertion.datevalidation_ci
			* Contratinsertion.dd_ci
			* Contratinsertion.df_ci
			* Dossier.dtdemrsa
		- CUI/cui.odt
			* Personne.dtnai
		- Rendezvous
			* Rendezvous.daterdv
			* Rendezvous.heurerdv

	5°) Dans les modèles odt suivants, des chemins ont changé:
		- Rendezvous
			* dossier_rsa_xxxx -> dossier_xxxx
			* rendezvous.referent_id -> referent_qual, referent_nom, referent_prenom
			* Les enregistrements de modèles suivants ne seront plus présents: Entretien, Fichiermodule, Sanctionrendezvousep58


	6°) FIXME: au 66, problème lors de l'impression
		- /contratsinsertion/notifbenef/9588 (Propodecisioncer66.isvalidcer est vide)
		- /cohortesci/valides/page:1/Filtre__date_saisi_ci:0/Filtre__decision_ci:V/Dossier__dernier:1/Situationdossierrsa__etatdosrsa_choice:0/sort:Contratinsertion.decision_ci/direction:asc

	7°) FIXME: Undefined offset au 66: /contratsinsertion/index/55245

	8°) FIXME: normalement les tables nonorientationsproseps66 et decisionsnonorientationsproseps66 (+ modèles liés) ne devraient plus exister (attention au qd....), éventuellement, changer l'enum de themeep

----------------------------------------------------------------------------- */

UPDATE acos SET alias = 'Apres66:impression' WHERE alias = 'Apres66:apre';
UPDATE acos SET alias = 'Cohortescomitesapres:impression' WHERE alias = 'Cohortescomitesapres:notificationscomitegedooo';
UPDATE acos SET alias = 'Cuis:impression' WHERE alias = 'Cuis:gedooo';
UPDATE acos SET alias = 'Rendezvous:impression' WHERE alias = 'Rendezvous:gedooo';
UPDATE acos SET alias = 'Recoursapres:impression' WHERE alias = 'Recoursapres:notificationsrecoursgedooo';
UPDATE acos SET alias = 'Etatsliquidatifs:impression' WHERE alias = 'Etatsliquidatifs:impressiongedoooapres';
UPDATE acos SET alias = 'Etatsliquidatifs:impressions' WHERE alias = 'Etatsliquidatifs:impressioncohorte';


-------------------------------------------------------------------------------------------------------------
-- 20120503 : Modification du type de la colonne name dans la table piecesmodelestypescourrierspcgs66
--			car les valeurs pouvant être prises par ces pièces peuvent être très longues
--			ex: si oui, alors telle pièce, sinon telle pièce ...
-------------------------------------------------------------------------------------------------------------
-- ALTER TABLE piecesmodelestypescourrierspcgs66 ALTER COLUMN name TYPE TEXT;

-------------------------------------------------------------------------------------------------------------
-- 20120523 : Ajout d'une  clé étrangère du CER dans le dossier PCG suite à la création d'un dossie PCG66
--				si le CER est une CER Particulier
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'dossierspcgs66', 'contratinsertion_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'dossierspcgs66', 'dossierspcgs66_contratinsertion_id_fkey', 'contratsinsertion', 'contratinsertion_id');
DROP INDEX IF EXISTS dossierspcgs66_contratinsertion_id_idx;
CREATE UNIQUE INDEX dossierspcgs66_contratinsertion_id_idx ON dossierspcgs66 (contratinsertion_id);


SELECT add_missing_table_field ('public', 'originespdos', 'cerparticulier', 'TYPE_NO');
ALTER TABLE originespdos ALTER COLUMN cerparticulier SET DEFAULT 'N'::TYPE_NO;
UPDATE originespdos SET cerparticulier = 'N' WHERE cerparticulier IS NULL;
ALTER TABLE originespdos ALTER COLUMN cerparticulier SET NOT NULL;

SELECT add_missing_table_field ('public', 'typespdos', 'cerparticulier', 'TYPE_NO');
ALTER TABLE typespdos ALTER COLUMN cerparticulier SET DEFAULT 'N'::TYPE_NO;
UPDATE typespdos SET cerparticulier = 'N' WHERE cerparticulier IS NULL;
ALTER TABLE typespdos ALTER COLUMN cerparticulier SET NOT NULL;

SELECT add_missing_table_field ('public', 'decisionspdos', 'cerparticulier', 'TYPE_NO');
ALTER TABLE decisionspdos ALTER COLUMN cerparticulier SET DEFAULT 'N'::TYPE_NO;
UPDATE decisionspdos SET cerparticulier = 'N' WHERE cerparticulier IS NULL;
ALTER TABLE decisionspdos ALTER COLUMN cerparticulier SET NOT NULL;

SELECT add_missing_table_field ('public', 'decisionspdos', 'decisioncerparticulier', 'CHAR(1)');


DROP INDEX IF EXISTS piecesmodelestypescourrierspcgs66_name_modeletypecourrierpcg66_id_idx;
CREATE UNIQUE INDEX piecesmodelestypescourrierspcgs66_name_modeletypecourrierpcg66_id_idx ON piecesmodelestypescourrierspcgs66(name, modeletypecourrierpcg66_id );

-- -----------------------------------------------------------------------------------------------------------
-- Ajout de la clé étrangère vers les historiques de Pôle Emploi
-- -----------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field( 'public', 'sanctionseps58', 'historiqueetatpe_id', 'INTEGER' );
SELECT public.add_missing_constraint( 'public', 'sanctionseps58', 'sanctionseps58_historiqueetatpe_id_fkey', 'historiqueetatspe', 'historiqueetatpe_id'  );
DROP INDEX IF EXISTS sanctionseps58_historiqueetatpe_id_idx;
CREATE INDEX sanctionseps58_historiqueetatpe_id_idx ON sanctionseps58 (historiqueetatpe_id);

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_sanctionseps58_historiqueetatpe() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					historiqueetatspe.id AS historiqueetatpe_id, sanctionseps58.id AS sanctionep58_id
				FROM sanctionseps58
					INNER JOIN dossierseps ON ( dossierseps.id = sanctionseps58.dossierep_id )
					INNER JOIN personnes ON ( personnes.id = dossierseps.personne_id )
					INNER JOIN informationspe ON (((((informationspe.nir IS NOT NULL) AND (SUBSTRING( informationspe.nir FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH ' ' FROM personnes.nir ) FROM 1 FOR 13 )) AND (informationspe.dtnai = personnes.dtnai))) OR (((personnes.nom IS NOT NULL) AND (personnes.prenom IS NOT NULL) AND (personnes.dtnai IS NOT NULL) AND (TRIM( BOTH ' ' FROM personnes.nom ) <> '') AND (TRIM( BOTH ' ' FROM personnes.prenom ) <> '') AND (TRIM( BOTH ' ' FROM informationspe.nom ) = TRIM( BOTH ' ' FROM personnes.nom )) AND (TRIM( BOTH ' ' FROM informationspe.prenom ) = TRIM( BOTH ' ' FROM personnes.prenom )) AND (informationspe.dtnai = personnes.dtnai)))))
					INNER JOIN historiqueetatspe ON ( historiqueetatspe.informationpe_id = informationspe.id )
				WHERE
					historiqueetatspe.etat IN ( 'cessation', 'radiation' )
					AND sanctionseps58.origine = 'radiepe'
					AND sanctionseps58.created >= historiqueetatspe.date
					AND informationspe.id IN (
						SELECT
							derniereinformationspe.i__id
						FROM (
							SELECT
									i.id AS i__id, h.date AS h__date
								FROM informationspe AS i
									INNER JOIN historiqueetatspe AS h ON (h.informationpe_id = i.id)
								WHERE
									(
										(((i.nir IS NOT NULL) AND (personnes.nir IS NOT NULL) AND (TRIM( BOTH ' ' FROM i.nir ) <> '') AND (TRIM( BOTH ' ' FROM personnes.nir ) <> '') AND (SUBSTRING( i.nir FROM 1 FOR 13 ) = SUBSTRING( personnes.nir FROM 1 FOR 13 )) AND (i.dtnai = personnes.dtnai))) OR (((i.nom IS NOT NULL) AND (personnes.nom IS NOT NULL) AND (i.prenom IS NOT NULL) AND (personnes.prenom IS NOT NULL) AND (TRIM( BOTH ' ' FROM i.nom ) <> '') AND (TRIM( BOTH ' ' FROM i.prenom ) <> '') AND (TRIM( BOTH ' ' FROM personnes.nom ) <> '') AND (TRIM( BOTH ' ' FROM personnes.prenom ) <> '') AND (TRIM( BOTH ' ' FROM i.nom ) = personnes.nom) AND (TRIM( BOTH ' ' FROM i.prenom ) = personnes.prenom) AND (i.dtnai = personnes.dtnai)))
									)
									AND h.id IN (
										SELECT dernierhistoriqueetatspe.id AS dernierhistoriqueetatspe__id
											FROM historiqueetatspe AS dernierhistoriqueetatspe
											WHERE
												dernierhistoriqueetatspe.informationpe_id = i.id
												AND dernierhistoriqueetatspe.etat IN ( 'cessation', 'radiation' )
												AND sanctionseps58.created >= dernierhistoriqueetatspe.date
											ORDER BY dernierhistoriqueetatspe.date DESC
											LIMIT 1
									)
						) AS derniereinformationspe ORDER BY derniereinformationspe.h__date DESC LIMIT 1
					)
		LOOP
			-- Mise à jour dans la table sanctionseps58
			v_query := 'UPDATE sanctionseps58 SET historiqueetatpe_id = ' || v_row.historiqueetatpe_id || ' WHERE id = ' || v_row.sanctionep58_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_sanctionseps58_historiqueetatpe();
DROP FUNCTION public.update_sanctionseps58_historiqueetatpe();

-- Lorsqu'on est radié de Pôle Emploi, il faut que la colonne historiqueetatpe_id soit remplie.+
ALTER TABLE sanctionseps58 ADD CONSTRAINT sanctionseps58_historiqueetatpe_id_origine_chk CHECK ( ( origine = 'radiepe' AND historiqueetatpe_id IS NOT NULL ) OR ( origine <> 'radiepe' AND historiqueetatpe_id IS NULL ) );

-- -----------------------------------------------------------------------------------------------------------
-- Ajout de la clé étrangère vers les orientations en emploi
-- -----------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field( 'public', 'sanctionseps58', 'orientstruct_id', 'INTEGER' );
SELECT public.add_missing_constraint( 'public', 'sanctionseps58', 'sanctionseps58_orientstruct_id_fkey', 'orientsstructs', 'orientstruct_id'  );
DROP INDEX IF EXISTS sanctionseps58_orientstruct_id_idx;
CREATE INDEX sanctionseps58_orientstruct_id_idx ON sanctionseps58 (orientstruct_id);

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_sanctionseps58_orientstruct() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					orientsstructs.id AS orientstruct_id, sanctionseps58.id AS sanctionep58_id
				FROM sanctionseps58
					INNER JOIN dossierseps ON ( dossierseps.id = sanctionseps58.dossierep_id )
					INNER JOIN personnes ON ( personnes.id = dossierseps.personne_id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = personnes.id )
				WHERE
					orientsstructs.statut_orient = 'Orienté'
					AND sanctionseps58.origine = 'noninscritpe'
					AND sanctionseps58.created >= orientsstructs.date_valid
					AND orientsstructs.id IN (
						SELECT
							derniereorientsstructs.i__id
						FROM (
							SELECT
									i.id AS i__id, i.date_valid AS h__date
								FROM orientsstructs AS i
								WHERE
									i.personne_id = personnes.id
									AND i.id IN (
										SELECT dernierorientsstructs.id AS dernierorientsstructs__id
											FROM orientsstructs AS dernierorientsstructs
											WHERE
												dernierorientsstructs.personne_id = i.personne_id
												AND dernierorientsstructs.statut_orient = 'Orienté'
												AND sanctionseps58.created >= dernierorientsstructs.date_valid
											ORDER BY dernierorientsstructs.date_valid DESC
											LIMIT 1
									)
						) AS derniereorientsstructs ORDER BY derniereorientsstructs.h__date DESC LIMIT 1
					)
		LOOP
			-- Mise à jour dans la table sanctionseps58
			v_query := 'UPDATE sanctionseps58 SET orientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.sanctionep58_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_sanctionseps58_orientstruct();
DROP FUNCTION public.update_sanctionseps58_orientstruct();

-- Lorsqu'on est radié de Pôle Emploi, il faut que la colonne orientstruct_id soit remplie.+
ALTER TABLE sanctionseps58 ADD CONSTRAINT sanctionseps58_orientstruct_id_origine_chk CHECK ( ( origine = 'noninscritpe' AND orientstruct_id IS NOT NULL ) OR ( origine <> 'noninscritpe' AND orientstruct_id IS NULL ) );


-------------------------------------------------------------------------------------------------------------
-- 20120604 : Ajout d'une  table nonorientees66 qui stockera la date d'impression des courriers envoyés
--				aux allocataires ne possédant pas d'orientation
-------------------------------------------------------------------------------------------------------------
DROP TABLE IF EXISTS nonorientes66 CASCADE;
CREATE TABLE nonorientes66 (
  	id 						SERIAL NOT NULL PRIMARY KEY,
	personne_id           	INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	orientstruct_id       	INTEGER REFERENCES orientsstructs(id) ON DELETE CASCADE ON UPDATE CASCADE,
	dateimpression			DATE NOT NULL,
	user_id           		INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE nonorientes66 IS 'Table servant à l''enregistrement de la data d''impression du courrier envoyé aux allocataires ne possédant pas d''orientation(CG66)';
DROP INDEX IF EXISTS nonorientes66_personne_id_idx;
DROP INDEX IF EXISTS nonorientes66_orientstruct_id_idx;
DROP INDEX IF EXISTS nonorientes66_user_id_idx;
CREATE UNIQUE INDEX nonorientes66_personne_id_idx ON nonorientes66(personne_id);
CREATE INDEX nonorientes66_orientstruct_id_idx ON nonorientes66(orientstruct_id);
CREATE INDEX nonorientes66_user_id_idx ON nonorientes66(user_id);

-------------------------------------------------------------------------------------------------------------
-- 20120605 : Ajout d'une colonne supplémentaire modeleodt pour les décisions PDOs du CG93
------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ('public', 'decisionspdos', 'modeleodt', 'VARCHAR(250)');
UPDATE decisionspdos SET modeleodt = 'pdo_etudiant' WHERE modeleodt IS NULL AND libelle LIKE '%AJ 7a%';
UPDATE decisionspdos SET modeleodt = 'pdo_insertion' WHERE modeleodt IS NULL AND libelle LIKE '%DO 19%';
UPDATE decisionspdos SET modeleodt = NULL WHERE modeleodt IS NOT NULL AND TRIM( BOTH ' ' FROM modeleodt ) = '';

-------------------------------------------------------------------------------------------------------------
-- 20120605: ajout des clés étrangères pour nouvelles orientations et nouveaux contrats
-- d'insertion suite aux propositions pour les thématiques de COV du CG 58.
-------------------------------------------------------------------------------------------------------------

--
-- 1°) proposcontratsinsertioncovs58
--
SELECT add_missing_table_field ( 'public', 'proposcontratsinsertioncovs58', 'nvcontratinsertion_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'proposcontratsinsertioncovs58', 'proposcontratsinsertioncovs58_nvcontratinsertion_id_fkey', 'contratsinsertion', 'nvcontratinsertion_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_contratsinsertion_decisionsproposcontratsinsertioncovs58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					proposcontratsinsertioncovs58.id AS propo_id,
					contratsinsertion.id AS contratinsertion_id
				FROM dossierscovs58
					INNER JOIN proposcontratsinsertioncovs58 ON ( dossierscovs58.id = proposcontratsinsertioncovs58.dossiercov58_id )
					INNER JOIN passagescovs58 ON ( dossierscovs58.id = passagescovs58.dossiercov58_id )
					INNER JOIN decisionsproposcontratsinsertioncovs58 ON ( passagescovs58.id = decisionsproposcontratsinsertioncovs58.passagecov58_id )
					INNER JOIN contratsinsertion ON ( contratsinsertion.personne_id = dossierscovs58.personne_id )
				WHERE
					dossierscovs58.themecov58 = 'proposcontratsinsertioncovs58'
					AND passagescovs58.etatdossiercov = 'traite'
					AND decisionsproposcontratsinsertioncovs58.decisioncov IN ( 'valide', 'refuse' )
					AND passagescovs58.id IN (
						SELECT
								p.id
							FROM passagescovs58 AS p
								INNER JOIN covs58 ON ( p.cov58_id = covs58.id )
							WHERE dossierscovs58.id = p.dossiercov58_id
							ORDER BY covs58.datecommission DESC
							LIMIT 1
					)
					AND contratsinsertion.dd_ci = decisionsproposcontratsinsertioncovs58.dd_ci
					AND contratsinsertion.df_ci = decisionsproposcontratsinsertioncovs58.df_ci
					AND contratsinsertion.duree_engag = decisionsproposcontratsinsertioncovs58.duree_engag
					AND contratsinsertion.id NOT IN ( SELECT proposcontratsinsertioncovs58.nvcontratinsertion_id FROM proposcontratsinsertioncovs58  WHERE proposcontratsinsertioncovs58.nvcontratinsertion_id IS NOT NULL )
					AND contratsinsertion.rg_ci = proposcontratsinsertioncovs58.rg_ci
				ORDER BY decisionsproposcontratsinsertioncovs58.modified ASC
		LOOP
			-- Mise à jour dans la table proposcontratsinsertioncovs58
			v_query := 'UPDATE proposcontratsinsertioncovs58 SET nvcontratinsertion_id = ' || v_row.contratinsertion_id || ' WHERE id = ' || v_row.propo_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_contratsinsertion_decisionsproposcontratsinsertioncovs58();
DROP FUNCTION public.update_contratsinsertion_decisionsproposcontratsinsertioncovs58();

--
-- 2°) proposorientationscovs58
--
SELECT add_missing_table_field ( 'public', 'proposorientationscovs58', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'proposorientationscovs58', 'proposorientationscovs58_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsproposorientationscovs58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					proposorientationscovs58.id AS propo_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierscovs58
					INNER JOIN proposorientationscovs58 ON ( dossierscovs58.id = proposorientationscovs58.dossiercov58_id )
					INNER JOIN passagescovs58 ON ( dossierscovs58.id = passagescovs58.dossiercov58_id )
					INNER JOIN decisionsproposorientationscovs58 ON ( passagescovs58.id = decisionsproposorientationscovs58.passagecov58_id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierscovs58.personne_id )
					INNER JOIN covs58 ON ( covs58.id = passagescovs58.cov58_id )
				WHERE
					dossierscovs58.themecov58 = 'proposorientationscovs58'
					AND passagescovs58.etatdossiercov = 'traite'
					AND decisionsproposorientationscovs58.decisioncov IN ( 'valide', 'refuse' )
					AND passagescovs58.id IN (
						SELECT
								p.id
							FROM passagescovs58 AS p
								INNER JOIN covs58 ON ( p.cov58_id = covs58.id )
							WHERE dossierscovs58.id = p.dossiercov58_id
							ORDER BY covs58.datecommission DESC
							LIMIT 1
					)
					AND orientsstructs.user_id = proposorientationscovs58.user_id
					AND orientsstructs.date_propo = proposorientationscovs58.datedemande
					AND orientsstructs.typeorient_id = decisionsproposorientationscovs58.typeorient_id
					AND orientsstructs.structurereferente_id = decisionsproposorientationscovs58.structurereferente_id
					AND orientsstructs.date_valid = DATE_TRUNC('day', covs58.datecommission)
					AND orientsstructs.id NOT IN ( SELECT proposorientationscovs58.nvorientstruct_id FROM proposorientationscovs58  WHERE proposorientationscovs58.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionsproposorientationscovs58.modified ASC
		LOOP
			-- Mise à jour dans la table proposorientationscovs58
			v_query := 'UPDATE proposorientationscovs58 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.propo_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsproposorientationscovs58();
DROP FUNCTION public.update_orientsstructs_decisionsproposorientationscovs58();

--
-- 3°) proposnonorientationsproscovs58
--
SELECT add_missing_table_field ( 'public', 'proposnonorientationsproscovs58', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'proposnonorientationsproscovs58', 'proposnonorientationsproscovs58_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsproposnonorientationsproscovs58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					proposnonorientationsproscovs58.id AS propo_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierscovs58
					INNER JOIN proposnonorientationsproscovs58 ON ( dossierscovs58.id = proposnonorientationsproscovs58.dossiercov58_id )
					INNER JOIN passagescovs58 ON ( dossierscovs58.id = passagescovs58.dossiercov58_id )
					INNER JOIN decisionsproposnonorientationsproscovs58 ON ( passagescovs58.id = decisionsproposnonorientationsproscovs58.passagecov58_id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierscovs58.personne_id )
					INNER JOIN covs58 ON ( covs58.id = passagescovs58.cov58_id )
				WHERE
					dossierscovs58.themecov58 = 'proposnonorientationsproscovs58'
					AND passagescovs58.etatdossiercov = 'traite'
					AND decisionsproposnonorientationsproscovs58.decisioncov IN ( 'valide', 'refuse' )
					AND passagescovs58.id IN (
						SELECT
								p.id
							FROM passagescovs58 AS p
								INNER JOIN covs58 ON ( p.cov58_id = covs58.id )
							WHERE dossierscovs58.id = p.dossiercov58_id
							ORDER BY covs58.datecommission DESC
							LIMIT 1
					)
					AND orientsstructs.date_propo = proposnonorientationsproscovs58.datedemande
					AND orientsstructs.typeorient_id = decisionsproposnonorientationsproscovs58.typeorient_id
					AND orientsstructs.structurereferente_id = decisionsproposnonorientationsproscovs58.structurereferente_id
					AND orientsstructs.date_valid = DATE_TRUNC('day', covs58.datecommission)
					AND orientsstructs.id NOT IN ( SELECT proposnonorientationsproscovs58.nvorientstruct_id FROM proposnonorientationsproscovs58  WHERE proposnonorientationsproscovs58.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionsproposnonorientationsproscovs58.modified ASC
		LOOP
			-- Mise à jour dans la table proposnonorientationsproscovs58
			v_query := 'UPDATE proposnonorientationsproscovs58 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.propo_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsproposnonorientationsproscovs58();
DROP FUNCTION public.update_orientsstructs_decisionsproposnonorientationsproscovs58();


-------------------------------------------------------------------------------------------------------------
-- 20120611: ajout de la clé étrangère pour la table historiqueetatspe
--  + modificaiton de la contrainte not null sur la dateimpression
-------------------------------------------------------------------------------------------------------------

SELECT add_missing_table_field ('public', 'nonorientes66', 'historiqueetatpe_id', 'INTEGER');
SELECT add_missing_constraint ('public', 'nonorientes66', 'nonorientees66_historiqueetatpe_id_fkey', 'historiqueetatspe', 'historiqueetatpe_id');

DROP INDEX IF EXISTS nonorientes66_historiqueetatpe_id_idx;
CREATE UNIQUE INDEX nonorientes66_historiqueetatpe_id_idx ON nonorientes66(historiqueetatpe_id);

DROP TYPE IF EXISTS TYPE_ORIGINENONORIENTE66 CASCADE;
CREATE TYPE TYPE_ORIGINENONORIENTE66 AS ENUM ( 'isemploi','notisemploi');
SELECT add_missing_table_field ( 'public', 'nonorientes66', 'origine', 'TYPE_ORIGINENONORIENTE66' );
ALTER TABLE nonorientes66 ALTER COLUMN origine SET NOT NULL;

ALTER TABLE nonorientes66 ALTER COLUMN dateimpression DROP NOT NULL;

ALTER TABLE nonorientes66 ADD CONSTRAINT nonorientees66_dateimpression_origine_chk CHECK ( ( origine = 'isemploi' AND historiqueetatpe_id IS NOT NULL  AND dateimpression IS NULL) OR ( origine = 'notisemploi' AND historiqueetatpe_id IS NULL AND dateimpression IS NOT NULL ) );

-------------------------------------------------------------------------------------------------------------
-- 20120611: ajout d'indexes pour les nouvelles clés étrangères des tables de
-- thématiques de COV (CG 58).
-------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.nettoyage_proposnonorientationsproscovs58_nvorientstruct_id() RETURNS VOID AS
$$
	DECLARE
		v_row_doublon record;
		v_row_propo record;
		v_row_orientstruct record;
		v_row_orientstruct_fixme record;
		v_query text;
	BEGIN
		FOR v_row_doublon IN
			SELECT DISTINCT nvorientstruct_id
			FROM proposnonorientationsproscovs58
			GROUP BY nvorientstruct_id
			HAVING COUNT(nvorientstruct_id) > 1
		LOOP
			CREATE TEMPORARY TABLE omega(position INTEGER, propo_id INTEGER);
			INSERT INTO omega ( position, propo_id )
				SELECT row_number() OVER (ORDER BY id ASC), proposnonorientationsproscovs58.id
					FROM proposnonorientationsproscovs58 WHERE proposnonorientationsproscovs58.nvorientstruct_id = v_row_doublon.nvorientstruct_id;

			FOR v_row_propo IN
				SELECT DISTINCT dossierscovs58.personne_id
				FROM proposnonorientationsproscovs58
					INNER JOIN dossierscovs58 ON ( proposnonorientationsproscovs58.dossiercov58_id = dossierscovs58.id )
				WHERE proposnonorientationsproscovs58.nvorientstruct_id = v_row_doublon.nvorientstruct_id
				GROUP BY dossierscovs58.personne_id
			LOOP
				FOR v_row_orientstruct IN
					SELECT *
					FROM orientsstructs
					WHERE
						orientsstructs.personne_id = v_row_propo.personne_id
						AND orientsstructs.id = v_row_doublon.nvorientstruct_id
				LOOP
					FOR v_row_orientstruct_fixme IN
						SELECT row_number() OVER (ORDER BY orientsstructs.id ASC) AS position, orientsstructs.id
						FROM orientsstructs
						WHERE
							orientsstructs.personne_id = v_row_propo.personne_id
							AND orientsstructs.statut_orient = v_row_orientstruct.statut_orient
							AND orientsstructs.date_valid = v_row_orientstruct.date_valid
							AND orientsstructs.typeorient_id = v_row_orientstruct.typeorient_id
							AND orientsstructs.structurereferente_id = v_row_orientstruct.structurereferente_id
						ORDER BY orientsstructs.id ASC
					LOOP
						v_query := 'UPDATE proposnonorientationsproscovs58
							SET nvorientstruct_id = ' || v_row_orientstruct_fixme.id
							|| ' WHERE id = ( SELECT omega.propo_id FROM omega WHERE position = ' || v_row_orientstruct_fixme.position || ' );';
						EXECUTE v_query;
					END LOOP;
				END LOOP;
			END LOOP;
			DROP TABLE omega;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_proposnonorientationsproscovs58_nvorientstruct_id();
DROP FUNCTION public.nettoyage_proposnonorientationsproscovs58_nvorientstruct_id();

CREATE OR REPLACE FUNCTION public.nettoyage_proposorientationscovs58_nvorientstruct_id() RETURNS VOID AS
$$
	DECLARE
		v_row_doublon record;
		v_row_propo record;
		v_row_orientstruct record;
		v_row_orientstruct_fixme record;
		v_query text;
	BEGIN
		FOR v_row_doublon IN
			SELECT DISTINCT nvorientstruct_id
			FROM proposorientationscovs58
			GROUP BY nvorientstruct_id
			HAVING COUNT(nvorientstruct_id) > 1
		LOOP
			CREATE TEMPORARY TABLE omega(position INTEGER, propo_id INTEGER);
			INSERT INTO omega ( position, propo_id )
				SELECT row_number() OVER (ORDER BY id ASC), proposorientationscovs58.id
					FROM proposorientationscovs58 WHERE proposorientationscovs58.nvorientstruct_id = v_row_doublon.nvorientstruct_id;

			FOR v_row_propo IN
				SELECT DISTINCT dossierscovs58.personne_id
				FROM proposorientationscovs58
					INNER JOIN dossierscovs58 ON ( proposorientationscovs58.dossiercov58_id = dossierscovs58.id )
				WHERE proposorientationscovs58.nvorientstruct_id = v_row_doublon.nvorientstruct_id
				GROUP BY dossierscovs58.personne_id
			LOOP
				FOR v_row_orientstruct IN
					SELECT *
					FROM orientsstructs
					WHERE
						orientsstructs.personne_id = v_row_propo.personne_id
						AND orientsstructs.id = v_row_doublon.nvorientstruct_id
				LOOP
					FOR v_row_orientstruct_fixme IN
						SELECT row_number() OVER (ORDER BY orientsstructs.id ASC) AS position, orientsstructs.id
						FROM orientsstructs
						WHERE
							orientsstructs.personne_id = v_row_propo.personne_id
							AND orientsstructs.statut_orient = v_row_orientstruct.statut_orient
							AND orientsstructs.date_valid = v_row_orientstruct.date_valid
							AND orientsstructs.typeorient_id = v_row_orientstruct.typeorient_id
							AND orientsstructs.structurereferente_id = v_row_orientstruct.structurereferente_id
						ORDER BY orientsstructs.id ASC
					LOOP
						v_query := 'UPDATE proposorientationscovs58
							SET nvorientstruct_id = ' || v_row_orientstruct_fixme.id
							|| ' WHERE id = ( SELECT omega.propo_id FROM omega WHERE position = ' || v_row_orientstruct_fixme.position || ' );';
						EXECUTE v_query;
					END LOOP;
				END LOOP;
			END LOOP;
			DROP TABLE omega;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_proposorientationscovs58_nvorientstruct_id();
DROP FUNCTION public.nettoyage_proposorientationscovs58_nvorientstruct_id();

CREATE OR REPLACE FUNCTION public.nettoyage_proposcontratsinsertioncovs58_nvcontratinsertion_id() RETURNS VOID AS
$$
	DECLARE
		v_row_doublon record;
		v_row_propo record;
		v_row_contratinsertion record;
		v_row_contratinsertion_fixme record;
		v_query text;
	BEGIN
		FOR v_row_doublon IN
			SELECT DISTINCT nvcontratinsertion_id
			FROM proposcontratsinsertioncovs58
			GROUP BY nvcontratinsertion_id
			HAVING COUNT(nvcontratinsertion_id) > 1
		LOOP
			CREATE TEMPORARY TABLE omega(position INTEGER, propo_id INTEGER);
			INSERT INTO omega ( position, propo_id )
				SELECT row_number() OVER (ORDER BY id ASC), proposcontratsinsertioncovs58.id
					FROM proposcontratsinsertioncovs58 WHERE proposcontratsinsertioncovs58.nvcontratinsertion_id = v_row_doublon.nvcontratinsertion_id;

			FOR v_row_propo IN
				SELECT DISTINCT dossierscovs58.personne_id
				FROM proposcontratsinsertioncovs58
					INNER JOIN dossierscovs58 ON ( proposcontratsinsertioncovs58.dossiercov58_id = dossierscovs58.id )
				WHERE proposcontratsinsertioncovs58.nvcontratinsertion_id = v_row_doublon.nvcontratinsertion_id
				GROUP BY dossierscovs58.personne_id
			LOOP
				FOR v_row_contratinsertion IN
					SELECT *
					FROM contratsinsertion
					WHERE
						contratsinsertion.personne_id = v_row_propo.personne_id
						AND contratsinsertion.id = v_row_doublon.nvcontratinsertion_id
				LOOP
					FOR v_row_contratinsertion_fixme IN
						SELECT row_number() OVER (ORDER BY contratsinsertion.id ASC) AS position, contratsinsertion.id
						FROM contratsinsertion
						WHERE
							contratsinsertion.personne_id = v_row_propo.personne_id
							AND contratsinsertion.decision_ci = v_row_contratinsertion.decision_ci
							AND contratsinsertion.datevalidation_ci = v_row_contratinsertion.datevalidation_ci
							AND contratsinsertion.structurereferente_id = v_row_contratinsertion.structurereferente_id
						ORDER BY contratsinsertion.id ASC
					LOOP
						v_query := 'UPDATE proposcontratsinsertioncovs58
							SET nvcontratinsertion_id = ' || v_row_contratinsertion_fixme.id
							|| ' WHERE id = ( SELECT omega.propo_id FROM omega WHERE position = ' || v_row_contratinsertion_fixme.position || ' );';
						EXECUTE v_query;
					END LOOP;
				END LOOP;
			END LOOP;
			DROP TABLE omega;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_proposcontratsinsertioncovs58_nvcontratinsertion_id();
DROP FUNCTION public.nettoyage_proposcontratsinsertioncovs58_nvcontratinsertion_id();

CREATE UNIQUE INDEX proposcontratsinsertioncovs58_nvcontratinsertion_id_idx ON proposcontratsinsertioncovs58 (nvcontratinsertion_id);
CREATE UNIQUE INDEX proposorientationscovs58_nvorientstruct_id_idx ON proposorientationscovs58 (nvorientstruct_id);
CREATE UNIQUE INDEX proposnonorientationsproscovs58_nvorientstruct_id_idx ON proposnonorientationsproscovs58 (nvorientstruct_id);

-- -----------------------------------------------------------------------------------------------------------
-- 20120611: correction: une décision de refuse pour la thématique regressionsorientationseps58
-- entraîne tout de même la création d'une nouvelle orientation
-- -----------------------------------------------------------------------------------------------------------

DROP INDEX orientsstructs_personne_id_rgorient_idx;
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_statut_orient_oriente_rgorient_not_null_chk;
ALTER TABLE orientsstructs DROP CONSTRAINT orientsstructs_origine_check;

UPDATE orientsstructs
	SET statut_orient = 'Orienté'
	WHERE
		typeorient_id IS NOT NULL
		AND structurereferente_id IS NOT NULL
		AND date_valid IS NOT NULL;

INSERT INTO orientsstructs ( personne_id, typeorient_id, structurereferente_id, referent_id, date_propo, date_valid, statut_orient, rgorient, etatorient, user_id, origine )
	SELECT
			dossierseps.personne_id AS personne_id,
			decisionsregressionsorientationseps58.typeorient_id AS typeorient_id,
			decisionsregressionsorientationseps58.structurereferente_id AS structurereferente_id,
			decisionsregressionsorientationseps58.referent_id AS referent_id,
			DATE_TRUNC( 'day', regressionsorientationseps58.datedemande ) AS date_propo,
			DATE_TRUNC( 'day', commissionseps.dateseance ) AS date_valid,
			'Orienté' AS statut_orient,
			NULL AS rgorient,
			'decision' AS etatorient,
			regressionsorientationseps58.user_id AS user_id,
			'reorientation' AS origine
		FROM decisionsregressionsorientationseps58
			INNER JOIN passagescommissionseps ON ( decisionsregressionsorientationseps58.passagecommissionep_id  =passagescommissionseps.id )
			INNER JOIN dossierseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
			INNER JOIN regressionsorientationseps58 ON ( regressionsorientationseps58.dossierep_id = dossierseps.id )
			INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
		WHERE
			commissionseps.etatcommissionep = 'traite'
			AND passagescommissionseps.etatdossierep = 'traite'
			AND decisionsregressionsorientationseps58.decision = 'refuse';

UPDATE orientsstructs SET rgorient = NULL;
UPDATE orientsstructs
	SET rgorient = (
		SELECT ( COUNT(orientsstructspcd.id) + 1 )
			FROM orientsstructs AS orientsstructspcd
			WHERE orientsstructspcd.personne_id = orientsstructs.personne_id
				AND orientsstructspcd.id <> orientsstructs.id
				AND orientsstructs.date_valid IS NOT NULL
				AND orientsstructspcd.date_valid IS NOT NULL
				AND (
					orientsstructspcd.date_valid < orientsstructs.date_valid
					OR ( orientsstructspcd.date_valid = orientsstructs.date_valid AND orientsstructspcd.id < orientsstructs.id )
				)
				AND orientsstructs.statut_orient = 'Orienté'
				AND orientsstructspcd.statut_orient = 'Orienté'
	)
	WHERE
		orientsstructs.date_valid IS NOT NULL
		AND orientsstructs.statut_orient = 'Orienté';

UPDATE orientsstructs
	SET origine = 'manuelle'
	WHERE rgorient = 1 AND origine = 'reorientation';

UPDATE orientsstructs
	SET origine = 'reorientation'
	WHERE rgorient > 1 AND origine <> 'reorientation';

CREATE UNIQUE INDEX orientsstructs_personne_id_rgorient_idx ON orientsstructs( personne_id, rgorient ) WHERE rgorient IS NOT NULL;

ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_statut_orient_oriente_rgorient_not_null_chk CHECK (
	( statut_orient <> 'Orienté' AND rgorient IS NULL )
	OR ( statut_orient = 'Orienté' AND rgorient IS NOT NULL )
);

ALTER TABLE orientsstructs ADD CONSTRAINT orientsstructs_origine_check CHECK(
	( origine IS NULL AND date_valid IS NULL )
	OR (
		( origine IS NOT NULL AND date_valid IS NOT NULL )
		AND (
			( rgorient = 1 AND origine IN ( 'manuelle', 'cohorte' ) )
			OR ( rgorient > 1 AND origine = 'reorientation' )
		)
	)
);

-------------------------------------------------------------------------------------------------------------
-- 20120611: ajout des clés étrangères pour nouvelles orientations et nouveaux contrats
-- d'insertion suite aux passages en EP.
-------------------------------------------------------------------------------------------------------------

--
--  1°) nonorientationsproseps58
--

SELECT add_missing_table_field ( 'public', 'nonorientationsproseps58', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'nonorientationsproseps58', 'nonorientationsproseps58_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsnonorientationsproseps58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					nonorientationsproseps58.id AS thematique_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierseps
					INNER JOIN nonorientationsproseps58 ON ( nonorientationsproseps58.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionsnonorientationsproseps58 ON ( decisionsnonorientationsproseps58.passagecommissionep_id = passagescommissionseps.id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierseps.personne_id )
				WHERE
					dossierseps.themeep = 'nonorientationsproseps58'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND decisionsnonorientationsproseps58.decision IN ( 'reorientation', 'maintienref' )
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionsnonorientationsproseps58.id IN (
						SELECT
								d.id
							FROM decisionsnonorientationsproseps58 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					AND orientsstructs.id IN (
						SELECT
								o.id
							FROM orientsstructs AS o
							WHERE
								o.typeorient_id = decisionsnonorientationsproseps58.typeorient_id
								AND o.structurereferente_id = decisionsnonorientationsproseps58.structurereferente_id
								AND o.date_propo = DATE_TRUNC('day', nonorientationsproseps58.created)
								AND o.date_valid = DATE_TRUNC('day', commissionseps.dateseance)
								AND o.statut_orient = 'Orienté'
								AND o.etatorient = 'decision'
							ORDER BY o.rgorient DESC
							LIMIT 1
					)
					-- Jointure sur les orientations
					AND orientsstructs.typeorient_id = decisionsnonorientationsproseps58.typeorient_id
					AND orientsstructs.structurereferente_id = decisionsnonorientationsproseps58.structurereferente_id
					AND orientsstructs.date_propo = DATE_TRUNC('day', nonorientationsproseps58.created)
					AND orientsstructs.date_valid = DATE_TRUNC('day', commissionseps.dateseance)
					AND orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.etatorient = 'decision'
					AND orientsstructs.id NOT IN ( SELECT nonorientationsproseps58.nvorientstruct_id FROM nonorientationsproseps58  WHERE nonorientationsproseps58.nvorientstruct_id IS NOT NULL )
				ORDER BY nonorientationsproseps58.id ASC, decisionsnonorientationsproseps58.modified ASC, decisionsnonorientationsproseps58.id ASC
		LOOP
			-- Mise à jour dans la table nonorientationsproseps58
			v_query := 'UPDATE nonorientationsproseps58 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsnonorientationsproseps58();
DROP FUNCTION public.update_orientsstructs_decisionsnonorientationsproseps58();

-- Dédoublonnage
CREATE OR REPLACE FUNCTION public.dedoublonnage_orientsstructs_nonorientationsproseps58() RETURNS VOID AS
$$
	DECLARE
		v_row_doublon record;
		v_row_propo record;
		v_row_orientstruct record;
		v_row_orientstruct_fixme record;
		v_query text;
	BEGIN
		FOR v_row_doublon IN
			SELECT DISTINCT nvorientstruct_id
			FROM nonorientationsproseps58
			GROUP BY nvorientstruct_id
			HAVING COUNT(nvorientstruct_id) > 1
		LOOP
			RAISE NOTICE  '%', v_row_doublon;

			CREATE TEMPORARY TABLE omega(position INTEGER, propo_id INTEGER);
			INSERT INTO omega ( position, propo_id )
				SELECT row_number() OVER (ORDER BY id ASC), nonorientationsproseps58.id
					FROM nonorientationsproseps58 WHERE nonorientationsproseps58.nvorientstruct_id = v_row_doublon.nvorientstruct_id;

			FOR v_row_propo IN
				SELECT DISTINCT dossierseps.personne_id
				FROM nonorientationsproseps58
					INNER JOIN dossierseps ON ( nonorientationsproseps58.dossierep_id = dossierseps.id )
				WHERE nonorientationsproseps58.nvorientstruct_id = v_row_doublon.nvorientstruct_id
				GROUP BY dossierseps.personne_id
			LOOP
				FOR v_row_orientstruct IN
					SELECT *
					FROM orientsstructs
					WHERE
						orientsstructs.personne_id = v_row_propo.personne_id
						AND orientsstructs.id = v_row_doublon.nvorientstruct_id
				LOOP
					FOR v_row_orientstruct_fixme IN
						SELECT row_number() OVER (ORDER BY orientsstructs.id ASC) AS position, orientsstructs.id
						FROM orientsstructs
						WHERE
							orientsstructs.personne_id = v_row_propo.personne_id

							AND orientsstructs.typeorient_id = v_row_orientstruct.typeorient_id
							AND orientsstructs.structurereferente_id = v_row_orientstruct.structurereferente_id
							AND orientsstructs.date_propo = v_row_orientstruct.date_propo
							AND orientsstructs.date_valid = v_row_orientstruct.date_valid
							AND orientsstructs.statut_orient = v_row_orientstruct.statut_orient
							AND orientsstructs.etatorient = v_row_orientstruct.etatorient
						ORDER BY orientsstructs.id ASC
					LOOP
						v_query := 'UPDATE nonorientationsproseps58
							SET nvorientstruct_id = ' || v_row_orientstruct_fixme.id
							|| ' WHERE id = ( SELECT omega.propo_id FROM omega WHERE position = ' || v_row_orientstruct_fixme.position || ' );';
						EXECUTE v_query;
					END LOOP;
				END LOOP;
			END LOOP;
			DROP TABLE omega;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.dedoublonnage_orientsstructs_nonorientationsproseps58();
DROP FUNCTION public.dedoublonnage_orientsstructs_nonorientationsproseps58();

CREATE UNIQUE INDEX nonorientationsproseps58_nvorientstruct_id_idx ON nonorientationsproseps58 (nvorientstruct_id);

--
--  2°) regressionsorientationseps58
--

SELECT add_missing_table_field ( 'public', 'regressionsorientationseps58', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'regressionsorientationseps58', 'regressionsorientationseps58_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsregressionsorientationseps58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					regressionsorientationseps58.id AS thematique_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierseps
					INNER JOIN regressionsorientationseps58 ON ( regressionsorientationseps58.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionsregressionsorientationseps58 ON ( decisionsregressionsorientationseps58.passagecommissionep_id = passagescommissionseps.id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierseps.personne_id )
				WHERE
					dossierseps.themeep = 'regressionsorientationseps58'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND decisionsregressionsorientationseps58.decision IN ( 'accepte', 'refuse' )
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionsregressionsorientationseps58.id IN (
						SELECT
								d.id
							FROM decisionsregressionsorientationseps58 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					-- Jointure sur les orientations
					AND orientsstructs.typeorient_id = decisionsregressionsorientationseps58.typeorient_id
					AND orientsstructs.structurereferente_id = decisionsregressionsorientationseps58.structurereferente_id
					AND orientsstructs.date_propo = DATE_TRUNC('day', regressionsorientationseps58.datedemande)
					AND orientsstructs.date_valid = DATE_TRUNC('day', commissionseps.dateseance)
					AND orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.etatorient = 'decision'
					AND orientsstructs.id NOT IN ( SELECT regressionsorientationseps58.nvorientstruct_id FROM regressionsorientationseps58  WHERE regressionsorientationseps58.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionsregressionsorientationseps58.modified ASC
		LOOP
			-- Mise à jour dans la table regressionsorientationseps58
			v_query := 'UPDATE regressionsorientationseps58 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsregressionsorientationseps58();
DROP FUNCTION public.update_orientsstructs_decisionsregressionsorientationseps58();

CREATE UNIQUE INDEX regressionsorientationseps58_nvorientstruct_id_idx ON regressionsorientationseps58 (nvorientstruct_id);

--
-- 3.a°) saisinesbilansparcourseps66, nvorientstruct_id
--

SELECT add_missing_table_field ( 'public', 'saisinesbilansparcourseps66', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'saisinesbilansparcourseps66', 'saisinesbilansparcourseps66_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionssaisinesbilansparcourseps66() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					saisinesbilansparcourseps66.id AS thematique_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierseps
					INNER JOIN saisinesbilansparcourseps66 ON ( saisinesbilansparcourseps66.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionssaisinesbilansparcourseps66 ON ( decisionssaisinesbilansparcourseps66.passagecommissionep_id = passagescommissionseps.id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierseps.personne_id )
				WHERE
					dossierseps.themeep = 'saisinesbilansparcourseps66'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND (
						decisionssaisinesbilansparcourseps66.decision = 'reorientation'
						OR (
							decisionssaisinesbilansparcourseps66.decision = 'maintien'
							AND NOT (
								decisionssaisinesbilansparcourseps66.changementrefparcours = 'N'
								AND decisionssaisinesbilansparcourseps66.typeorientprincipale_id IN (
									SELECT id FROM typesorients WHERE lib_type_orient LIKE 'Emploi%'
								)
							)
						)
					)
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionssaisinesbilansparcourseps66.id IN (
						SELECT
								d.id
							FROM decisionssaisinesbilansparcourseps66 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					-- Jointure sur les orientations
					AND orientsstructs.typeorient_id = decisionssaisinesbilansparcourseps66.typeorient_id
					AND orientsstructs.structurereferente_id = decisionssaisinesbilansparcourseps66.structurereferente_id
					AND orientsstructs.date_propo = DATE_TRUNC('day', decisionssaisinesbilansparcourseps66.modified )
					AND orientsstructs.date_valid = DATE_TRUNC('day', decisionssaisinesbilansparcourseps66.modified )
					AND orientsstructs.user_id = decisionssaisinesbilansparcourseps66.user_id
					AND orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.id NOT IN ( SELECT saisinesbilansparcourseps66.nvorientstruct_id FROM saisinesbilansparcourseps66 WHERE saisinesbilansparcourseps66.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionssaisinesbilansparcourseps66.modified ASC
		LOOP
			-- Mise à jour dans la table saisinesbilansparcourseps66
			v_query := 'UPDATE saisinesbilansparcourseps66 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionssaisinesbilansparcourseps66();
DROP FUNCTION public.update_orientsstructs_decisionssaisinesbilansparcourseps66();

CREATE UNIQUE INDEX saisinesbilansparcourseps66_nvorientstruct_id_idx ON saisinesbilansparcourseps66 (nvorientstruct_id);

--
-- 3.b°) saisinesbilansparcourseps66.nvcontratinsertion_id
--

SELECT add_missing_table_field ( 'public', 'saisinesbilansparcourseps66', 'nvcontratinsertion_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'saisinesbilansparcourseps66', 'saisinesbilansparcourseps66_nvcontratinsertion_id_fkey', 'contratsinsertion', 'nvcontratinsertion_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_contratsinsertion_decisionssaisinesbilansparcourseps66() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					saisinesbilansparcourseps66.id AS thematique_id,
					contratsinsertion.id AS nvcontratinsertion_id
				FROM dossierseps
					INNER JOIN saisinesbilansparcourseps66 ON ( saisinesbilansparcourseps66.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionssaisinesbilansparcourseps66 ON ( decisionssaisinesbilansparcourseps66.passagecommissionep_id = passagescommissionseps.id )
					INNER JOIN contratsinsertion ON ( contratsinsertion.personne_id = dossierseps.personne_id )
					INNER JOIN bilansparcours66 ON ( bilansparcours66.id = saisinesbilansparcourseps66.bilanparcours66_id )
				WHERE
					dossierseps.themeep = 'saisinesbilansparcourseps66'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND decisionssaisinesbilansparcourseps66.decision = 'maintien'
					AND decisionssaisinesbilansparcourseps66.changementrefparcours = 'N'
					AND decisionssaisinesbilansparcourseps66.typeorientprincipale_id IN (
						SELECT id FROM typesorients WHERE lib_type_orient LIKE 'Emploi%'
					)
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionssaisinesbilansparcourseps66.id IN (
						SELECT
								d.id
							FROM decisionssaisinesbilansparcourseps66 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					-- Jointure sur les CER
					AND contratsinsertion.dd_ci = bilansparcours66.ddreconductoncontrat
					AND contratsinsertion.df_ci = bilansparcours66.dfreconductoncontrat
					AND contratsinsertion.duree_engag = bilansparcours66.duree_engag
					AND contratsinsertion.typocontrat_id IN ( SELECT id FROM typoscontrats WHERE lib_typo = 'Renouvellement' )
					AND contratsinsertion.date_saisi_ci = DATE_TRUNC('day', decisionssaisinesbilansparcourseps66.modified )
					AND contratsinsertion.id NOT IN ( SELECT saisinesbilansparcourseps66.nvcontratinsertion_id FROM saisinesbilansparcourseps66 WHERE saisinesbilansparcourseps66.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionssaisinesbilansparcourseps66.modified ASC
		LOOP
			-- Mise à jour dans la table saisinesbilansparcourseps66
			v_query := 'UPDATE saisinesbilansparcourseps66 SET nvcontratinsertion_id = ' || v_row.nvcontratinsertion_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_contratsinsertion_decisionssaisinesbilansparcourseps66();
DROP FUNCTION public.update_contratsinsertion_decisionssaisinesbilansparcourseps66();

CREATE UNIQUE INDEX saisinesbilansparcourseps66_nvcontratinsertion_id_idx ON saisinesbilansparcourseps66 (nvcontratinsertion_id);

--
-- 4°) FIXME: defautsinsertionseps66 nvbilanparcours66_id, nvdossierpcg66_id
--

--
-- 5°) nonorientationsproseps93.nvorientstruct_id
--

SELECT add_missing_table_field ( 'public', 'nonorientationsproseps93', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'nonorientationsproseps93', 'nonorientationsproseps93_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsnonorientationsproseps93() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					nonorientationsproseps93.id AS thematique_id,
					orientsstructs.id AS orientstruct_id
				FROM dossierseps
					INNER JOIN nonorientationsproseps93 ON ( nonorientationsproseps93.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionsnonorientationsproseps93 ON ( decisionsnonorientationsproseps93.passagecommissionep_id = passagescommissionseps.id )
					INNER JOIN orientsstructs ON ( orientsstructs.personne_id = dossierseps.personne_id )
				WHERE
					dossierseps.themeep = 'nonorientationsproseps93'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND decisionsnonorientationsproseps93.decision = 'reorientation'
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionsnonorientationsproseps93.id IN (
						SELECT
								d.id
							FROM decisionsnonorientationsproseps93 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					-- Jointure sur les orientations
					AND orientsstructs.typeorient_id = decisionsnonorientationsproseps93.typeorient_id
					AND orientsstructs.structurereferente_id = decisionsnonorientationsproseps93.structurereferente_id
					AND orientsstructs.date_propo = DATE_TRUNC('day', nonorientationsproseps93.created )
					AND orientsstructs.date_valid = DATE_TRUNC('day', commissionseps.dateseance )
					AND orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.etatorient = 'decision'
					AND (
						orientsstructs.user_id IS NULL
						OR orientsstructs.user_id = nonorientationsproseps93.user_id
					)
					AND orientsstructs.id NOT IN ( SELECT nonorientationsproseps93.nvorientstruct_id FROM nonorientationsproseps93 WHERE nonorientationsproseps93.nvorientstruct_id IS NOT NULL )
				ORDER BY decisionsnonorientationsproseps93.modified ASC
		LOOP
			-- Mise à jour dans la table nonorientationsproseps93
			v_query := 'UPDATE nonorientationsproseps93 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsnonorientationsproseps93();
DROP FUNCTION public.update_orientsstructs_decisionsnonorientationsproseps93();

CREATE UNIQUE INDEX nonorientationsproseps93_nvorientstruct_id_idx ON nonorientationsproseps93 (nvorientstruct_id);

--
-- 6°) reorientationseps93.nvorientstruct_id
--

SELECT add_missing_table_field ( 'public', 'reorientationseps93', 'nvorientstruct_id', 'INTEGER' );
SELECT add_missing_constraint ( 'public', 'reorientationseps93', 'reorientationseps93_nvorientstruct_id_fkey', 'orientsstructs', 'nvorientstruct_id', false );

-- On rapatrie les données implicites
CREATE OR REPLACE FUNCTION public.update_orientsstructs_decisionsreorientationseps93() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					reorientationseps93.id AS thematique_id,
					decisionsreorientationseps93.orientstruct_id AS orientstruct_id
				FROM dossierseps
					INNER JOIN reorientationseps93 ON ( reorientationseps93.dossierep_id = dossierseps.id )
					INNER JOIN passagescommissionseps ON ( passagescommissionseps.dossierep_id = dossierseps.id )
					INNER JOIN commissionseps ON ( passagescommissionseps.commissionep_id = commissionseps.id )
					INNER JOIN decisionsreorientationseps93 ON ( decisionsreorientationseps93.passagecommissionep_id = passagescommissionseps.id )
				WHERE
					dossierseps.themeep = 'reorientationseps93'
					AND passagescommissionseps.etatdossierep = 'traite'
					AND commissionseps.etatcommissionep = 'traite'
					AND decisionsreorientationseps93.decision = 'accepte'
					AND passagescommissionseps.id IN (
						SELECT
								p.id
							FROM passagescommissionseps AS p
								INNER JOIN commissionseps AS c ON ( p.commissionep_id = c.id )
							WHERE dossierseps.id = p.dossierep_id
							ORDER BY c.dateseance DESC
							LIMIT 1
					)
					AND decisionsreorientationseps93.id IN (
						SELECT
								d.id
							FROM decisionsreorientationseps93 AS d
							WHERE passagescommissionseps.id = d.passagecommissionep_id
							ORDER BY ( CASE WHEN d.etape = 'ep' THEN 1 WHEN etape = 'cg' THEN 2 ELSE 0 END ) DESC -- cg, ep
							LIMIT 1
					)
					AND decisionsreorientationseps93.orientstruct_id IS NOT NULL
		LOOP
			-- Mise à jour dans la table reorientationseps93
			v_query := 'UPDATE reorientationseps93 SET nvorientstruct_id = ' || v_row.orientstruct_id || ' WHERE id = ' || v_row.thematique_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.update_orientsstructs_decisionsreorientationseps93();
DROP FUNCTION public.update_orientsstructs_decisionsreorientationseps93();

CREATE UNIQUE INDEX reorientationseps93_nvorientstruct_id_idx ON reorientationseps93 (nvorientstruct_id);

-- FIXME: changement des chemins pour l'impression
-- Suppression de la colonne decisionsreorientationseps93.orientstruct_id (à présent dans reorientationseps93.nvorientstruct_id)
-- grep -nri "\(Decisionreorientationep93\|decisionsreorientationseps93\).*orientstruct" app | grep -v "\.svn"
SELECT alter_table_drop_column_if_exists('public', 'decisionsreorientationseps93', 'orientstruct_id');

-- TODO: dans les méthodes getDecisionPdf de modèles de thématiques, on passe encore par les décisions alors qu'on pourrait passer par nvorientstruct_id (/commissionseps/impressionDecision)

-------------------------------------------------------------------------------------------------------------
-- 20120615 : Ajout d'une date de notification pour les courriers des allocataires orientés au CG66
-------------------------------------------------------------------------------------------------------------
SELECT add_missing_table_field ( 'public', 'nonorientes66', 'datenotification', 'DATE' );
SELECT add_missing_table_field ( 'public', 'nonorientes66', 'reponseallocataire', 'TYPE_NO' );



SELECT add_missing_table_field ('public', 'nonorientes66', 'haspiecejointe', 'type_booleannumber');
ALTER TABLE nonorientes66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE nonorientes66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE nonorientes66 ALTER COLUMN haspiecejointe SET NOT NULL;

-- ***********************************************************************************************************
-- 20120619 -- Ajout d'un champ  permettant de savoir si l'APRE est transférée vers la cellule
-- ***********************************************************************************************************
SELECT add_missing_table_field ('public', 'apres', 'istransfere', 'TYPE_BOOLEANNUMBER');
ALTER TABLE apres ALTER COLUMN istransfere SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE apres SET istransfere = '0'::TYPE_BOOLEANNUMBER WHERE istransfere IS NULL;
ALTER TABLE apres ALTER COLUMN istransfere SET NOT NULL;

-- ***********************************************************************************************************
-- 20120620 -- Ajout de champs supplémentaires et nouveaux pour les décisions de sanction ep 58
			-- dans le cas de modification de ces sanctions
-- ***********************************************************************************************************
DROP TYPE IF EXISTS TYPE_ARRETSANCTIONEP58;
CREATE TYPE TYPE_ARRETSANCTIONEP58 AS ENUM ( 'finsanction1', 'finsanction2', 'annulation1', 'annulation2' );
SELECT add_missing_table_field ('public', 'decisionssanctionseps58', 'arretsanction', 'TYPE_ARRETSANCTIONEP58');
SELECT add_missing_table_field ('public', 'decisionssanctionseps58', 'datearretsanction', 'DATE');
SELECT add_missing_table_field ('public', 'decisionssanctionseps58', 'commentairearretsanction', 'TEXT');

SELECT add_missing_table_field ('public', 'decisionssanctionsrendezvouseps58', 'arretsanction', 'TYPE_ARRETSANCTIONEP58');
SELECT add_missing_table_field ('public', 'decisionssanctionsrendezvouseps58', 'datearretsanction', 'DATE');
SELECT add_missing_table_field ('public', 'decisionssanctionsrendezvouseps58', 'commentairearretsanction', 'TEXT');

ALTER TABLE decisionssanctionseps58 ADD CONSTRAINT decisionssanctionseps58_arretsanction_datearretsanction_chk CHECK ( ( arretsanction IN ( 'finsanction1', 'finsanction2' ) AND datearretsanction IS NOT NULL ) OR ( arretsanction NOT IN ( 'finsanction1', 'finsanction2' ) AND datearretsanction IS NULL ) );
ALTER TABLE decisionssanctionsrendezvouseps58 ADD CONSTRAINT decisionssanctionsrendezvouseps58_arretsanction_datearretsanction_chk CHECK ( ( arretsanction IN ( 'finsanction1', 'finsanction2' ) AND datearretsanction IS NOT NULL ) OR ( arretsanction NOT IN ( 'finsanction1', 'finsanction2' ) AND datearretsanction IS NULL ) );


-- ***********************************************************************************************************
-- 20120626 -- Ajout d'un champ supplémenataire pour les pièces Autres des modèles de traitements PCGs courriers
-- ***********************************************************************************************************
SELECT add_missing_table_field ('public', 'piecesmodelestypescourrierspcgs66', 'isautrepiece', 'TYPE_BOOLEANNUMBER');
ALTER TABLE piecesmodelestypescourrierspcgs66 ALTER COLUMN isautrepiece SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE piecesmodelestypescourrierspcgs66 SET isautrepiece = '0'::TYPE_BOOLEANNUMBER WHERE isautrepiece IS NULL;

DROP INDEX IF EXISTS piecesmodelestypescourrierspcgs66_isautrepiece_idx;
CREATE INDEX piecesmodelestypescourrierspcgs66_isautrepiece_idx ON piecesmodelestypescourrierspcgs66 (isautrepiece);

DROP INDEX IF EXISTS piecesmodelestypescourrierspcgs66_isautrepiece_modeletypecourrierpcg66_id_idx;
CREATE UNIQUE INDEX piecesmodelestypescourrierspcgs66_isautrepiece_modeletypecourrierpcg66_id_idx ON piecesmodelestypescourrierspcgs66 (isautrepiece, modeletypecourrierpcg66_id) WHERE isautrepiece = '1';


SELECT add_missing_table_field ('public', 'modelestraitementspcgs66', 'autrepiecemanquante', 'TEXT');
-- *****************************************************************************
COMMIT;
-- *****************************************************************************