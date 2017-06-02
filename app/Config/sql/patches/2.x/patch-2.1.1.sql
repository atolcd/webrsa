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

ALTER TABLE tiersprestatairesapres ALTER COLUMN nomtiturib TYPE VARCHAR(100);

-- 20120209: ajout d'un index pour les filtres "Uniquement la dernière demande RSA pour un même allocataire"
DROP INDEX IF EXISTS personnes_nir13_trim_idx;
CREATE INDEX personnes_nir13_trim_idx ON personnes ( SUBSTRING( TRIM( BOTH ' ' FROM nir ) FROM 1 FOR 13 ) );

-- 20120209: remplacement des colones de types TYPE_STATUTDEMRSA et TYPE_FONORGCEDMUT en types CHARACTER
--           pour ne plus avoir de problème avec l'intégration des flux et ajout de contraintes pour blinder
--           la base
CREATE OR REPLACE FUNCTION cakephp_validate_in_list( text, text[] ) RETURNS boolean AS
$$
	SELECT $1 IS NULL OR ( ARRAY[CAST($1 AS TEXT)] <@ CAST($2 AS TEXT[]) );
$$
LANGUAGE 'sql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_in_list( text, text[] ) IS
	'@see http://api.cakephp.org/class/validation#method-ValidationinList';

ALTER TABLE dossiers ALTER COLUMN statudemrsa TYPE CHARACTER(1) USING CAST(statudemrsa AS CHARACTER(1));
ALTER TABLE dossiers ALTER COLUMN fonorgcedmut TYPE CHARACTER(3) USING CAST(fonorgcedmut AS CHARACTER(3));
ALTER TABLE dossiers ALTER COLUMN fonorgprenmut TYPE CHARACTER(3) USING CAST(fonorgprenmut AS CHARACTER(3));

ALTER TABLE dossiers ADD CONSTRAINT dossiers_statudemrsa_in_list_chk CHECK ( cakephp_validate_in_list( statudemrsa, ARRAY['N', 'C', 'A', 'M', 'S'] ) );
ALTER TABLE dossiers ADD CONSTRAINT dossiers_fonorgcedmut_in_list_chk CHECK ( cakephp_validate_in_list( fonorgcedmut, ARRAY['CAF', 'MSA', 'OPF'] ) );
ALTER TABLE dossiers ADD CONSTRAINT dossiers_fonorgprenmut_in_list_chk CHECK ( cakephp_validate_in_list( fonorgprenmut, ARRAY['CAF', 'MSA', 'OPF'] ) );

DROP TYPE IF EXISTS TYPE_STATUTDEMRSA;
DROP TYPE IF EXISTS TYPE_FONORGCEDMUT;

-- 20120217: Changement de la volatilité de certaines fonctions car celles-ci sont sans effet de bord
ALTER FUNCTION public.cakephp_validate_ssn (text, text, text) IMMUTABLE;
ALTER FUNCTION public.calcul_cle_nir (text) IMMUTABLE RETURNS NULL ON NULL INPUT;

CREATE OR REPLACE FUNCTION public.nir_correct13( TEXT ) RETURNS BOOLEAN AS
$body$
	DECLARE
		p_nir text;
	BEGIN
		p_nir:=$1;

		IF p_nir IS NULL THEN
			RETURN false;
		END IF;

		RETURN (
			CHAR_LENGTH( TRIM( BOTH ' ' FROM p_nir ) ) >= 13
			AND (
				cakephp_validate_ssn( SUBSTRING( p_nir FROM 1 FOR 13 ) || calcul_cle_nir( SUBSTRING( p_nir FROM 1 FOR 13 ) ), null, 'fr' )
			)
		);
	END;
$body$
LANGUAGE 'plpgsql' IMMUTABLE;

COMMENT ON FUNCTION public.nir_correct13( TEXT ) IS
	'Vérification du format du NIR sur 13 caractères (la clé est recalculée dans tous les cas) grâce à la fonction public.cakephp_validate_ssn. Retourne false en cas de nir NULL';

DROP INDEX IF EXISTS personnes_nir_correct13_idx;
CREATE INDEX personnes_nir_correct13_idx ON personnes ( nir_correct13(nir) );

