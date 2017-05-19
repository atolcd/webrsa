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

CREATE OR REPLACE FUNCTION public.correction_proposnonorientationsproscovs58() RETURNS VOID AS
$$
	DECLARE
		v_row record;
		v_query text;
		v_rg_ci integer;
		v_num_contrat type_num_contrat;
	BEGIN
		CREATE TEMPORARY TABLE omega(
			propocontratinsertioncov58_id INTEGER,
			personne_id INTEGER,
			structurereferente_id INTEGER,
			referent_id INTEGER,
			num_contrat TYPE_NUM_CONTRAT,
			dd_ci DATE,
			duree_engag INTEGER,
			df_ci DATE,
			forme_ci CHAR(1),
			avisraison_ci CHAR(1),
			rg_ci INTEGER,
			observ_ci TEXT,
			date_saisi_ci DATE,
			datevalidation_ci DATE,
			decision_ci CHAR(1),
			avenant_id INTEGER,
			created TIMESTAMP WITHOUT TIME ZONE,
			modified TIMESTAMP WITHOUT TIME ZONE
		);

		INSERT INTO omega ( propocontratinsertioncov58_id, personne_id, structurereferente_id, referent_id, num_contrat, dd_ci, duree_engag, df_ci, forme_ci, avisraison_ci, rg_ci, observ_ci, date_saisi_ci, datevalidation_ci, decision_ci, avenant_id, created, modified )
			SELECT
					proposcontratsinsertioncovs58.id,
					dossierscovs58.personne_id,
					proposcontratsinsertioncovs58.structurereferente_id,
					proposcontratsinsertioncovs58.referent_id,
					proposcontratsinsertioncovs58.num_contrat,
					decisionsproposcontratsinsertioncovs58.dd_ci,
					decisionsproposcontratsinsertioncovs58.duree_engag,
					decisionsproposcontratsinsertioncovs58.df_ci,
					proposcontratsinsertioncovs58.forme_ci,
					proposcontratsinsertioncovs58.avisraison_ci,
					proposcontratsinsertioncovs58.rg_ci,
					decisionsproposcontratsinsertioncovs58.commentaire,
					proposcontratsinsertioncovs58.datedemande,
					decisionsproposcontratsinsertioncovs58.datevalidation,
					'V',
					proposcontratsinsertioncovs58.avenant_id,
					covs58.datecommission,
					covs58.datecommission
				FROM dossierscovs58
					LEFT OUTER JOIN passagescovs58 ON ( passagescovs58.dossiercov58_id = dossierscovs58.id )
					LEFT OUTER JOIN covs58 ON ( passagescovs58.cov58_id = covs58.id )
					LEFT OUTER JOIN proposcontratsinsertioncovs58 ON ( proposcontratsinsertioncovs58.dossiercov58_id = dossierscovs58.id )
					LEFT OUTER JOIN decisionsproposcontratsinsertioncovs58 ON ( decisionsproposcontratsinsertioncovs58.passagecov58_id = passagescovs58.id )
					LEFT OUTER JOIN contratsinsertion ON ( proposcontratsinsertioncovs58.nvcontratinsertion_id = contratsinsertion.id )
				WHERE
					passagescovs58.etatdossiercov = 'traite'
					AND dossierscovs58.themecov58 = 'proposcontratsinsertioncovs58'
					AND decisionsproposcontratsinsertioncovs58.decisioncov = 'valide'
					AND proposcontratsinsertioncovs58.nvcontratinsertion_id IS NULL;

		INSERT INTO omega ( propocontratinsertioncov58_id, personne_id, structurereferente_id, referent_id, num_contrat, dd_ci, duree_engag, df_ci, forme_ci, avisraison_ci, rg_ci, observ_ci, date_saisi_ci, datevalidation_ci, decision_ci, avenant_id, created, modified )
			SELECT
						proposcontratsinsertioncovs58.id,
						dossierscovs58.personne_id,
						proposcontratsinsertioncovs58.structurereferente_id,
						proposcontratsinsertioncovs58.referent_id,
						proposcontratsinsertioncovs58.num_contrat,
						proposcontratsinsertioncovs58.dd_ci,
						proposcontratsinsertioncovs58.duree_engag,
						proposcontratsinsertioncovs58.df_ci,
						proposcontratsinsertioncovs58.forme_ci,
						proposcontratsinsertioncovs58.avisraison_ci,
						proposcontratsinsertioncovs58.rg_ci,
						decisionsproposcontratsinsertioncovs58.commentaire,
						proposcontratsinsertioncovs58.datedemande,
						decisionsproposcontratsinsertioncovs58.datevalidation,
						'N',
						proposcontratsinsertioncovs58.avenant_id,
						covs58.datecommission,
						covs58.datecommission
					FROM dossierscovs58
						LEFT OUTER JOIN passagescovs58 ON ( passagescovs58.dossiercov58_id = dossierscovs58.id )
						LEFT OUTER JOIN covs58 ON ( passagescovs58.cov58_id = covs58.id )
						LEFT OUTER JOIN proposcontratsinsertioncovs58 ON ( proposcontratsinsertioncovs58.dossiercov58_id = dossierscovs58.id )
						LEFT OUTER JOIN decisionsproposcontratsinsertioncovs58 ON ( decisionsproposcontratsinsertioncovs58.passagecov58_id = passagescovs58.id )
						LEFT OUTER JOIN contratsinsertion ON ( proposcontratsinsertioncovs58.nvcontratinsertion_id = contratsinsertion.id )
					WHERE
						passagescovs58.etatdossiercov = 'traite'
						AND dossierscovs58.themecov58 = 'proposcontratsinsertioncovs58'
						AND decisionsproposcontratsinsertioncovs58.decisioncov = 'refuse'
						AND proposcontratsinsertioncovs58.nvcontratinsertion_id IS NULL;

		FOR v_row IN
			SELECT * FROM omega ORDER BY modified ASC, propocontratinsertioncov58_id ASC
		LOOP
			IF v_row.decision_ci = 'V' THEN
				SELECT INTO v_rg_ci COUNT(*) + 1 FROM contratsinsertion WHERE contratsinsertion.personne_id = v_row.personne_id AND contratsinsertion.decision_ci = 'V';
			ELSE
				v_rg_ci := NULL;
			END IF;

			IF v_rg_ci IS NULL THEN
				v_num_contrat := NULL;
			ELSE
				IF v_rg_ci = 1 THEN
					v_num_contrat := 'PRE';
				ELSE
					v_num_contrat := 'REN';
				END IF;
			END IF;

			INSERT INTO contratsinsertion (personne_id, structurereferente_id, referent_id, num_contrat, dd_ci, duree_engag, df_ci, forme_ci, avisraison_ci, rg_ci, observ_ci, date_saisi_ci, datevalidation_ci, decision_ci, avenant_id, created, modified)
				VALUES ( v_row.personne_id, v_row.structurereferente_id, v_row.referent_id, v_row.num_contrat, v_row.dd_ci, v_row.duree_engag, v_row.df_ci, v_row.forme_ci, v_row.avisraison_ci, v_rg_ci, v_row.observ_ci, v_row.date_saisi_ci, v_row.datevalidation_ci, v_row.decision_ci, v_row.avenant_id, v_row.created, v_row.modified );

			v_query := 'UPDATE proposcontratsinsertioncovs58
				SET nvcontratinsertion_id = ( SELECT lastval() )'
				|| ' WHERE id = ' || v_row.propocontratinsertioncov58_id || ';';
			EXECUTE v_query;
		END LOOP;

		DROP TABLE omega;
	END;
$$
LANGUAGE plpgsql;

SELECT public.correction_proposnonorientationsproscovs58();
DROP FUNCTION public.correction_proposnonorientationsproscovs58();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
