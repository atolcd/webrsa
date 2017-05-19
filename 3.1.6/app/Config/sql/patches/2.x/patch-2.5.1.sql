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

-------------------------------------------------------------------------------------
-- 20130619 : Ajout d'un champ isactif pour masquer les organismes n'étant plus actifs
-------------------------------------------------------------------------------------
SELECT add_missing_table_field( 'public', 'orgstransmisdossierspcgs66', 'isactif', 'VARCHAR(1)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'orgstransmisdossierspcgs66', 'orgstransmisdossierspcgs66_isactif_in_list_chk' );
ALTER TABLE orgstransmisdossierspcgs66 ADD CONSTRAINT orgstransmisdossierspcgs66_isactif_in_list_chk CHECK ( cakephp_validate_in_list( isactif, ARRAY['0','1'] ) );
UPDATE orgstransmisdossierspcgs66 SET isactif = '1' WHERE isactif IS NULL;
ALTER TABLE orgstransmisdossierspcgs66 ALTER COLUMN isactif SET DEFAULT '1'::VARCHAR(1);

-------------------------------------------------------------------------------------
-- 20130619 : Transformation du type enum en check in liste
-------------------------------------------------------------------------------------
ALTER TABLE proposdecisionscers66 ALTER COLUMN nonvalidationparticulier TYPE VARCHAR(9) USING CAST(nonvalidationparticulier AS VARCHAR(9));
SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscers66', 'proposdecisionscers66_nonvalidationparticulier_in_list_chk' );
ALTER TABLE proposdecisionscers66 ADD CONSTRAINT proposdecisionscers66_nonvalidationparticulier_in_list_chk CHECK ( cakephp_validate_in_list( nonvalidationparticulier, ARRAY['reprise','radiation','etudiant'] ) );

-------------------------------------------------------------------------------------
-- 20130619 : Ajout d'une table rupture (0,1) en lien avec les CUIs
-------------------------------------------------------------------------------------

DROP TABLE IF EXISTS rupturescuis66 CASCADE;
CREATE TABLE rupturescuis66(
	id			                      SERIAL NOT NULL PRIMARY KEY,
  cui_id		                    INTEGER NOT NULL REFERENCES cuis(id) ON DELETE CASCADE ON UPDATE CASCADE,
  user_id		                    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  observation		                TEXT NULL,
  daterupturecui                DATE NOT NULL,
  dateenregistrementrupture     DATE NOT NULL,
	created		                    TIMESTAMP WITHOUT TIME ZONE,
  modified	                    TIMESTAMP WITHOUT TIME ZONE
);

COMMENT ON TABLE rupturescuis66 IS 'Saisie de la rupture du CUI (CG66)';

DROP INDEX IF EXISTS rupturescuis66_cui_id_idx;
CREATE INDEX rupturescuis66_cui_id_idx ON rupturescuis66( cui_id );

DROP INDEX IF EXISTS rupturescuis66_user_id_idx;
CREATE INDEX rupturescuis66_user_id_idx ON rupturescuis66( user_id );
-------------------------------------------------------------------------------------
-- 20130619 : Ajout d'une table de paramétrage pour les motifs de rupture CUI
-------------------------------------------------------------------------------------
DROP TABLE IF EXISTS motifssortiecuis66 CASCADE;
DROP TABLE IF EXISTS motifsrupturescuis66 CASCADE;
CREATE TABLE motifsrupturescuis66(
  id			SERIAL NOT NULL PRIMARY KEY,
  name		VARCHAR(250) NOT NULL,
  created		TIMESTAMP WITHOUT TIME ZONE,
  modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsrupturescuis66 IS 'Liste des motifs de rupture pour les CUIs (CG66)';

DROP INDEX IF EXISTS motifsrupturescuis66_name_idx;
CREATE UNIQUE INDEX motifsrupturescuis66_name_idx ON motifsrupturescuis66( name );


-------------------------------------------------------------------------------------
-- 20130619 : Ajout d'une table de liaison entre les ruptures de cuis et les motifs de ruptures
-------------------------------------------------------------------------------------
DROP TABLE IF EXISTS motifsrupturescuis66_rupturescuis66 CASCADE;
CREATE TABLE motifsrupturescuis66_rupturescuis66 (
  id                 					SERIAL NOT NULL PRIMARY KEY,
  motifrupturecui66_id			INTEGER NOT NULL REFERENCES motifsrupturescuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
  rupturecui66_id					INTEGER NOT NULL REFERENCES rupturescuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
  created								TIMESTAMP WITHOUT TIME ZONE,
  modified							TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifsrupturescuis66_rupturescuis66 IS 'Table de liaison entre les motifs de ruptures de cuis et les ruptures de cuis (CG 66)';
DROP INDEX IF EXISTS motifsrupturescuis66_rupturescuis66_motifrupturecui66_id_idx;
CREATE INDEX motifsrupturescuis66_rupturescuis66_motifrupturecui66_id_idx ON motifsrupturescuis66_rupturescuis66(motifrupturecui66_id);

DROP INDEX IF EXISTS motifsrupturescuis66_rupturescuis66_rupturecui66_id_idx;
CREATE INDEX motifsrupturescuis66_rupturescuis66_rupturecui66_id_idx ON motifsrupturescuis66_rupturescuis66(rupturecui66_id);

DROP INDEX IF EXISTS motifsrupturescuis66_rupturescuis66_motifrupturecui66_id_rupturecui66_id_idx;
CREATE UNIQUE INDEX motifsrupturescuis66_rupturescuis66_motifrupturecui66_id_rupturecui66_id_idx ON motifsrupturescuis66_rupturescuis66(motifrupturecui66_id,rupturecui66_id);


-------------------------------------------------------------------------------------
-- 20130620 : Modification de la contrainte (décision) sur les tables cuis et decisionscuis66
-------------------------------------------------------------------------------------
SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscuis66', 'decisioncui_decisioncui_in_list_chk' );

SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscuis66', 'decisionscuis66_decisioncui_in_list_chk' );
ALTER TABLE decisionscuis66 ADD CONSTRAINT decisionscuis66_decisioncui_in_list_chk CHECK ( cakephp_validate_in_list( decisioncui, ARRAY['accord','refus','enattente','annule','rupture'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_decisioncui_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_decisioncui_in_list_chk CHECK ( cakephp_validate_in_list( decisioncui, ARRAY['accord','refus','enattente','annule','rupture'] ) );


ALTER TABLE cuis ALTER COLUMN positioncui66 TYPE VARCHAR(20) USING CAST(positioncui66 AS VARCHAR(20));
SELECT alter_table_drop_constraint_if_exists( 'public', 'cuis', 'cuis_positioncui66_in_list_chk' );
ALTER TABLE cuis ADD CONSTRAINT cuis_positioncui66_in_list_chk CHECK ( cakephp_validate_in_list( positioncui66, ARRAY['attavismne','attaviselu','attavisreferent','attdecision','encours','annule','fincontrat','attrenouv','perime','nonvalide','valid','validnotifie','nonvalidnotifie','rupture'] ) );
ALTER TABLE cuis ALTER COLUMN positioncui66 SET NOT NULL;


SELECT add_missing_table_field( 'public', 'suspensionscuis66', 'observation', 'TEXT' );
SELECT add_missing_table_field( 'public', 'suspensionscuis66', 'datedebut', 'DATE' );
ALTER TABLE suspensionscuis66 ALTER COLUMN datedebut SET NOT NULL;
SELECT add_missing_table_field( 'public', 'suspensionscuis66', 'datefin', 'DATE' );
ALTER TABLE suspensionscuis66 ALTER COLUMN datefin SET NOT NULL;
SELECT add_missing_table_field( 'public', 'suspensionscuis66', 'formatjournee', 'VARCHAR(9)' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'suspensionscuis66', 'suspensionscuis66_formatjournee_in_list_chk' );
ALTER TABLE suspensionscuis66 ADD CONSTRAINT suspensionscuis66_formatjournee_in_list_chk CHECK ( cakephp_validate_in_list( formatjournee, ARRAY['matin','apresmidi','journee'] ) );

-------------------------------------------------------------------------------------
-- 20130620 : Ajout d'une table de paramétrage pour les motifs de suspensions CUI
-------------------------------------------------------------------------------------
DROP TABLE IF EXISTS motifssuspensioncuis66 CASCADE;
DROP TABLE IF EXISTS motifssuspensioncuis66 CASCADE;
CREATE TABLE motifssuspensioncuis66(
  id			SERIAL NOT NULL PRIMARY KEY,
  name		VARCHAR(250) NOT NULL,
  created		TIMESTAMP WITHOUT TIME ZONE,
  modified	TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifssuspensioncuis66 IS 'Liste des motifs de suspension pour les CUIs (CG66)';

DROP INDEX IF EXISTS motifssuspensioncuis66_name_idx;
CREATE UNIQUE INDEX motifssuspensioncuis66_name_idx ON motifssuspensioncuis66( name );


DROP TABLE IF EXISTS motifssuspensioncuis66_suspensionscuis66 CASCADE;
CREATE TABLE motifssuspensioncuis66_suspensionscuis66 (
  id                 					SERIAL NOT NULL PRIMARY KEY,
  motifsuspensioncui66_id			INTEGER NOT NULL REFERENCES motifssuspensioncuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
  suspensioncui66_id					INTEGER NOT NULL REFERENCES suspensionscuis66(id) ON DELETE CASCADE ON UPDATE CASCADE,
  created								TIMESTAMP WITHOUT TIME ZONE,
  modified							TIMESTAMP WITHOUT TIME ZONE
);
COMMENT ON TABLE motifssuspensioncuis66_suspensionscuis66 IS 'Table de liaison entre les motifs de suspension de cuis et les suspensions de cuis (CG 66)';
DROP INDEX IF EXISTS motifssuspensioncuis66_suspensionscuis66_motifsuspensioncui66_id_idx;
CREATE INDEX motifssuspensioncuis66_suspensionscuis66_motifsuspensioncui66_id_idx ON motifssuspensioncuis66_suspensionscuis66(motifsuspensioncui66_id);

DROP INDEX IF EXISTS motifssuspensioncuis66_suspensionscuis66_suspensioncui66_id_idx;
CREATE INDEX motifssuspensioncuis66_suspensionscuis66_suspensioncui66_id_idx ON motifssuspensioncuis66_suspensionscuis66(suspensioncui66_id);

DROP INDEX IF EXISTS motifssuspensioncuis66_suspensionscuis66_motifsuspensioncui66_id_rupturecui66_id_idx;
CREATE UNIQUE INDEX motifssuspensioncuis66_suspensionscuis66_motifsuspensioncui66_id_rupturecui66_id_idx ON motifssuspensioncuis66_suspensionscuis66(motifsuspensioncui66_id,suspensioncui66_id);

SELECT add_missing_table_field('public', 'suspensionscuis66', 'haspiecejointe', 'type_booleannumber' );
ALTER TABLE suspensionscuis66 ALTER COLUMN haspiecejointe SET DEFAULT '0'::TYPE_BOOLEANNUMBER;
UPDATE suspensionscuis66 SET haspiecejointe = '0'::TYPE_BOOLEANNUMBER WHERE haspiecejointe IS NULL;
ALTER TABLE suspensionscuis66 ALTER COLUMN haspiecejointe SET NOT NULL;

-- -----------------------------------------------------------------------------
-- Tableaux de suivis PDV 93
-- -----------------------------------------------------------------------------

-- Ajouter une colonne version (+ année + structurereferente)
DROP TABLE IF EXISTS tableauxsuivispdvs93 CASCADE;
CREATE TABLE tableauxsuivispdvs93 (
	id						SERIAL NOT NULL PRIMARY KEY,
	name					VARCHAR(255) NOT NULL,
	annee					INTEGER NOT NULL,
	structurereferente_id	INTEGER DEFAULT NULL REFERENCES structuresreferentes(id) ON DELETE SET NULL ON UPDATE CASCADE,
	version					VARCHAR(33) NOT NULL,
	search					TEXT NOT NULL,
	results					TEXT NOT NULL,
	user_id					INTEGER DEFAULT NULL REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
	created					TIMESTAMP WITHOUT TIME ZONE,
	modified				TIMESTAMP WITHOUT TIME ZONE
);

DROP INDEX IF EXISTS tableauxsuivispdvs93_name_idx;
CREATE INDEX tableauxsuivispdvs93_name_idx ON tableauxsuivispdvs93(name);

DROP INDEX IF EXISTS tableauxsuivispdvs93_annee_idx;
CREATE INDEX tableauxsuivispdvs93_annee_idx ON tableauxsuivispdvs93(annee);

DROP INDEX IF EXISTS tableauxsuivispdvs93_structurereferente_id_idx;
CREATE INDEX tableauxsuivispdvs93_structurereferente_id_idx ON tableauxsuivispdvs93(structurereferente_id);

DROP INDEX IF EXISTS tableauxsuivispdvs93_version_idx;
CREATE INDEX tableauxsuivispdvs93_version_idx ON tableauxsuivispdvs93(version);

DROP INDEX IF EXISTS tableauxsuivispdvs93_user_id_idx;
CREATE INDEX tableauxsuivispdvs93_user_id_idx ON tableauxsuivispdvs93(user_id);

-------------------------------------------------------------------------------------
-- 20130628 : Modification des valeurs prises par les avis techniques du CUI
-------------------------------------------------------------------------------------
SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscuis66', 'proposdecisionscuis66_propositioncui_in_list_chk' );
ALTER TABLE proposdecisionscuis66 ADD CONSTRAINT proposdecisionscuis66_propositioncui_in_list_chk CHECK ( cakephp_validate_in_list( propositioncui, ARRAY['enattente','accord','refus','reserve'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscuis66', 'proposdecisionscuis66_propositioncuireferent_in_list_chk' );
ALTER TABLE proposdecisionscuis66 ADD CONSTRAINT proposdecisionscuis66_propositioncuireferent_in_list_chk CHECK ( cakephp_validate_in_list( propositioncuireferent, ARRAY['enattente','accord','refus','reserve'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'proposdecisionscuis66', 'proposdecisionscuis66_propositioncuielu_in_list_chk' );
ALTER TABLE proposdecisionscuis66 ADD CONSTRAINT proposdecisionscuis66_propositioncuielu_in_list_chk CHECK ( cakephp_validate_in_list( propositioncuielu, ARRAY['enattente','accord','refus','reserve'] ) );

-------------------------------------------------------------------------------------
-- 20130628 : Ajout des thématiques de rendez-vous
-------------------------------------------------------------------------------------

-- TODO: ajouter un champ contrainte (bool) -> statutrdv_id / linkedmodel
DROP TABLE IF EXISTS thematiquesrdvs CASCADE;
CREATE TABLE thematiquesrdvs (
	id				SERIAL NOT NULL PRIMARY KEY,
	name			VARCHAR(255) NOT NULL,
	typerdv_id		INTEGER NOT NULL REFERENCES typesrdv(id) ON DELETE SET NULL ON UPDATE CASCADE,
	statutrdv_id	INTEGER DEFAULT NULL REFERENCES statutsrdvs(id) ON DELETE SET NULL ON UPDATE CASCADE,
	linkedmodel		VARCHAR(255) DEFAULT NULL,
	created			TIMESTAMP WITHOUT TIME ZONE,
	modified		TIMESTAMP WITHOUT TIME ZONE
);

DROP INDEX IF EXISTS thematiquesrdvs_name_idx;
CREATE UNIQUE INDEX thematiquesrdvs_name_idx ON thematiquesrdvs(name);

DROP INDEX IF EXISTS thematiquesrdvs_statutrdv_id_idx;
CREATE INDEX thematiquesrdvs_statutrdv_id_idx ON thematiquesrdvs(statutrdv_id);

DROP INDEX IF EXISTS thematiquesrdvs_typerdv_id_idx;
CREATE INDEX thematiquesrdvs_typerdv_id_idx ON thematiquesrdvs(typerdv_id);

DROP INDEX IF EXISTS thematiquesrdvs_linkedmodel_idx;
CREATE INDEX thematiquesrdvs_linkedmodel_idx ON thematiquesrdvs(linkedmodel);

-- 20130630 : Ajout de la table de liaison avec les rendez-vous
DROP TABLE IF EXISTS rendezvous_thematiquesrdvs CASCADE;
CREATE TABLE rendezvous_thematiquesrdvs (
	id					SERIAL NOT NULL PRIMARY KEY,
	rendezvous_id		INTEGER DEFAULT NULL REFERENCES rendezvous(id) ON DELETE CASCADE ON UPDATE CASCADE,
	thematiquerdv_id	INTEGER DEFAULT NULL REFERENCES thematiquesrdvs(id) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP INDEX IF EXISTS rendezvous_thematiquesrdvs_rendezvous_id_idx;
CREATE INDEX rendezvous_thematiquesrdvs_rendezvous_id_idx ON rendezvous_thematiquesrdvs(rendezvous_id);

DROP INDEX IF EXISTS rendezvous_thematiquesrdvs_thematiquerdv_id_idx;
CREATE INDEX rendezvous_thematiquesrdvs_thematiquerdv_id_idx ON rendezvous_thematiquesrdvs(thematiquerdv_id);

DROP INDEX IF EXISTS rendezvous_thematiquesrdvs_rendezvous_id_thematiquerdv_id_idx;
CREATE UNIQUE INDEX rendezvous_thematiquesrdvs_rendezvous_id_thematiquerdv_id_idx ON rendezvous_thematiquesrdvs(rendezvous_id,thematiquerdv_id);

-- Suppression de la colonne questionnairesd1pdvs93.valide qui ne sert plus à rien
SELECT alter_table_drop_column_if_exists( 'public', 'questionnairesd1pdvs93', 'valide' );

-- -----------------------------------------------------------------------------
-- 20130703 - Historisation des droits et devoirs et de l'état du droit
-- -----------------------------------------------------------------------------

DROP TABLE IF EXISTS historiquesdroits CASCADE;
CREATE TABLE historiquesdroits (
	id					SERIAL NOT NULL PRIMARY KEY,
	personne_id			INTEGER NOT NULL REFERENCES personnes(id) ON DELETE CASCADE ON UPDATE CASCADE,
	toppersdrodevorsa	VARCHAR(1) DEFAULT NULL, -- calculsdroitsrsa.toppersdrodevorsa
	etatdosrsa			VARCHAR(1) DEFAULT NULL, -- situationsdossiersrsa.etatdosrsa
	created				TIMESTAMP WITHOUT TIME ZONE,
	modified			TIMESTAMP WITHOUT TIME ZONE
);

DROP INDEX IF EXISTS historiquesdroits_personne_id_idx;
CREATE INDEX historiquesdroits_personne_id_idx ON historiquesdroits( personne_id );

SELECT alter_table_drop_constraint_if_exists( 'public', 'historiquesdroits', 'historiquesdroits_toppersdrodevorsa_in_list_chk' );
ALTER TABLE historiquesdroits ADD CONSTRAINT historiquesdroits_toppersdrodevorsa_in_list_chk CHECK ( cakephp_validate_in_list( toppersdrodevorsa, ARRAY['0', '1'] ) );

SELECT alter_table_drop_constraint_if_exists( 'public', 'historiquesdroits', 'historiquesdroits_etatdosrsa_in_list_chk' );
ALTER TABLE historiquesdroits ADD CONSTRAINT historiquesdroits_etatdosrsa_in_list_chk CHECK ( cakephp_validate_in_list( etatdosrsa, ARRAY['Z', '0', '1', '2', '3', '4', '5', '6'] ) );


-- -----------------------------------------------------------------------------
-- 20130705 - Transformation de l'enum de la table accompagnementscuis66 par un check in list
-- -----------------------------------------------------------------------------
ALTER TABLE accompagnementscuis66 ALTER COLUMN typeaccompagnementcui66 TYPE VARCHAR(9) USING CAST(typeaccompagnementcui66 AS VARCHAR(9));
SELECT alter_table_drop_constraint_if_exists( 'public', 'accompagnementscuis66', 'accompagnementscuis66_typeaccompagnementcui66_in_list_chk' );
ALTER TABLE accompagnementscuis66 ADD CONSTRAINT accompagnementscuis66_typeaccompagnementcui66_in_list_chk CHECK ( cakephp_validate_in_list( typeaccompagnementcui66, ARRAY['periode','formation','bilan'] ) );

-- -----------------------------------------------------------------------------
-- 20130708 - Suppression des situationsallocataires qui ne sont pas liées à un
-- questionnairesd1pdvs93.
-- -----------------------------------------------------------------------------

DELETE FROM situationsallocataires WHERE id NOT IN (
	SELECT questionnairesd1pdvs93.situationallocataire_id
		FROM questionnairesd1pdvs93
);

-- -----------------------------------------------------------------------------
-- 20130725 - Ajout d'un index unique sur les intitulés des types de RDV
-- -----------------------------------------------------------------------------

DROP INDEX IF EXISTS typesrdv_libelle_idx;
CREATE UNIQUE INDEX typesrdv_libelle_idx ON typesrdv( libelle );

-- *****************************************************************************
COMMIT;
-- *****************************************************************************