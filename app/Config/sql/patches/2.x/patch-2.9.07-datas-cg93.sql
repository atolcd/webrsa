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

-- Insertion des entrées venant de situationsallocataires afin de compléter historiquesdroits
INSERT INTO historiquesdroits ( personne_id, toppersdrodevorsa, etatdosrsa, created, modified )
	SELECT personne_id, toppersdrodevorsa, etatdosrsa, created, modified
		FROM situationsallocataires;

-- Dédoublonnage des informations contenues dans historiquesdroits
CREATE OR REPLACE FUNCTION public.webrsa_historiquesdroits_dedoublonnage() RETURNS VOID AS
$$
	DECLARE
		v_row record;
		v_query text;
		v_count integer;
	BEGIN
		v_count := 0;

		FOR v_row IN
			SELECT
					histo.id AS histo_id,
					histo.personne_id AS histo_personne_id,
					histo.toppersdrodevorsa AS histo_toppersdrodevorsa,
					histo.etatdosrsa AS histo_etatdosrsa,
					histo.created AS histo_created,
					histo.modified AS histo_modified,
					histosvt.id AS histosvt_id,
					histosvt.personne_id AS histosvt_personne_id,
					histosvt.toppersdrodevorsa AS histosvt_toppersdrodevorsa,
					histosvt.etatdosrsa AS histosvt_etatdosrsa,
					histosvt.created AS histosvt_created,
					histosvt.modified AS histosvt_modified
				FROM historiquesdroits AS histo,
					historiquesdroits AS histosvt
				WHERE
					histosvt.id = (
						SELECT h.id
							FROM historiquesdroits AS h
							WHERE
								h.id <> histo.id
								AND h.personne_id = histo.personne_id
								AND h.created::DATE >= histo.created::DATE
							ORDER BY h.created ASC
							LIMIT 1
					)
				ORDER BY histo.created ASC
		LOOP
			IF (
				v_row.histo_toppersdrodevorsa = v_row.histosvt_toppersdrodevorsa
				AND v_row.histo_etatdosrsa = v_row.histosvt_etatdosrsa
			) THEN
				v_query := 'UPDATE historiquesdroits SET modified = ( CASE WHEN ''' || v_row.histo_modified || '''::TIMESTAMP > ''' || v_row.histosvt_modified || '''::TIMESTAMP THEN ''' || v_row.histo_modified || '''::TIMESTAMP ELSE ''' || v_row.histosvt_modified || '''::TIMESTAMP END ) WHERE id = ''' || v_row.histo_id || ''';';
				RAISE NOTICE '%', v_query;
				EXECUTE v_query;

				v_query := 'DELETE FROM historiquesdroits WHERE id = ''' || v_row.histosvt_id || ''';';
				RAISE NOTICE '%', v_query;
				EXECUTE v_query;
			END IF;
			v_count := v_count + 1;
		END LOOP;
		RAISE NOTICE '% enregistrements traités', v_count;
	END;
$$
LANGUAGE plpgsql VOLATILE;

SELECT public.webrsa_historiquesdroits_dedoublonnage();

-- Consolidation des données de la table historiquesdroits
CREATE OR REPLACE FUNCTION public.webrsa_historiquesdroits_consolidation() RETURNS VOID AS
$$
	DECLARE
		v_row record;
		v_query text;
		v_count integer;
	BEGIN
		v_count := 0;

		FOR v_row IN
			SELECT
					histo.id,
					( histosvt.created - INTERVAL '1 day' ) AS modified
				FROM
					historiquesdroits AS histo,
					historiquesdroits AS histosvt
				WHERE
					histosvt.id = (
						SELECT h.id
							FROM historiquesdroits AS h
							WHERE
								h.id <> histo.id
								AND h.personne_id = histo.personne_id
								AND h.created::DATE >= histo.created::DATE
							ORDER BY h.created ASC
							LIMIT 1
					)
					AND ( histosvt.created - INTERVAL '1 day' )::DATE > histo.modified::DATE
		LOOP
			v_query := 'UPDATE historiquesdroits SET modified = ''' || v_row.modified || ''' WHERE id = ''' || v_row.id || ''';';
			RAISE NOTICE '%', v_query;
			EXECUTE v_query;
			v_count := v_count + 1;
		END LOOP;
		RAISE NOTICE '% enregistrements traités', v_count;
	END;
$$
LANGUAGE plpgsql VOLATILE;

SELECT public.webrsa_historiquesdroits_consolidation();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************