DROP INDEX IF EXISTS personnes_nir_correct13_nir13_trim_dtnai_idx;
CREATE INDEX personnes_nir_correct13_nir13_trim_dtnai_idx ON personnes ( nir_correct13(nir), SUBSTRING( TRIM( BOTH ' ' FROM nir ) FROM 1 FOR 13 ), dtnai );

DROP INDEX IF EXISTS personnes_upper_nom_upper_prenom_dtnai_idx;
CREATE INDEX personnes_upper_nom_upper_prenom_dtnai_idx ON personnes ( UPPER(nom), UPPER(prenom), dtnai );

-- 20120221
DELETE FROM pdfs WHERE modele = 'Passagecommissionep';
ALTER TABLE historiqueetatspe ALTER COLUMN localite TYPE VARCHAR(250);

-- 20120420: Mise à jour des orientations du CG 58 passées en COV avant la version 2.0.8
CREATE OR REPLACE FUNCTION public.nettoyage_orientsstructs58() RETURNS VOID AS
$$
	DECLARE
		v_row   record;
		v_query text;
	BEGIN
		FOR v_row IN
			SELECT
					proposorientationscovs58.user_id,
					proposorientationscovs58.datedemande,
					orientsstructs.id AS orientstruct_id
				FROM orientsstructs
					INNER JOIN dossierscovs58 ON ( dossierscovs58.personne_id = orientsstructs.personne_id )
					INNER JOIN proposorientationscovs58 ON ( dossierscovs58.id = proposorientationscovs58.dossiercov58_id )
					INNER JOIN passagescovs58 ON ( passagescovs58.dossiercov58_id = dossierscovs58.id )
					INNER JOIN decisionsproposorientationscovs58 ON ( passagescovs58.id = decisionsproposorientationscovs58.passagecov58_id )
					INNER JOIN covs58 ON ( passagescovs58.cov58_id = covs58.id )
				WHERE
					orientsstructs.statut_orient = 'Orienté'
					AND orientsstructs.user_id IS NULL
					AND orientsstructs.date_propo IS NULL
					AND decisionsproposorientationscovs58.etapecov = 'finalise'
					AND decisionsproposorientationscovs58.decisioncov IN ( 'valide', 'refuse' )
					AND DATE_TRUNC('day', covs58.datecommission) = orientsstructs.date_valid
					AND decisionsproposorientationscovs58.typeorient_id = orientsstructs.typeorient_id
					AND decisionsproposorientationscovs58.structurereferente_id = orientsstructs.structurereferente_id
		LOOP
			-- Mise à jour dans la table historiqueetatspe
			v_query := 'UPDATE orientsstructs SET user_id = ' || v_row.user_id || ', date_propo = \'' || v_row.datedemande || '\' WHERE id = ' || v_row.orientstruct_id || ';';
			RAISE NOTICE  '%', v_query;
			EXECUTE v_query;
		END LOOP;
	END;
$$
LANGUAGE plpgsql;

SELECT public.nettoyage_orientsstructs58();
DROP FUNCTION public.nettoyage_orientsstructs58();

-- 20120423: On veut des entrées uniques dans la table nonrespectssanctionseps93; il est possible d'avoir des
-- problèmes de doublons (détectés lorsque Gedooo est tombé). Il vaut mieux que le patch ne passe pas
-- maintenant, et nettoyer les données au plus vite, plutôt que d'attendre.
CREATE UNIQUE INDEX nonrespectssanctionseps93_origine_orientstruct_id_rgpassage_idx ON nonrespectssanctionseps93 ( origine, orientstruct_id, rgpassage ) WHERE orientstruct_id IS NOT NULL;
CREATE UNIQUE INDEX nonrespectssanctionseps93_origine_propopdo_id_rgpassage_idx ON nonrespectssanctionseps93 ( origine, propopdo_id, rgpassage ) WHERE propopdo_id IS NOT NULL;
CREATE UNIQUE INDEX nonrespectssanctionseps93_origine_contratinsertion_id_rgpassage_idx ON nonrespectssanctionseps93 ( origine, contratinsertion_id, rgpassage ) WHERE contratinsertion_id IS NOT NULL;
CREATE UNIQUE INDEX nonrespectssanctionseps93_origine_historiqueetatpe_id_rgpassage_idx ON nonrespectssanctionseps93 ( origine, historiqueetatpe_id, rgpassage ) WHERE historiqueetatpe_id IS NOT NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
