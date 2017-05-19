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

DROP TABLE IF EXISTS cuis_piecesmailscuis66;

--------------------------------------------------------------------------------
-- commeDroits des anciens moteurs en AroAco
--------------------------------------------------------------------------------

--------------------------------------------------------------------------------
-- Dossierspcgs66
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteresdossierspcgs66';
DELETE FROM acos WHERE alias = 'Module:Cohortesdossierspcgs66';

CREATE OR REPLACE FUNCTION copy_permission_dossierspcgs66() RETURNS void AS
$$
DECLARE
	v_row record;
	module_id integer;
	exportcsv_aco_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Dossierspcgs66');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:cohorte_atransmettre'
		WHERE alias = 'Cohortesdossierspcgs66:atransmettre';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:cohorte_enattenteaffectation'
		WHERE alias = 'Cohortesdossierspcgs66:enattenteaffectation';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:cohorte_imprimer'
		WHERE alias = 'Cohortesdossierspcgs66:aimprimer';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:exportcsv'
		WHERE alias = 'Criteresdossierspcgs66:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:search'
		WHERE alias = 'Criteresdossierspcgs66:dossier';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:search_affectes'
		WHERE alias = 'Cohortesdossierspcgs66:affectes';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Dossierspcgs66:search_gestionnaire'
		WHERE alias = 'Criteresdossierspcgs66:gestionnaire';



    IF NOT EXISTS(SELECT * FROM acos
		WHERE alias = 'Dossierspcgs66:exportcsv_gestionnaire') THEN

        INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
			VALUES (module_id, '', 0, 'Dossierspcgs66:exportcsv_gestionnaire', 0, 0);

		exportcsv_aco_id := (SELECT id FROM acos WHERE alias = 'Dossierspcgs66:exportcsv_gestionnaire');

		FOR v_row IN
			SELECT * FROM aros_acos rc
			JOIN acos c ON rc.aco_id = c.id
			WHERE c.alias = 'Dossierspcgs66:exportcsv'

		LOOP

			INSERT INTO aros_acos (aro_id, aco_id, _create, _read, _update, _delete)
				VALUES (v_row.aro_id, exportcsv_aco_id, v_row._create, v_row._read, v_row._update, v_row._delete);

		END LOOP;

    END IF;

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_dossierspcgs66();
DROP FUNCTION copy_permission_dossierspcgs66();


--------------------------------------------------------------------------------
-- ActionscandidatsPersonnes
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:CriteresActionscandidatsPersonnes';
DELETE FROM acos WHERE alias = 'Module:Cohortesfichescandidature66';

CREATE OR REPLACE FUNCTION copy_permission_actionscandidats_personnes() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:ActionscandidatsPersonnes');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'ActionscandidatsPersonnes:cohorte_enattente'
		WHERE alias = 'Cohortesfichescandidature66:fichesenattente';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'ActionscandidatsPersonnes:cohorte_encours'
		WHERE alias = 'Cohortesfichescandidature66:fichesencours';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'ActionscandidatsPersonnes:exportcsv'
		WHERE alias = 'Criteresfichescandidature:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'ActionscandidatsPersonnes:search'
		WHERE alias = 'Criteresfichescandidature:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_actionscandidats_personnes();
DROP FUNCTION copy_permission_actionscandidats_personnes();


--------------------------------------------------------------------------------
-- Cohortes (d'orientation)
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortes';

CREATE OR REPLACE FUNCTION copy_permission_cohortes() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Orientsstructs');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Orientsstructs:cohorte_enattente'
		WHERE alias = 'Cohortes:enattente';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Orientsstructs:cohorte_impressions'
		WHERE alias = 'Cohortes:cohortegedooo';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Orientsstructs:cohorte_nouvelles'
		WHERE alias = 'Cohortes:nouvelles';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Orientsstructs:cohorte_orientees'
		WHERE alias = 'Cohortes:orientees';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_cohortes();
DROP FUNCTION copy_permission_cohortes();

--------------------------------------------------------------------------------
-- Cohortesci (de CER)
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortesci';

CREATE OR REPLACE FUNCTION copy_permission_cohortesci() RETURNS void AS
$$
DECLARE
	v_row record;
	module_id integer;
	exportcsv_aco_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Contratsinsertion');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Contratsinsertion:cohorte_cerparticulieravalider'
		WHERE alias = 'Cohortesci:nouveauxparticulier';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Contratsinsertion:cohorte_cersimpleavalider'
		WHERE alias = 'Cohortesci:nouveauxsimple';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Contratsinsertion:cohorte_nouveaux'
		WHERE alias = 'Cohortesci:nouveaux';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Contratsinsertion:cohorte_valides'
		WHERE alias = 'Cohortesci:valides';

    IF NOT EXISTS(SELECT * FROM acos
		WHERE alias = 'Contratsinsertion:exportcsv_valides') THEN

        INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
			VALUES (module_id, '', 0, 'Contratsinsertion:exportcsv_valides', 0, 0);

		exportcsv_aco_id := (SELECT id FROM acos WHERE alias = 'Contratsinsertion:exportcsv_valides');

		FOR v_row IN
			SELECT * FROM aros_acos rc
			JOIN acos c ON rc.aco_id = c.id
			WHERE c.alias = 'Contratsinsertion:cohorte_valides'

		LOOP

			INSERT INTO aros_acos (aro_id, aco_id, _create, _read, _update, _delete)
				VALUES (v_row.aro_id, exportcsv_aco_id, v_row._create, v_row._read, v_row._update, v_row._delete);

		END LOOP;

    END IF;

    IF NOT EXISTS(SELECT * FROM acos
		WHERE alias = 'Contratsinsertion:search_valides') THEN

        INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
			VALUES (module_id, '', 0, 'Contratsinsertion:search_valides', 0, 0);

		exportcsv_aco_id := (SELECT id FROM acos WHERE alias = 'Contratsinsertion:search_valides');

		FOR v_row IN
			SELECT * FROM aros_acos rc
			JOIN acos c ON rc.aco_id = c.id
			WHERE c.alias = 'Contratsinsertion:cohorte_valides'

		LOOP

			INSERT INTO aros_acos (aro_id, aco_id, _create, _read, _update, _delete)
				VALUES (v_row.aro_id, exportcsv_aco_id, v_row._create, v_row._read, v_row._update, v_row._delete);

		END LOOP;

    END IF;

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_cohortesci();
DROP FUNCTION copy_permission_cohortesci();


--------------------------------------------------------------------------------
-- Nonorientes66
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortesnonorientes66';

CREATE OR REPLACE FUNCTION copy_permission_nonorientes66() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Nonorientes66');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Nonorientes66:cohorte_imprimeremploi'
		WHERE alias = 'Cohortesnonorientes66:notisemploiaimprimer';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Nonorientes66:cohorte_imprimernotifications'
		WHERE alias = 'Cohortesnonorientes66:notifaenvoyer';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Nonorientes66:cohorte_isemploi'
		WHERE alias = 'Cohortesnonorientes66:isemploi';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Nonorientes66:cohorte_reponse'
		WHERE alias = 'Cohortesnonorientes66:notisemploi';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Nonorientes66:recherche_notifie'
		WHERE alias = 'Cohortesnonorientes66:oriente';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_nonorientes66();
DROP FUNCTION copy_permission_nonorientes66();


--------------------------------------------------------------------------------
-- Apres66
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortesvalidationapres66';

CREATE OR REPLACE FUNCTION copy_permission_apres66() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Apres66');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Apres66:cohorte_imprimer'
		WHERE alias = 'Cohortesvalidationapres66:validees';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Apres66:cohorte_notifiees'
		WHERE alias = 'Cohortesvalidationapres66:notifiees';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Apres66:cohorte_traitement'
		WHERE alias = 'Cohortesvalidationapres66:traitement';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Apres66:cohorte_transfert'
		WHERE alias = 'Cohortesvalidationapres66:transfert';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Apres66:cohorte_validation'
		WHERE alias = 'Cohortesvalidationapres66:apresavalider';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_apres66();
DROP FUNCTION copy_permission_apres66();


--------------------------------------------------------------------------------
-- Cohortesindus
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortesindus';

CREATE OR REPLACE FUNCTION copy_permission_cohortesindus() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Indus');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Indus:exportcsv'
		WHERE alias = 'Cohortesindus:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Indus:search'
		WHERE alias = 'Cohortesindus:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_cohortesindus();
DROP FUNCTION copy_permission_cohortesindus();


--------------------------------------------------------------------------------
-- Bilansparcours66
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteresbilansparcours66';

CREATE OR REPLACE FUNCTION copy_permission_bilansparcours66() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Bilansparcours66');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Bilansparcours66:exportcsv'
		WHERE alias = 'Criteresbilansparcours66:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Bilansparcours66:search'
		WHERE alias = 'Criteresbilansparcours66:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_bilansparcours66();
DROP FUNCTION copy_permission_bilansparcours66();


--------------------------------------------------------------------------------
-- Traitementspcgs66
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criterestraitementspcgs66';

CREATE OR REPLACE FUNCTION copy_permission_traitementspcgs66() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Traitementspcgs66');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Traitementspcgs66:exportcsv'
		WHERE alias = 'Criterestraitementspcgs66:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Traitementspcgs66:search'
		WHERE alias = 'Criterestraitementspcgs66:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_traitementspcgs66();
DROP FUNCTION copy_permission_traitementspcgs66();


--------------------------------------------------------------------------------
-- Defautsinsertionseps66
--------------------------------------------------------------------------------

UPDATE acos
		SET alias = 'Defautsinsertionseps66:search_noninscrits'
		WHERE alias = 'Defautsinsertionseps66:selectionnoninscrits';

UPDATE acos
		SET alias = 'Defautsinsertionseps66:search_radies'
		WHERE alias = 'Defautsinsertionseps66:selectionradies';


--------------------------------------------------------------------------------
-- Propospdos
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortespdos';
DELETE FROM acos WHERE alias = 'Module:Criterespdos';

CREATE OR REPLACE FUNCTION copy_permission_propospdos() RETURNS void AS
$$
DECLARE
	v_row record;
	module_id integer;
	exportcsv_aco_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Propospdos');


	UPDATE acos
		SET parent_id = module_id,
			alias = 'Propospdos:cohorte_nouvelles'
		WHERE alias = 'Cohortespdos:avisdemande';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Propospdos:cohorte_validees'
		WHERE alias = 'Cohortespdos:valide';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Propospdos:exportcsv'
		WHERE alias = 'Criterespdos:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Propospdos:exportcsv_possibles'
		WHERE alias = 'Criterespdos:nouvelles';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Propospdos:exportcsv_validees'
		WHERE alias = 'Cohortespdos:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Propospdos:search'
		WHERE alias = 'Criterespdos:index';

	IF NOT EXISTS(SELECT * FROM acos
		WHERE alias = 'Propospdos:search_possibles') THEN

		INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
			VALUES (module_id, '', 0, 'Propospdos:search_possibles', 0, 0);

		exportcsv_aco_id := (SELECT id FROM acos WHERE alias = 'Propospdos:search_possibles');

		FOR v_row IN
			SELECT * FROM aros_acos rc
			JOIN acos c ON rc.aco_id = c.id
			WHERE c.alias = 'Propospdos:exportcsv_possibles'

		LOOP

			INSERT INTO aros_acos (aro_id, aco_id, _create, _read, _update, _delete)
				VALUES (v_row.aro_id, exportcsv_aco_id, v_row._create, v_row._read, v_row._update, v_row._delete);

		END LOOP;

    END IF;

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_propospdos();
DROP FUNCTION copy_permission_propospdos();

--------------------------------------------------------------------------------
-- Cohortesreferents93
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Cohortesreferents93';

CREATE OR REPLACE FUNCTION copy_permission_cohortesreferents93() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:PersonnesReferents');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'PersonnesReferents:cohorte_affectation93'
		WHERE alias = 'Cohortesreferents93:affecter';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'PersonnesReferents:exportcsv_affectation93'
		WHERE alias = 'Cohortesreferents93:exportcsv';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_cohortesreferents93();
DROP FUNCTION copy_permission_cohortesreferents93();


--------------------------------------------------------------------------------
-- Cohortestransfertspdvs93:atransferer
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION copy_permission_cohortestransfertspdvs93() RETURNS void AS
$$
DECLARE
	v_row record;
	module_id integer;

BEGIN

    IF NOT EXISTS(SELECT * FROM acos
		WHERE alias = 'Module:Transfertspdvs93') THEN

        INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
			VALUES (0, '', 0, 'Module:Transfertspdvs93', 0, 0);

		module_id := (SELECT id FROM acos WHERE alias = 'Module:Transfertspdvs93');

		FOR v_row IN
			SELECT * FROM aros_acos rc
			JOIN acos c ON rc.aco_id = c.id
			WHERE c.alias = 'Cohortestransfertspdvs93:atransferer'

		LOOP

			INSERT INTO aros_acos (aro_id, aco_id, _create, _read, _update, _delete)
				VALUES (v_row.aro_id, module_id, v_row._create, v_row._read, v_row._update, v_row._delete);

		END LOOP;
    END IF;

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Transfertspdvs93');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Transfertspdvs93:cohorte_atransferer'
		WHERE alias = 'Cohortestransfertspdvs93:atransferer';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_cohortestransfertspdvs93();
DROP FUNCTION copy_permission_cohortestransfertspdvs93();


--------------------------------------------------------------------------------
-- Orientsstructs
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteres';

CREATE OR REPLACE FUNCTION copy_permission_orientsstructs() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Orientsstructs');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Orientsstructs:exportcsv'
		WHERE alias = 'Criteres:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Orientsstructs:search'
		WHERE alias = 'Criteres:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_orientsstructs();
DROP FUNCTION copy_permission_orientsstructs();


--------------------------------------------------------------------------------
-- Criteresci
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteresci';

CREATE OR REPLACE FUNCTION copy_permission_criteresci() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Contratsinsertion');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Contratsinsertion:exportcsv'
		WHERE alias = 'Criteresci:index';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Contratsinsertion:search'
		WHERE alias = 'Criteresci:exportcsv';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_criteresci();
DROP FUNCTION copy_permission_criteresci();


--------------------------------------------------------------------------------
-- Cuis
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criterescuis';

CREATE OR REPLACE FUNCTION copy_permission_cuis() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Cuis');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Cuis:exportcsv'
		WHERE alias = 'Criterescuis:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Cuis:search'
		WHERE alias = 'Criterescuis:search';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_cuis();
DROP FUNCTION copy_permission_cuis();


--------------------------------------------------------------------------------
-- Cuis
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteresentretiens';

CREATE OR REPLACE FUNCTION copy_permission_entretiens() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Entretiens');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Entretiens:exportcsv'
		WHERE alias = 'Criteresentretiens:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Entretiens:search'
		WHERE alias = 'Criteresentretiens:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_entretiens();
DROP FUNCTION copy_permission_entretiens();


--------------------------------------------------------------------------------
-- Rendezvous
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteresrdv';

CREATE OR REPLACE FUNCTION copy_permission_rendezvous() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Rendezvous');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Rendezvous:exportcsv'
		WHERE alias = 'Criteresrdv:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Rendezvous:search'
		WHERE alias = 'Criteresrdv:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_rendezvous();
DROP FUNCTION copy_permission_rendezvous();


--------------------------------------------------------------------------------
-- Dossiers
--------------------------------------------------------------------------------

UPDATE acos
	SET alias = 'Dossiers:search',
 		parent_id = (SELECT id FROM acos WHERE alias = 'Module:Dossiers')
	WHERE alias = 'Dossiers:index';

--------------------------------------------------------------------------------
-- DSPs
--------------------------------------------------------------------------------

UPDATE acos
	SET alias = 'Dsps:search'
	WHERE alias = 'Dsps:index';


--------------------------------------------------------------------------------
-- Sanctionseps58
--------------------------------------------------------------------------------

UPDATE acos
	SET alias = 'Sanctionseps58:cohorte_noninscritspe'
	WHERE alias = 'Sanctionseps58:selectionnoninscrits';

UPDATE acos
	SET alias = 'Sanctionseps58:cohorte_radiespe'
	WHERE alias = 'Sanctionseps58:selectionradies';

INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
	SELECT parent_id, model, foreign_key, 'Sanctionseps58:exportcsv_radiespe', 0, 0
		FROM acos
		WHERE alias = 'Sanctionseps58:exportcsv'
		LIMIT 1;

UPDATE acos
	SET alias = 'Sanctionseps58:exportcsv_noninscritspe'
	WHERE alias = 'Sanctionseps58:exportcsv';


--------------------------------------------------------------------------------
-- Rendezvous
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criterestransfertspdvs93';

CREATE OR REPLACE FUNCTION copy_permission_transfertspdvs93() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Transfertspdvs93');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Transfertspdvs93:exportcsv'
		WHERE alias = 'Criterestransfertspdvs93:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Transfertspdvs93:search'
		WHERE alias = 'Criterestransfertspdvs93:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_transfertspdvs93();
DROP FUNCTION copy_permission_transfertspdvs93();


--------------------------------------------------------------------------------
-- Indicateurssuivis
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION copy_permission_indicateurssuivis() RETURNS void AS
$$
DECLARE
	module_id integer;

BEGIN

	module_id := (SELECT id FROM acos WHERE alias =  'Module:Indicateurssuivis');

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Indicateurssuivis:exportcsv_search'
		WHERE alias = 'Indicateurssuivis:exportcsv';

	UPDATE acos
		SET parent_id = module_id,
			alias = 'Indicateurssuivis:search'
		WHERE alias = 'Indicateurssuivis:index';

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_indicateurssuivis();
DROP FUNCTION copy_permission_indicateurssuivis();


--------------------------------------------------------------------------------
-- Apre
--------------------------------------------------------------------------------

DELETE FROM acos WHERE alias = 'Module:Criteresapres';

CREATE OR REPLACE FUNCTION copy_permission_apres() RETURNS void AS
$$
DECLARE
	v_row record;
	module_id integer;
	exportcsv_aco_id integer;

BEGIN

		module_id := (SELECT id FROM acos WHERE alias =  'Module:Apres');

		UPDATE acos
			SET parent_id = module_id,
				alias = 'Apres:search'
			WHERE alias = 'Criteresapres:all';

		UPDATE acos
			SET parent_id = module_id,
				alias = 'Apres:search_eligibilite'
			WHERE alias = 'Criteresapres:eligible';

		UPDATE acos
			SET parent_id = module_id,
				alias = 'Apres:exportcsv'
			WHERE alias = 'Criteresapres:exportcsv';

	IF NOT EXISTS(SELECT * FROM acos
		WHERE alias = 'Apres:exportcsv_eligibilite') THEN

		INSERT INTO acos (parent_id, model, foreign_key, alias, lft, rght)
			VALUES (module_id, '', 0, 'Apres:exportcsv_eligibilite', 0, 0);

		exportcsv_aco_id := (SELECT id FROM acos WHERE alias = 'Apres:exportcsv_eligibilite');

		FOR v_row IN
			SELECT * FROM aros_acos rc
			JOIN acos c ON rc.aco_id = c.id
			WHERE c.alias = 'Apres:exportcsv'

		LOOP

			INSERT INTO aros_acos (aro_id, aco_id, _create, _read, _update, _delete)
				VALUES (v_row.aro_id, exportcsv_aco_id, v_row._create, v_row._read, v_row._update, v_row._delete);

		END LOOP;

    END IF;

END;
$$
LANGUAGE 'plpgsql';

SELECT copy_permission_apres();
DROP FUNCTION copy_permission_apres();

-- 20170220
DELETE FROM acos WHERE alias = 'Gestionseps:index';
DELETE FROM acos WHERE alias = 'Module:Gestionseps';

-- 20170222
DELETE FROM acos WHERE alias = 'Pdos:index';
DELETE FROM acos WHERE alias = 'Module:Pdos';

UPDATE acos SET alias = 'Typesnotifspdos::delete' WHERE alias = 'Typesnotifspdos::deleteparametrage';

-- 20170227
DELETE FROM acos WHERE alias = 'Courrierspcgs66:index';
DELETE FROM acos WHERE alias = 'Module:Courrierspcgs66';

-- 20170228
DELETE FROM acos WHERE alias = 'Gestionsrdvs:index';
DELETE FROM acos WHERE alias = 'Module:Gestionsrdvs';

-- 20170301
UPDATE acos SET alias = 'Decisionsdossierspcgs66:view' WHERE alias = 'Decisionsdossierspcgs66:index';

-- 20170322
DELETE FROM acos WHERE alias = 'Gestionsdsps:index';
DELETE FROM acos WHERE alias = 'Module:Gestionsdsps';

DELETE FROM acos WHERE alias = 'Apres:indexparams';
DELETE FROM acos WHERE alias = 'Apres66:indexparams';

-- 20170323
DELETE FROM acos WHERE alias = 'Cers93:indexparams';

-- 20170327
DELETE FROM acos WHERE alias = 'Tags:indexparams';
DELETE FROM acos WHERE alias = 'Requestsmanager:indexparams';
DELETE FROM acos WHERE alias = 'Primoanalyses:indexparams';
DELETE FROM acos WHERE alias = 'Fichedeliaisons:indexparams';
DELETE FROM acos WHERE alias = 'Dashboards:indexparams';
DELETE FROM acos WHERE alias = 'Cuis66:indexparams';
DELETE FROM acos WHERE alias = 'ActionscandidatsPersonnes:indexparams';

DELETE FROM acos WHERE alias = 'Module:Tauxcgscuis';
DELETE FROM acos WHERE alias LIKE 'Tauxcgscuis:%';

DELETE FROM acos WHERE alias = 'Module:Prechargements';
DELETE FROM acos WHERE alias LIKE 'Prechargements:%';

--------------------------------------------------------------------------------
-- Ajout de la fonction cakephp_validate_phone
-- ( string|array $check , string $regex = null , string $country = 'all' )
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION cakephp_validate_phone( p_phone text, p_regex text, p_country text ) RETURNS boolean AS
$$
	BEGIN
		RETURN ( p_phone IS NULL )
			OR(
				(
					( p_country IS NULL OR p_country IN ( 'all', 'can', 'us' ) )
					AND p_phone ~ E'^(?:\\+?1)?[-. ]?\\(?[2-9][0-8][0-9]\\)?[-. ]?[2-9][0-9]{2}[-. ]?[0-9]{4}$'
				)
				OR
				(
					( p_country IN ( 'all', 'fr' ) )
					AND p_phone ~ E'^(0[1-9][0-9]{8}|1[0-9]{1,3}|11[0-9]{4}|3[0-9]{3})$'
				)
				OR
				(
					( p_regex IS NOT NULL )
					AND p_phone ~ p_regex
				)
			);
	END;
$$ LANGUAGE 'plpgsql' IMMUTABLE;

COMMENT ON FUNCTION cakephp_validate_phone( p_phone text, p_regex text, p_country text ) IS
	E'@see http://api.cakephp.org/2.2/class-Validation.html#_phone\nCustom country France (fr) added.';

-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION cakephp_validate_phone( p_phone text, p_regex text ) RETURNS boolean AS
$$
	BEGIN
		RETURN cakephp_validate_phone( p_phone, p_regex, 'all' );
	END;
$$
LANGUAGE 'plpgsql' IMMUTABLE;

-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION cakephp_validate_phone( p_phone text ) RETURNS boolean AS
$$
	BEGIN
		RETURN cakephp_validate_phone( p_phone, null, 'all' );
	END;
$$
LANGUAGE 'plpgsql' IMMUTABLE;

--==============================================================================
-- Transformation des contraintes TYPE_ par des contraintes cakephp_validate_in_list
-- dans les tables métier (ne venant pas de la CNAF), ajout de certaines règles
-- de validation en base de données (plutôt que dans le modèle).
--==============================================================================

-- Suppression des vues du CD 66 qui utilisent les tables dsps et dsps_revs
DROP VIEW IF EXISTS public.dps;
DROP VIEW IF EXISTS public.dps_rev;

-- accscreaentr
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'accscreaentr' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'accscreaentr' );
ALTER TABLE accscreaentr ALTER COLUMN microcredit SET DEFAULT NULL;
ALTER TABLE accscreaentr ALTER COLUMN nacre SET DEFAULT NULL;

-- actionscandidats
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'actionscandidats' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'actionscandidats' );
ALTER TABLE actionscandidats ALTER COLUMN contractualisation SET DEFAULT NULL;
ALTER TABLE actionscandidats ALTER COLUMN typeaction SET DEFAULT NULL;

-- actionscandidats_personnes
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'actionscandidats_personnes' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'actionscandidats_personnes' );
ALTER TABLE actionscandidats_personnes ALTER COLUMN bilanrecu SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN bilanretenu SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN bilanvenu SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN enattente SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN integrationaction SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN issortie SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN mobile SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN naturemobile SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN pieceallocataire SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN positionfiche SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN poursuitesuivicg SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN presencecontrat SET DEFAULT NULL;
ALTER TABLE actionscandidats_personnes ALTER COLUMN rendezvouspartenaire SET DEFAULT NULL;

-- actsprofs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'actsprofs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'actsprofs' );
ALTER TABLE actsprofs ALTER COLUMN typecontratact SET DEFAULT NULL;

-- adresses
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'adresses' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'adresses' );

-- aidesapres66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'aidesapres66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'aidesapres66' );
ALTER TABLE aidesapres66 ALTER COLUMN autorisationvers SET DEFAULT NULL;
ALTER TABLE aidesapres66 ALTER COLUMN decisionapre SET DEFAULT NULL;
ALTER TABLE aidesapres66 ALTER COLUMN versement SET DEFAULT NULL;
ALTER TABLE aidesapres66 ALTER COLUMN virement SET DEFAULT NULL;

-- allocationssoutienfamilial
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'allocationssoutienfamilial' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'allocationssoutienfamilial' );

-- amenagslogts
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'amenagslogts' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'amenagslogts' );
ALTER TABLE amenagslogts ALTER COLUMN typeaidelogement SET DEFAULT NULL;

-- apres
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'apres' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'apres' );
ALTER TABLE apres ALTER COLUMN activitebeneficiaire SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN eligibiliteapre SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN etatdossierapre SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN hascer SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN hasfrais SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN isbeneficiaire SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN justificatif SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN naturelogement SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN respectdelais SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN typecontrat SET DEFAULT NULL;
ALTER TABLE apres ALTER COLUMN typedemandeapre SET DEFAULT NULL;

-- apres_comitesapres
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'apres_comitesapres' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'apres_comitesapres' );
ALTER TABLE apres_comitesapres ALTER COLUMN decisioncomite SET DEFAULT NULL;
ALTER TABLE apres_comitesapres ALTER COLUMN recoursapre SET DEFAULT NULL;

-- autresavisradiation
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'autresavisradiation' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'autresavisradiation' );
ALTER TABLE autresavisradiation ALTER COLUMN autreavisradiation SET DEFAULT NULL;

-- autresavissuspension
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'autresavissuspension' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'autresavissuspension' );
ALTER TABLE autresavissuspension ALTER COLUMN autreavissuspension SET DEFAULT NULL;

-- aviscgssdompersonnes
ALTER TABLE aviscgssdompersonnes ALTER COLUMN resujusactdom SET DEFAULT NULL;

-- avisfichedeliaisons
ALTER TABLE avisfichedeliaisons ALTER COLUMN etape SET DEFAULT NULL;

-- avisprimoanalyses
ALTER TABLE avisprimoanalyses ALTER COLUMN etape SET DEFAULT NULL;

-- bilansparcours66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'bilansparcours66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'bilansparcours66' );
ALTER TABLE bilansparcours66 ALTER COLUMN accordprojet SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN bilanparcoursinsertion SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN changementref SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN changementrefeplocale SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN changementrefparcours SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN changementrefsansep SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN choixparcours SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN choixsanspassageep SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN decisioncga SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN decisioncommission SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN decisioncoordonnateur SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN examenaudition SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN examenauditionpe SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN maintienorientavisep SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN maintienorientparcours SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN maintienorientsansep SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN motifep SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN positionbilan SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN reorientation SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN reorientationeplocale SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN sitfam SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN typeeplocale SET DEFAULT NULL;
ALTER TABLE bilansparcours66 ALTER COLUMN typeformulaire SET DEFAULT NULL;

-- calculsdroitsrsa
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'calculsdroitsrsa' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'calculsdroitsrsa' );
ALTER TABLE calculsdroitsrsa ALTER COLUMN toppersentdrodevorsa SET DEFAULT NULL;

-- comitesapres_participantscomites
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'comitesapres_participantscomites' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'comitesapres_participantscomites' );
ALTER TABLE comitesapres_participantscomites ALTER COLUMN presence SET DEFAULT NULL;

-- commissionseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'commissionseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'commissionseps' );

-- commissionseps_membreseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'commissionseps_membreseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'commissionseps_membreseps' );
ALTER TABLE commissionseps_membreseps ALTER COLUMN presence SET DEFAULT NULL;

-- composfoyerspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'composfoyerspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'composfoyerspcgs66' );

-- compositionsregroupementseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'compositionsregroupementseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'compositionsregroupementseps' );

-- conditionsactivitesprealables
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'conditionsactivitesprealables' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'conditionsactivitesprealables' );

-- contratsinsertion
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'contratsinsertion' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'contratsinsertion' );
ALTER TABLE contratsinsertion ALTER COLUMN autreavisradiation SET DEFAULT NULL;
ALTER TABLE contratsinsertion ALTER COLUMN autreavissuspension SET DEFAULT NULL;
ALTER TABLE contratsinsertion ALTER COLUMN faitsuitea SET DEFAULT NULL;
ALTER TABLE contratsinsertion ALTER COLUMN num_contrat SET DEFAULT NULL;
ALTER TABLE contratsinsertion ALTER COLUMN type_demande SET DEFAULT NULL;
ALTER TABLE contratsinsertion ALTER COLUMN typeinsertion SET DEFAULT NULL;

-- covs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'covs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'covs58' );

-- cuis
ALTER TABLE cuis ALTER COLUMN decision_cui SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN formation SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN inscritpoleemploi SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN niveauformation SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN niveauqualif SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN organismepayeur SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN rsadepuis SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN sansemploi SET DEFAULT NULL;
ALTER TABLE cuis ALTER COLUMN typecontrat SET DEFAULT NULL;

-- cuis66
ALTER TABLE cuis66 ALTER COLUMN etatdossiercui66 SET DEFAULT NULL;
ALTER TABLE cuis66 ALTER COLUMN zonecouverte SET DEFAULT NULL;

-- decisionscontratscomplexeseps93
SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscontratscomplexeseps93', 'decisionscontratscomplexeseps93_decision_observationdecision_chk' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'decisionscontratscomplexeseps93', 'decisionscontratscomplexeseps93_decision_observationdecision_ch' );
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionscontratscomplexeseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionscontratscomplexeseps93' );
ALTER TABLE decisionscontratscomplexeseps93 ADD CONSTRAINT decisionscontratscomplexeseps93_decision_observationdecision_chk CHECK ( observationdecision IS NULL OR decision IN ( 'valide', 'rejete' ) );
ALTER TABLE decisionscontratscomplexeseps93 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionsdefautsinsertionseps66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsdefautsinsertionseps66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsdefautsinsertionseps66' );
ALTER TABLE decisionsdefautsinsertionseps66 ALTER COLUMN decision SET DEFAULT NULL;
ALTER TABLE decisionsdefautsinsertionseps66 ALTER COLUMN decisionsup SET DEFAULT NULL;

-- decisionsdossierspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsdossierspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsdossierspcgs66' );
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN avistechnique SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN defautinsertion SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN etatdossierpcg SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN etatop SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN instrencours SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN phase SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN recidive SET DEFAULT NULL;
ALTER TABLE decisionsdossierspcgs66 ALTER COLUMN validationproposition SET DEFAULT NULL;

-- decisionsnonorientationsproseps58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsnonorientationsproseps58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsnonorientationsproseps58' );
ALTER TABLE decisionsnonorientationsproseps58 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionsnonorientationsproseps66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsnonorientationsproseps66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsnonorientationsproseps66' );
ALTER TABLE decisionsnonorientationsproseps66 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionsnonorientationsproseps93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsnonorientationsproseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsnonorientationsproseps93' );
ALTER TABLE decisionsnonorientationsproseps93 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionsnonrespectssanctionseps93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsnonrespectssanctionseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsnonrespectssanctionseps93' );
ALTER TABLE decisionsnonrespectssanctionseps93 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionspcgs66' );

-- decisionspdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionspdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionspdos' );

-- decisionsproposcontratsinsertioncovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsproposcontratsinsertioncovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsproposcontratsinsertioncovs58' );

-- decisionsproposnonorientationsproscovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsproposnonorientationsproscovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsproposnonorientationsproscovs58' );

-- decisionsproposorientationscovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsproposorientationscovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsproposorientationscovs58' );

-- decisionsproposorientssocialescovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsproposorientssocialescovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsproposorientssocialescovs58' );

-- decisionspropospdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionspropospdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionspropospdos' );
ALTER TABLE decisionspropospdos ALTER COLUMN accordepaudition SET DEFAULT NULL;
ALTER TABLE decisionspropospdos ALTER COLUMN avistechnique SET DEFAULT NULL;
ALTER TABLE decisionspropospdos ALTER COLUMN decisionreponseep SET DEFAULT NULL;
ALTER TABLE decisionspropospdos ALTER COLUMN etatdossierpdo SET DEFAULT NULL;
ALTER TABLE decisionspropospdos ALTER COLUMN hasreponseep SET DEFAULT NULL;
ALTER TABLE decisionspropospdos ALTER COLUMN isvalidation SET DEFAULT NULL;
ALTER TABLE decisionspropospdos ALTER COLUMN validationdecision SET DEFAULT NULL;

-- decisionsregressionsorientationseps58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsregressionsorientationseps58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsregressionsorientationseps58' );
ALTER TABLE decisionsregressionsorientationseps58 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionsreorientationseps93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionsreorientationseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionsreorientationseps93' );
ALTER TABLE decisionsreorientationseps93 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionssaisinesbilansparcourseps66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionssaisinesbilansparcourseps66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionssaisinesbilansparcourseps66' );
ALTER TABLE decisionssaisinesbilansparcourseps66 ALTER COLUMN changementrefparcours SET DEFAULT NULL;
ALTER TABLE decisionssaisinesbilansparcourseps66 ALTER COLUMN decision SET DEFAULT NULL;
ALTER TABLE decisionssaisinesbilansparcourseps66 ALTER COLUMN maintienorientparcours SET DEFAULT NULL;
ALTER TABLE decisionssaisinesbilansparcourseps66 ALTER COLUMN reorientation SET DEFAULT NULL;

-- decisionssaisinespdoseps66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionssaisinespdoseps66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionssaisinespdoseps66' );
ALTER TABLE decisionssaisinespdoseps66 ALTER COLUMN decision SET DEFAULT NULL;
ALTER TABLE decisionssaisinespdoseps66 ALTER COLUMN nonadmis SET DEFAULT NULL;

-- decisionssanctionseps58
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionssanctionseps58', 'decisionssanctionseps58_arretsanction_datearretsanction_chk' );
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionssanctionseps58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionssanctionseps58' );
ALTER TABLE decisionssanctionseps58
	ADD CONSTRAINT decisionssanctionseps58_arretsanction_datearretsanction_chk
	CHECK (
		(
			arretsanction IN ( 'finsanction1', 'finsanction2', 'annulation1', 'annulation2' )
			AND datearretsanction IS NOT NULL
		)
		OR (
			arretsanction NOT IN ( 'finsanction1', 'finsanction2', 'annulation1', 'annulation2' )
			AND datearretsanction IS NULL
		)
	);

-- decisionssanctionsrendezvouseps58
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionssanctionsrendezvouseps58', 'decisionssanctionsrendezvouseps58_arretsanction_datearretsanction_chk' );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'decisionssanctionsrendezvouseps58', 'decisionssanctionsrendezvouseps58_arretsanction_datearretsancti' );
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionssanctionsrendezvouseps58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionssanctionsrendezvouseps58' );
ALTER TABLE decisionssanctionsrendezvouseps58
	ADD CONSTRAINT decisionssanctionsrendezvouseps58_arretsanction_datearretsanction_chk
	CHECK (
		(
			arretsanction IN ( 'finsanction1', 'finsanction2', 'annulation1', 'annulation2' )
			AND datearretsanction IS NOT NULL
		)
		OR (
			arretsanction NOT IN ( 'finsanction1', 'finsanction2', 'annulation1', 'annulation2' )
			AND datearretsanction IS NULL
		)
	);

-- decisionssignalementseps93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionssignalementseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionssignalementseps93' );
ALTER TABLE decisionssignalementseps93 ALTER COLUMN decision SET DEFAULT NULL;

-- decisionstraitementspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'decisionstraitementspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'decisionstraitementspcgs66' );

-- defautsinsertionseps66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'defautsinsertionseps66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'defautsinsertionseps66' );
ALTER TABLE defautsinsertionseps66 ALTER COLUMN type SET DEFAULT NULL;

-- descriptionspdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'descriptionspdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'descriptionspdos' );

-- destinatairesemails
ALTER TABLE destinatairesemails ALTER COLUMN type SET DEFAULT NULL;

-- detailsaccosocfams
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsaccosocfams' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsaccosocfams' );

-- detailsaccosocfams_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsaccosocfams_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsaccosocfams_revs' );

-- detailsaccosocindis
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsaccosocindis' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsaccosocindis' );

-- detailsaccosocindis_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsaccosocindis_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsaccosocindis_revs' );

-- detailsconforts
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsconforts' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsconforts' );

-- detailsconforts_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsconforts_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsconforts_revs' );

-- detailsdifdisps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdifdisps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdifdisps' );

-- detailsdifdisps_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdifdisps_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdifdisps_revs' );

-- detailsdiflogs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdiflogs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdiflogs' );

-- detailsdiflogs_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdiflogs_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdiflogs_revs' );

-- detailsdifsocs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdifsocs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdifsocs' );

-- detailsdifsocs_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdifsocs_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdifsocs_revs' );

-- detailsdifsocpros
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdifsocpros' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdifsocpros' );

-- detailsdifsocpros_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsdifsocpros_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsdifsocpros_revs' );

-- detailsfreinforms
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsfreinforms' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsfreinforms' );

-- detailsfreinforms_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsfreinforms_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsfreinforms_revs' );

-- detailsmoytrans
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsmoytrans' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsmoytrans' );

-- detailsmoytrans_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsmoytrans_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsmoytrans_revs' );

-- detailsnatmobs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsnatmobs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsnatmobs' );

-- detailsnatmobs_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsnatmobs_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsnatmobs_revs' );

-- detailsprojpros
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsprojpros' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsprojpros' );

-- detailsprojpros_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'detailsprojpros_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'detailsprojpros_revs' );

-- diplomescers93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'diplomescers93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'diplomescers93' );

-- dossiers
ALTER TABLE dossiers ALTER COLUMN fonorg SET DEFAULT NULL;
ALTER TABLE dossiers ALTER COLUMN fonorgcedmut SET DEFAULT NULL;
ALTER TABLE dossiers ALTER COLUMN fonorgprenmut SET DEFAULT NULL;
ALTER TABLE dossiers ALTER COLUMN statudemrsa SET DEFAULT NULL;

-- dossierscovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'dossierscovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'dossierscovs58' );
ALTER TABLE dossierscovs58 ALTER COLUMN themecov58 SET DEFAULT NULL;

-- dossierseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'dossierseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'dossierseps' );

-- dossierspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'dossierspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'dossierspcgs66' );
ALTER TABLE dossierspcgs66 ALTER COLUMN iscomplet SET DEFAULT NULL;
ALTER TABLE dossierspcgs66 ALTER COLUMN etatdossierpcg SET DEFAULT NULL;
ALTER TABLE dossierspcgs66 ALTER COLUMN orgpayeur SET DEFAULT NULL;

-- dsps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'dsps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'dsps' );
ALTER TABLE dsps ALTER COLUMN accoemploi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN accosocfam SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN accosocindi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN cessderact SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN concoformqualiemploi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN demarlog SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN drorsarmianta2 SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN duractdomi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN hispro SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN inscdememploi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN natlog SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN nivdipmaxobt SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN nivetu SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN sitpersdemrsa SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN soutdemarsoc SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN statutoccupation SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN suivimedical SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topautrpermicondu SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topcompeextrapro SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topcouvsoc SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topcreareprientre SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topdomideract SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topdrorsarmiant SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topengdemarechemploi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topisogroouenf SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topisogrorechemploi SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topmoyloco SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN toppermicondub SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topprojpro SET DEFAULT NULL;
ALTER TABLE dsps ALTER COLUMN topqualipro SET DEFAULT NULL;

-- dsps_revs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'dsps_revs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'dsps_revs' );
ALTER TABLE dsps_revs ALTER COLUMN accoemploi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN accosocfam SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN accosocindi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN cessderact SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN concoformqualiemploi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN demarlog SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN drorsarmianta2 SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN duractdomi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN hispro SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN inscdememploi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN natlog SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN nivdipmaxobt SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN nivetu SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN sitpersdemrsa SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN soutdemarsoc SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN statutoccupation SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN suivimedical SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topautrpermicondu SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topcompeextrapro SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topcouvsoc SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topcreareprientre SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topdomideract SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topdrorsarmiant SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topengdemarechemploi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topisogroouenf SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topisogrorechemploi SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topmoyloco SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN toppermicondub SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topprojpro SET DEFAULT NULL;
ALTER TABLE dsps_revs ALTER COLUMN topqualipro SET DEFAULT NULL;

-- entretiens
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'entretiens' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'entretiens' );
ALTER TABLE entretiens ALTER COLUMN rendezvousprevu SET DEFAULT NULL;
ALTER TABLE entretiens ALTER COLUMN typeentretien SET DEFAULT NULL;

-- etatsliquidatifs
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'etatsliquidatifs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'etatsliquidatifs' );

-- expsproscers93
ALTER TABLE expsproscers93 ALTER COLUMN typeduree SET DEFAULT NULL;

-- fichedeliaisons
ALTER TABLE fichedeliaisons ALTER COLUMN etat SET DEFAULT NULL;

-- fichierstraitementspdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'fichierstraitementspdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'fichierstraitementspdos' );

-- historiqueetatspe
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'historiqueetatspe' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'historiqueetatspe' );

-- immersionscuis66
ALTER TABLE immersionscuis66 ALTER COLUMN objectifprincipal SET DEFAULT NULL;

-- infosagricoles
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'infosagricoles' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'infosagricoles' );

-- manifestationsbilansparcours66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'manifestationsbilansparcours66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'manifestationsbilansparcours66' );

-- membreseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'membreseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'membreseps' );

-- memos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'memos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'memos' );

-- modelestypescourrierspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'modelestypescourrierspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'modelestypescourrierspcgs66' );

-- nonorientes66
SELECT alter_table_drop_constraint_if_exists ( 'public', 'nonorientes66', 'nonorientees66_dateimpression_origine_chk' );
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'nonorientes66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'nonorientes66' );
ALTER TABLE nonorientes66 ADD CONSTRAINT nonorientees66_dateimpression_origine_chk CHECK ( ( origine = 'isemploi' AND historiqueetatpe_id IS NOT NULL  AND dateimpression IS NULL) OR ( origine = 'notisemploi' AND historiqueetatpe_id IS NULL AND dateimpression IS NOT NULL ) );
ALTER TABLE nonorientes66 ALTER COLUMN reponseallocataire SET DEFAULT NULL;

-- nonrespectssanctionseps93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'nonrespectssanctionseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'nonrespectssanctionseps93' );
ALTER TABLE nonrespectssanctionseps93 ALTER COLUMN sortieprocedure SET DEFAULT NULL;

-- oldaccompagnementscuis66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'oldaccompagnementscuis66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'oldaccompagnementscuis66' );

-- orientsstructs
ALTER TABLE orientsstructs ALTER COLUMN origine SET DEFAULT NULL;
ALTER TABLE orientsstructs ALTER COLUMN statut_orient SET DEFAULT NULL;

-- originespdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'originespdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'originespdos' );

-- parcours
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'parcours' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'parcours' );

-- partenaires
ALTER TABLE partenaires ALTER COLUMN orgrecouvcotis SET DEFAULT NULL;
ALTER TABLE partenaires ALTER COLUMN statut SET DEFAULT NULL;

-- partenairescuis
ALTER TABLE partenairescuis ALTER COLUMN organismerecouvrement SET DEFAULT NULL;

-- passagescommissionseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'passagescommissionseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'passagescommissionseps' );

-- passagescovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'passagescovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'passagescovs58' );

-- permanences
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'permanences' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'permanences' );

-- personnes
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'personnes' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'personnes' );

-- personnes_referents
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'personnes_referents' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'personnes_referents' );

-- piecesmodelestypescourrierspcgs66
DROP INDEX IF EXISTS piecesmodelestypescourrierspcgs66_isautrepiece_modeletypecourrierpcg66_id_idx;
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'piecesmodelestypescourrierspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'piecesmodelestypescourrierspcgs66' );
CREATE UNIQUE INDEX piecesmodelestypescourrierspcgs66_isautrepiece_modeletypecourrierpcg66_id_idx ON piecesmodelestypescourrierspcgs66 (isautrepiece, modeletypecourrierpcg66_id) WHERE isautrepiece = '1';

-- primoanalyses
ALTER TABLE primoanalyses ALTER COLUMN etat SET DEFAULT NULL;

-- proposcontratsinsertioncovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'proposcontratsinsertioncovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'proposcontratsinsertioncovs58' );

-- proposdecisionscers66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'proposdecisionscers66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'proposdecisionscers66' );
ALTER TABLE proposdecisionscers66 ALTER COLUMN decisionfinale SET DEFAULT NULL;
ALTER TABLE proposdecisionscers66 ALTER COLUMN nonvalidationparticulier SET DEFAULT NULL;

-- proposdecisionscuis66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'proposdecisionscuis66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'proposdecisionscuis66' );
ALTER TABLE proposdecisionscuis66 ALTER COLUMN propositioncui SET DEFAULT NULL;
ALTER TABLE proposdecisionscuis66 ALTER COLUMN propositioncuielu SET DEFAULT NULL;
ALTER TABLE proposdecisionscuis66 ALTER COLUMN propositioncuireferent SET DEFAULT NULL;

-- propospdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'propospdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'propospdos' );
ALTER TABLE propospdos ALTER COLUMN autres SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN choixpdo SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN decision SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN decisionop SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN etatdossierpdo SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN iscomplet SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN isdecisionop SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN nonadmis SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN orgpayeur SET DEFAULT NULL;
ALTER TABLE propospdos ALTER COLUMN suivi SET DEFAULT NULL;

-- questionspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'questionspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'questionspcgs66' );

-- referents
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'referents' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'referents' );
UPDATE referents SET numero_poste = REPLACE(REPLACE(REPLACE(numero_poste, '-', ''), '.', ''), ' ', '');
UPDATE referents SET numero_poste = NULL WHERE numero_poste = '0000000000' OR numero_poste = '00000000000' OR numero_poste LIKE '%123456789%';
SELECT alter_table_drop_constraint_if_exists ( 'public', 'referents', 'referents_numero_poste_phone_fr_chk' );
ALTER TABLE referents ADD CONSTRAINT referents_numero_poste_phone_fr_chk CHECK (cakephp_validate_phone(numero_poste, NULL, 'fr'));

-- regroupementseps
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'regroupementseps' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'regroupementseps' );
ALTER TABLE regroupementseps ALTER COLUMN contratcomplexeep93 SET NOT NULL;

-- relancesapres
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'relancesapres' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'relancesapres' );
ALTER TABLE relancesapres ALTER COLUMN etatdossierapre SET DEFAULT NULL;

-- rendezvous
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'rendezvous' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'rendezvous' );

-- reorientationseps93
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'reorientationseps93' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'reorientationseps93' );

-- saisinesbilansparcourseps66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'saisinesbilansparcourseps66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'saisinesbilansparcourseps66' );
ALTER TABLE saisinesbilansparcourseps66 ALTER COLUMN changementrefparcours SET DEFAULT NULL;
ALTER TABLE saisinesbilansparcourseps66 ALTER COLUMN choixparcours SET DEFAULT NULL;
ALTER TABLE saisinesbilansparcourseps66 ALTER COLUMN maintienorientparcours SET DEFAULT NULL;
ALTER TABLE saisinesbilansparcourseps66 ALTER COLUMN reorientation SET DEFAULT NULL;

-- sanctionseps58
SELECT alter_table_drop_constraint_if_exists ( 'public', 'sanctionseps58', 'sanctionseps58_historiqueetatpe_id_origine_chk' );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'sanctionseps58', 'sanctionseps58_valid_entry_check' );
SELECT alter_table_drop_constraint_if_exists ( 'public', 'sanctionseps58', 'sanctionseps58_valid_entry_chk' );
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'sanctionseps58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'sanctionseps58' );
ALTER TABLE sanctionseps58 ADD CONSTRAINT sanctionseps58_historiqueetatpe_id_origine_chk CHECK ( ( origine = 'radiepe' AND historiqueetatpe_id IS NOT NULL ) OR ( origine <> 'radiepe' AND historiqueetatpe_id IS NULL ) );
ALTER TABLE sanctionseps58 ADD CONSTRAINT sanctionseps58_valid_entry_chk CHECK(
	( contratinsertion_id IS NULL AND origine <> 'nonrespectcer' ) OR ( contratinsertion_id IS NOT NULL AND origine = 'nonrespectcer' )
);

-- situationspdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'situationspdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'situationspdos' );

-- statutsrdvs
ALTER TABLE statutsrdvs ALTER COLUMN libelle SET NOT NULL;
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'statutsrdvs' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'statutsrdvs' );

-- statutsrdvs_typesrdv
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'statutsrdvs_typesrdv' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'statutsrdvs_typesrdv' );

-- suivisappuisorientation
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'suivisappuisorientation' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'suivisappuisorientation' );

-- suspensionscuis66
ALTER TABLE suspensionscuis66 ALTER COLUMN duree SET DEFAULT NULL;

-- tableauxsuivispdvs93
ALTER TABLE tableauxsuivispdvs93 ALTER COLUMN type SET DEFAULT NULL;

-- themescovs58
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'themescovs58' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'themescovs58' );
ALTER TABLE themescovs58 ALTER COLUMN nonorientationprocov58 SET DEFAULT NULL;
ALTER TABLE themescovs58 ALTER COLUMN propocontratinsertioncov58 SET DEFAULT NULL;
ALTER TABLE themescovs58 ALTER COLUMN propononorientationprocov58 SET DEFAULT NULL;
ALTER TABLE themescovs58 ALTER COLUMN propoorientationcov58 SET DEFAULT NULL;
ALTER TABLE themescovs58 ALTER COLUMN propoorientsocialecov58 SET DEFAULT NULL;
ALTER TABLE themescovs58 ALTER COLUMN regressionorientationcov58 SET DEFAULT NULL;

-- traitementspcgs66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'traitementspcgs66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'traitementspcgs66' );
ALTER TABLE traitementspcgs66 ALTER COLUMN aidesubvreint SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN dureeecheance SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN dureefinprisecompte SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN etattraitementpcg SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN recidive SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN regime SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN saisonnier SET DEFAULT NULL;
ALTER TABLE traitementspcgs66 ALTER COLUMN typetraitement SET DEFAULT NULL;

-- traitementspdos
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'traitementspdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'traitementspdos' );
ALTER TABLE traitementspdos ALTER COLUMN aidesubvreint SET DEFAULT NULL;
ALTER TABLE traitementspdos ALTER COLUMN dureedepart SET DEFAULT NULL;
ALTER TABLE traitementspdos ALTER COLUMN dureefinperiode SET DEFAULT NULL;
ALTER TABLE traitementspdos ALTER COLUMN regime SET DEFAULT NULL;
ALTER TABLE traitementspdos ALTER COLUMN saisonnier SET DEFAULT NULL;

-- typesaidesapres66
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'typesaidesapres66' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'typesaidesapres66' );
ALTER TABLE typesaidesapres66 ALTER COLUMN typeplafond SET DEFAULT NULL;

-- typesorients
ALTER TABLE typesorients ALTER COLUMN lib_type_orient SET NOT NULL;
ALTER TABLE typesorients ALTER COLUMN actif SET NOT NULL;

ALTER TABLE typesorients ALTER COLUMN modele_notif DROP NOT NULL;
ALTER TABLE typesorients ALTER COLUMN modele_notif_cohorte DROP NOT NULL;
ALTER TABLE typesorients ALTER COLUMN modele_notif SET DEFAULT NULL;
ALTER TABLE typesorients ALTER COLUMN modele_notif_cohorte SET DEFAULT NULL;

SELECT public.table_enumtypes_to_validate_in_list( 'public', 'typesorients' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'typesorients' );
-- Ajout de la clé étrangère manquante
SELECT add_missing_constraint ( 'public', 'typesorients', 'typesorients_parentid_fkey', 'typesorients', 'parentid' );

-- typespdos
ALTER TABLE typespdos ALTER COLUMN libelle SET NOT NULL;
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'typespdos' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'typespdos' );

-- users
SELECT public.table_enumtypes_to_validate_in_list( 'public', 'users' );
SELECT public.table_defaultvalues_enumtypes_to_varchar( 'public', 'users' );
ALTER TABLE users ALTER COLUMN sensibilite SET DEFAULT NULL;

-- @fixme -> champ non obligatoire ?
UPDATE users SET numtel = REPLACE(REPLACE(REPLACE(numtel, '-', ''), '.', ''), ' ', '');
UPDATE users SET numtel = NULL WHERE numtel LIKE '0000000000%' OR numtel LIKE '%123456789%';

SELECT alter_table_drop_constraint_if_exists ( 'public', 'users', 'users_numtel_phone_fr_chk' );
ALTER TABLE users ADD CONSTRAINT users_numtel_phone_fr_chk CHECK (cakephp_validate_phone(numtel, NULL, 'fr'));


/*******************************************************************************
 * Permet de rétablir les droits tel qu'ils ont été pensés pour cakephp
 *******************************************************************************/

SELECT alter_table_drop_constraint_if_exists ( 'public', 'acos', 'acos_alias_unique_chk' );
DROP INDEX IF EXISTS acos_alias_idx;
ALTER TABLE acos ALTER COLUMN parent_id DROP NOT NULL;
ALTER TABLE acos ALTER COLUMN foreign_key DROP NOT NULL;
ALTER TABLE acos ALTER COLUMN parent_id DROP DEFAULT;
ALTER TABLE acos ALTER COLUMN model DROP DEFAULT;
ALTER TABLE acos ALTER COLUMN foreign_key DROP DEFAULT;

UPDATE acos SET
	model = null,
	foreign_key = null,
	alias = substring(alias from position(':' in alias) +1 for char_length(alias) - position(':' in alias)),
	lft = lft +1,
	rght = rght +1;

INSERT INTO acos (alias, lft, rght) VALUES ('controllers', 1, (SELECT MAX(rght) +1 FROM acos));
UPDATE acos SET parent_id = (SELECT MAX(id) FROM acos) WHERE parent_id = 0;

UPDATE aros SET model = 'User' WHERE model = 'Utilisateur';
UPDATE aros SET parent_id = null WHERE parent_id = 0;

-- -----------------------------------------------------------------------------
-- 20161223: normalisation des alias des aros ainsi que de users.username et
-- groups.name, suppression des aros ne devant plus exister.
-- -----------------------------------------------------------------------------

UPDATE aros SET alias = TRIM( BOTH ' ' FROM alias );
UPDATE users SET username = TRIM( BOTH ' ' FROM username );
UPDATE groups SET name = TRIM( BOTH ' ' FROM name );

DELETE FROM aros
	WHERE (
		model NOT IN ( 'User', 'Group' )
		OR (
			model = 'User'
			AND NOT EXISTS(
				SELECT
						*
					FROM users
					WHERE
						users.id = foreign_key
						AND users.username = alias
			)
		)
		OR (
			model = 'Group'
			AND NOT EXISTS(
				SELECT
						*
					FROM groups
					WHERE
						groups.id = foreign_key
						AND groups.name = alias
			)
		)
	);

/**
 * ATTENTION : passez les shells suivant pour s'assurer une base propre
 * sudo -u www-data lib/Cake/Console/cake WebrsaSessionAcl update Aco
 * sudo -u www-data lib/Cake/Console/cake WebrsaSessionAcl update Aro
 *
 * <optionnel> Supprime les droits d'un utilisateur si il possède les mêmes droits que son parent
 * sudo -u www-data lib/Cake/Console/cake WebrsaSessionAcl forceHeritage
 */

-- 20170130: création d'un index unique sur le nom du groupe
DROP INDEX IF EXISTS groups_name_idx;
CREATE UNIQUE INDEX groups_name_idx ON groups (name);

-- 20170201: mise en place d'une réelle clé étrangère pour la colonne groups.parent_id
UPDATE groups SET parent_id = NULL WHERE parent_id = 0;
SELECT alter_table_drop_constraint_if_exists ( 'public', 'groups', 'groups_parent_id_fk' );
ALTER TABLE groups ADD CONSTRAINT groups_parent_id_fk FOREIGN KEY (parent_id) REFERENCES groups(id) ON DELETE CASCADE ON UPDATE CASCADE;

--------------------------------------------------------------------------------
-- 20170308: remplacement des abbréviations de typevoie par des libellés de types
-- de voie dans les tables métiers
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS correspondancestypevoie;
CREATE TABLE correspondancestypevoie (
    id          SERIAL NOT NULL PRIMARY KEY,
    typevoie	VARCHAR(4) NOT NULL,
    libtypevoie	VARCHAR(30) NOT NULL
);

INSERT INTO correspondancestypevoie ( typevoie, libtypevoie ) VALUES
	( 'ABE', 'Abbaye' ),
	( 'ACH', 'Ancien chemin' ),
	( 'AGL', 'Agglomération' ),
	( 'AIRE', 'Aire' ),
	( 'ALL', 'Allée' ),
	( 'ANSE', 'Anse' ),
	( 'ARC', 'Arcade' ),
	( 'ART', 'Ancienne route' ),
	( 'AUT', 'Autoroute' ),
	( 'AV', 'Avenue' ),
	( 'BAST', 'Bastion' ),
	( 'BCH', 'Bas chemin' ),
	( 'BCLE', 'Boucle' ),
	( 'BD', 'Boulevard' ),
	( 'BEGI', 'Béguinage' ),
	( 'BER', 'Berge' ),
	( 'BOIS', 'Bois' ),
	( 'BRE', 'Barriere' ),
	( 'BRG', 'Bourg' ),
	( 'BSTD', 'Bastide' ),
	( 'BUT', 'Butte' ),
	( 'CALE', 'Cale' ),
	( 'CAMP', 'Camp' ),
	( 'CAR', 'Carrefour' ),
	( 'CARE', 'Carriere' ),
	( 'CARR', 'Carre' ),
	( 'CAU', 'Carreau' ),
	( 'CAV', 'Cavée' ),
	( 'CGNE', 'Campagne' ),
	( 'CHE', 'Chemin' ),
	( 'CHEM', 'Cheminement' ),
	( 'CHEZ', 'Chez' ),
	( 'CHI', 'Charmille' ),
	( 'CHL', 'Chalet' ),
	( 'CHP', 'Chapelle' ),
	( 'CHS', 'Chaussée' ),
	( 'CHT', 'Château' ),
	( 'CHV', 'Chemin vicinal' ),
	( 'CITE', 'Cité' ),
	( 'CLOI', 'Cloître' ),
	( 'CLOS', 'Clos' ),
	( 'COL', 'Col' ),
	( 'COLI', 'Colline' ),
	( 'COR', 'Corniche' ),
	( 'COTE', 'Côte(au)' ),
	( 'COTT', 'Cottage' ),
	( 'COUR', 'Cour' ),
	( 'CPG', 'Camping' ),
	( 'CRS', 'Cours' ),
	( 'CST', 'Castel' ),
	( 'CTR', 'Contour' ),
	( 'CTRE', 'Centre' ),
	( 'DARS', 'Darse' ),
	( 'DEG', 'Degré' ),
	( 'DIG', 'Digue' ),
	( 'DOM', 'Domaine' ),
	( 'DSC', 'Descente' ),
	( 'ECL', 'Ecluse' ),
	( 'EGL', 'Eglise' ),
	( 'EN', 'Enceinte' ),
	( 'ENC', 'Enclos' ),
	( 'ENV', 'Enclave' ),
	( 'ESC', 'Escalier' ),
	( 'ESP', 'Esplanade' ),
	( 'ESPA', 'Espace' ),
	( 'ETNG', 'Etang' ),
	( 'FG', 'Faubourg' ),
	( 'FON', 'Fontaine' ),
	( 'FORM', 'Forum' ),
	( 'FORT', 'Fort' ),
	( 'FOS', 'Fosse' ),
	( 'FOYR', 'Foyer' ),
	( 'FRM', 'Ferme' ),
	( 'GAL', 'Galerie' ),
	( 'GARE', 'Gare' ),
	( 'GARN', 'Garenne' ),
	( 'GBD', 'Grand boulevard' ),
	( 'GDEN', 'Grand ensemble' ),
	( 'GPE', 'Groupe' ),
	( 'GPT', 'Groupement' ),
	( 'GR', 'Grand(e) rue' ),
	( 'GRI', 'Grille' ),
	( 'GRIM', 'Grimpette' ),
	( 'HAM', 'Hameau' ),
	( 'HCH', 'Haut chemin' ),
	( 'HIP', 'Hippodrome' ),
	( 'HLE', 'Halle' ),
	( 'HLM', 'HLM' ),
	( 'ILE', 'Ile' ),
	( 'IMM', 'Immeuble' ),
	( 'IMP', 'Impasse' ),
	( 'JARD', 'Jardin' ),
	( 'JTE', 'Jetée' ),
	( 'LD', 'Lieu dit' ),
	( 'LEVE', 'Levée' ),
	( 'LOT', 'Lotissement' ),
	( 'MAIL', 'Mail' ),
	( 'MAN', 'Manoir' ),
	( 'MAR', 'Marche' ),
	( 'MAS', 'Mas' ),
	( 'MET', 'Métro' ),
	( 'MF', 'Maison forestiere' ),
	( 'MLN', 'Moulin' ),
	( 'MTE', 'Montée' ),
	( 'MUS', 'Musée' ),
	( 'NTE', 'Nouvelle route' ),
	( 'PAE', 'Petite avenue' ),
	( 'PAL', 'Palais' ),
	( 'PARC', 'Parc' ),
	( 'PAS', 'Passage' ),
	( 'PASS', 'Passe' ),
	( 'PAT', 'Patio' ),
	( 'PAV', 'Pavillon' ),
	( 'PCH', 'Porche - petit chemin' ),
	( 'PERI', 'Périphérique' ),
	( 'PIM', 'Petite impasse' ),
	( 'PKG', 'Parking' ),
	( 'PL', 'Place' ),
	( 'PLAG', 'Plage' ),
	( 'PLAN', 'Plan' ),
	( 'PLCI', 'Placis' ),
	( 'PLE', 'Passerelle' ),
	( 'PLN', 'Plaine' ),
	( 'PLT', 'Plateau(x)' ),
	( 'PN', 'Passage à niveau' ),
	( 'PNT', 'Pointe' ),
	( 'PONT', 'Pont(s)' ),
	( 'PORQ', 'Portique' ),
	( 'PORT', 'Port' ),
	( 'POT', 'Poterne' ),
	( 'POUR', 'Pourtour' ),
	( 'PRE', 'Pré' ),
	( 'PROM', 'Promenade' ),
	( 'PRQ', 'Presqu''île' ),
	( 'PRT', 'Petite route' ),
	( 'PRV', 'Parvis' ),
	( 'PSTY', 'Peristyle' ),
	( 'PTA', 'Petite allée' ),
	( 'PTE', 'Porte' ),
	( 'PTR', 'Petite rue' ),
	( 'QU', 'Quai' ),
	( 'QUA', 'Quartier' ),
	( 'R', 'Rue' ),
	( 'RAC', 'Raccourci' ),
	( 'RAID', 'Raidillon' ),
	( 'REM', 'Rempart' ),
	( 'RES', 'Résidence' ),
	( 'RLE', 'Ruelle' ),
	( 'ROC', 'Rocade' ),
	( 'ROQT', 'Roquet' ),
	( 'RPE', 'Rampe' ),
	( 'RPT', 'Rond point' ),
	( 'RTD', 'Rotonde' ),
	( 'RTE', 'Route' ),
	( 'SEN', 'Sentier' ),
	( 'SQ', 'Square' ),
	( 'STA', 'Station' ),
	( 'STDE', 'Stade' ),
	( 'TOUR', 'Tour' ),
	( 'TPL', 'Terre plein' ),
	( 'TRA', 'Traverse' ),
	( 'TRN', 'Terrain' ),
	( 'TRT', 'Tertre(s)' ),
	( 'TSSE', 'Terrasse(s)' ),
	( 'VAL', 'Val(lée)(lon)' ),
	( 'VCHE', 'Vieux chemin' ),
	( 'VEN', 'Venelle' ),
	( 'VGE', 'Village' ),
	( 'VIA', 'Via' ),
	( 'VLA', 'Villa' ),
	( 'VOI', 'Voie' ),
	( 'VTE', 'Vieille route' ),
	( 'ZA', 'Zone artisanale' ),
	( 'ZAC', 'Zone d''aménagement concerte' ),
	( 'ZAD', 'Zone d''aménagement différé' ),
	( 'ZI', 'Zone industrielle' ),
	( 'ZONE', 'Zone' ),
	( 'ZUP', 'Zone à urbaniser en priorité' );

--------------------------------------------------------------------------------
-- Dans la table membreseps
--------------------------------------------------------------------------------
UPDATE membreseps SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
ALTER TABLE membreseps ALTER COLUMN typevoie TYPE VARCHAR(30);
UPDATE membreseps
	SET typevoie = COALESCE(correspondancestypevoie.libtypevoie, membreseps.typevoie)
	FROM correspondancestypevoie
	WHERE membreseps.typevoie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Dans la table partenaires
--------------------------------------------------------------------------------
UPDATE partenaires SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
ALTER TABLE partenaires ALTER COLUMN typevoie TYPE VARCHAR(30);
UPDATE partenaires
	SET typevoie = COALESCE(correspondancestypevoie.libtypevoie, partenaires.typevoie)
	FROM correspondancestypevoie
	WHERE partenaires.typevoie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Dans la table permanences
--------------------------------------------------------------------------------
UPDATE permanences SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
ALTER TABLE permanences ALTER COLUMN typevoie TYPE VARCHAR(30);
UPDATE permanences
	SET typevoie = COALESCE(correspondancestypevoie.libtypevoie, permanences.typevoie)
	FROM correspondancestypevoie
	WHERE permanences.typevoie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Dans la table servicesinstructeurs
--------------------------------------------------------------------------------
UPDATE servicesinstructeurs SET type_voie = NULL WHERE TRIM( BOTH ' ' FROM type_voie ) = '';
ALTER TABLE servicesinstructeurs ALTER COLUMN type_voie TYPE VARCHAR(30);
UPDATE servicesinstructeurs
	SET type_voie = COALESCE(correspondancestypevoie.libtypevoie, servicesinstructeurs.type_voie)
	FROM correspondancestypevoie
	WHERE servicesinstructeurs.type_voie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Dans la table structuresreferentes
--------------------------------------------------------------------------------
UPDATE structuresreferentes SET type_voie = NULL WHERE TRIM( BOTH ' ' FROM type_voie ) = '';
ALTER TABLE structuresreferentes ALTER COLUMN type_voie TYPE VARCHAR(30);
UPDATE structuresreferentes
	SET type_voie = COALESCE(correspondancestypevoie.libtypevoie, structuresreferentes.type_voie)
	FROM correspondancestypevoie
	WHERE structuresreferentes.type_voie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Dans la table tiersprestatairesapres
--------------------------------------------------------------------------------
UPDATE tiersprestatairesapres SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
ALTER TABLE tiersprestatairesapres ALTER COLUMN typevoie TYPE VARCHAR(30);
UPDATE tiersprestatairesapres
	SET typevoie = COALESCE(correspondancestypevoie.libtypevoie, tiersprestatairesapres.typevoie)
	FROM correspondancestypevoie
	WHERE tiersprestatairesapres.typevoie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Dans la table users
--------------------------------------------------------------------------------
UPDATE users SET typevoie = NULL WHERE TRIM( BOTH ' ' FROM typevoie ) = '';
ALTER TABLE users ALTER COLUMN typevoie TYPE VARCHAR(30);
UPDATE users
	SET typevoie = COALESCE(correspondancestypevoie.libtypevoie, users.typevoie)
	FROM correspondancestypevoie
	WHERE users.typevoie = correspondancestypevoie.typevoie;

--------------------------------------------------------------------------------
-- Suppression de la table correspondancestypevoie
--------------------------------------------------------------------------------
DROP TABLE correspondancestypevoie;

--------------------------------------------------------------------------------
-- 20170322: ajout de contraintes sur les tables de paramétrages de APRE (CD 93)
--------------------------------------------------------------------------------
ALTER TABLE suivisaidesaprestypesaides ALTER COLUMN suiviaideapre_id SET NOT NULL;

--------------------------------------------------------------------------------
-- 20170323: normalisation des tables de paramétrage liées au CER du CD 93
--------------------------------------------------------------------------------

ALTER TABLE metiersexerces ALTER COLUMN name TYPE VARCHAR(250);
ALTER TABLE metiersexerces ALTER COLUMN name SET NOT NULL;

ALTER TABLE secteursactis ALTER COLUMN name TYPE VARCHAR(250);
ALTER TABLE secteursactis ALTER COLUMN name SET NOT NULL;

--------------------------------------------------------------------------------
-- 20170328: normalisation des tables de paramétrage du CD 66
--------------------------------------------------------------------------------

ALTER TABLE services66 ALTER COLUMN name SET NOT NULL;
ALTER TABLE categoriesactionroles ALTER COLUMN name SET NOT NULL;
ALTER TABLE actionroles ALTER COLUMN name SET NOT NULL;
ALTER TABLE motiffichedeliaisons ALTER COLUMN name SET NOT NULL;
ALTER TABLE logicielprimos ALTER COLUMN name SET NOT NULL;
ALTER TABLE propositionprimos ALTER COLUMN name SET NOT NULL;

ALTER TABLE tauxcgscuis66 ALTER COLUMN typeformulaire SET NOT NULL;
ALTER TABLE tauxcgscuis66 ALTER COLUMN secteurmarchand SET NOT NULL;
ALTER TABLE tauxcgscuis66 ALTER COLUMN typecontrat SET NOT NULL;
ALTER TABLE tauxcgscuis66 ALTER COLUMN tauxfixeregion SET NOT NULL;
ALTER TABLE tauxcgscuis66 ALTER COLUMN priseenchargeeffectif SET NOT NULL;
ALTER TABLE tauxcgscuis66 ALTER COLUMN tauxcg SET NOT NULL;

-- Transformation de la fausse clé étrangère depuis tauxcgscuis66.typecontrat vers typescontratscuis66.id
ALTER TABLE tauxcgscuis66 ALTER COLUMN typecontrat TYPE INTEGER USING CAST(typecontrat AS INTEGER);
SELECT alter_table_drop_constraint_if_exists ( 'public', 'tauxcgscuis66', 'tauxcgscuis66_typecontrat_fk' );
ALTER TABLE tauxcgscuis66 ADD CONSTRAINT tauxcgscuis66_typecontrat_fk FOREIGN KEY (typecontrat) REFERENCES typescontratscuis66(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Création d'un index unique pour l'intitulé des types de contrats liés aux secteurs du CUI du CG 66
DROP INDEX IF EXISTS typescontratscuis66_name_idx;
CREATE UNIQUE INDEX typescontratscuis66_name_idx ON typescontratscuis66 (name);

ALTER TABLE motifssortie ALTER COLUMN name TYPE VARCHAR(250);

--------------------------------------------------------------------------------
-- 20170403: ajout de contrainte NOT NULL et d'indexes uniques pour les tables
-- de paramétrage
--------------------------------------------------------------------------------

ALTER TABLE actions ALTER COLUMN libelle SET NOT NULL;

DROP INDEX IF EXISTS actionscandidats_name_idx;
CREATE UNIQUE INDEX actionscandidats_name_idx ON actionscandidats(name);

DROP INDEX IF EXISTS budgetsapres_exercicebudgetai_idx;
CREATE UNIQUE INDEX budgetsapres_exercicebudgetai_idx ON budgetsapres(exercicebudgetai);

DROP INDEX IF EXISTS categorietags_name_idx;
CREATE UNIQUE INDEX categorietags_name_idx ON categorietags(name);

ALTER TABLE comitesapres ALTER COLUMN intitulecomite SET NOT NULL;
DROP INDEX IF EXISTS comitesapres_intitulecomite_idx;
CREATE UNIQUE INDEX comitesapres_intitulecomite_idx ON comitesapres(intitulecomite);

DROP INDEX IF EXISTS courrierspdos_name_idx;
CREATE UNIQUE INDEX courrierspdos_name_idx ON courrierspdos(name);

ALTER TABLE decisionspdos ALTER COLUMN libelle SET NOT NULL;

ALTER TABLE descriptionspdos ALTER COLUMN name SET NOT NULL;
DROP INDEX IF EXISTS descriptionspdos_name_idx;
CREATE UNIQUE INDEX descriptionspdos_name_idx ON descriptionspdos(name);

ALTER TABLE groups ALTER COLUMN name SET NOT NULL;

DROP INDEX IF EXISTS listesanctionseps58_sanction_idx;
CREATE UNIQUE INDEX listesanctionseps58_sanction_idx ON listesanctionseps58(sanction);

DROP INDEX IF EXISTS logicielprimos_name_idx;
CREATE UNIQUE INDEX logicielprimos_name_idx ON logicielprimos(name);

DROP INDEX IF EXISTS modelestypescourrierspcgs66_name_idx;
CREATE UNIQUE INDEX modelestypescourrierspcgs66_name_idx ON modelestypescourrierspcgs66(name);

DROP INDEX IF EXISTS motiffichedeliaisons_name_idx;
CREATE UNIQUE INDEX motiffichedeliaisons_name_idx ON motiffichedeliaisons(name);

DROP INDEX IF EXISTS motifsrefuscuis66_name_idx;
CREATE UNIQUE INDEX motifsrefuscuis66_name_idx ON motifsrefuscuis66(name);

ALTER TABLE permanences ALTER COLUMN libpermanence SET NOT NULL;

DROP INDEX IF EXISTS piecescomptables66_name_idx;
CREATE UNIQUE INDEX piecescomptables66_name_idx ON piecescomptables66(name);

DROP INDEX IF EXISTS piecesmailscuis66_name_idx;
CREATE UNIQUE INDEX piecesmailscuis66_name_idx ON piecesmailscuis66(name);

DROP INDEX IF EXISTS polesdossierspcgs66_name_idx;
CREATE UNIQUE INDEX polesdossierspcgs66_name_idx ON polesdossierspcgs66(name);

DROP INDEX IF EXISTS prestatairesfps93_name_idx;
CREATE UNIQUE INDEX prestatairesfps93_name_idx ON prestatairesfps93(name);

DROP INDEX IF EXISTS progsfichescandidatures66_name_idx;
CREATE UNIQUE INDEX progsfichescandidatures66_name_idx ON progsfichescandidatures66(name);

DROP INDEX IF EXISTS propositionprimos_name_idx;
CREATE UNIQUE INDEX propositionprimos_name_idx ON propositionprimos(name);

ALTER TABLE requestgroups ALTER COLUMN name SET NOT NULL;
DROP INDEX IF EXISTS requestgroups_parent_id_name_idx;
CREATE UNIQUE INDEX requestgroups_parent_id_name_idx ON requestgroups(parent_id,name);

ALTER TABLE servicesinstructeurs ALTER COLUMN lib_service SET NOT NULL;

DROP INDEX IF EXISTS sitescovs58_name_idx;
CREATE UNIQUE INDEX sitescovs58_name_idx ON sitescovs58(name);

ALTER TABLE situationspdos ALTER COLUMN libelle SET NOT NULL;
DROP INDEX IF EXISTS situationspdos_libelle_idx;
CREATE UNIQUE INDEX situationspdos_libelle_idx ON situationspdos(libelle);

ALTER TABLE statutsdecisionspdos ALTER COLUMN libelle SET NOT NULL;
DROP INDEX IF EXISTS statutsdecisionspdos_libelle_idx;
CREATE UNIQUE INDEX statutsdecisionspdos_libelle_idx ON statutsdecisionspdos(libelle);

ALTER TABLE statutspdos ALTER COLUMN libelle SET NOT NULL;

DROP INDEX IF EXISTS textareascourrierspdos_name_idx;
CREATE UNIQUE INDEX textareascourrierspdos_name_idx ON textareascourrierspdos(name);

DROP INDEX IF EXISTS thematiquesfps93_type_name_idx;
CREATE UNIQUE INDEX thematiquesfps93_type_name_idx ON thematiquesfps93(type,name);

DROP INDEX IF EXISTS themesapres66_name_idx;
CREATE UNIQUE INDEX themesapres66_name_idx ON themesapres66(name);

ALTER TABLE tiersprestatairesapres ALTER COLUMN nomtiers SET NOT NULL;

ALTER TABLE traitementstypespdos ALTER COLUMN name SET NOT NULL;

ALTER TABLE typesactions ALTER COLUMN libelle SET NOT NULL;

DROP INDEX IF EXISTS typesaidesapres66_themeapre66_id_name_idx;
CREATE UNIQUE INDEX typesaidesapres66_themeapre66_id_name_idx ON typesaidesapres66(themeapre66_id,name);

DROP INDEX IF EXISTS typescourrierspcgs66_name_idx;
CREATE UNIQUE INDEX typescourrierspcgs66_name_idx ON typescourrierspcgs66(name);

ALTER TABLE typesnotifspdos ALTER COLUMN libelle SET NOT NULL;
ALTER TABLE typesnotifspdos ALTER COLUMN modelenotifpdo SET NOT NULL;

ALTER TABLE typesrdv ALTER COLUMN libelle SET NOT NULL;
ALTER TABLE typesrdv ALTER COLUMN modelenotifrdv SET NOT NULL;

DROP INDEX IF EXISTS typesrsapcgs66_name_idx;
CREATE UNIQUE INDEX typesrsapcgs66_name_idx ON typesrsapcgs66(name);

DROP INDEX IF EXISTS valeurstags_categorietag_id_name_idx;
CREATE UNIQUE INDEX valeurstags_categorietag_id_name_idx ON valeurstags(categorietag_id,name);

DROP INDEX IF EXISTS valsprogsfichescandidatures66_progfichecandidature66_id_name_idx;
CREATE UNIQUE INDEX valsprogsfichescandidatures66_progfichecandidature66_id_name_idx ON valsprogsfichescandidatures66(progfichecandidature66_id,name);

ALTER TABLE typoscontrats ALTER COLUMN lib_typo SET NOT NULL;
DROP INDEX IF EXISTS typoscontrats_lib_typo_idx;
CREATE UNIQUE INDEX typoscontrats_lib_typo_idx ON typoscontrats(lib_typo);

DROP INDEX IF EXISTS zonesgeographiques_codeinsee_idx;
CREATE UNIQUE INDEX zonesgeographiques_codeinsee_idx ON zonesgeographiques(codeinsee);
DROP INDEX IF EXISTS zonesgeographiques_libelle_idx;
CREATE UNIQUE INDEX zonesgeographiques_libelle_idx ON zonesgeographiques(libelle);

--------------------------------------------------------------------------------
-- 20170414: ajout de la table contenant les pôles PCG auxquels l'utilisateur aura
-- appartenu précédemment (CG 66).
--------------------------------------------------------------------------------

DROP TABLE IF EXISTS polesdossierspcgs66_users;
CREATE TABLE polesdossierspcgs66_users (
	id					SERIAL NOT NULL PRIMARY KEY,
	poledossierpcg66_id	INTEGER NOT NULL REFERENCES polesdossierspcgs66(id) ON DELETE CASCADE ON UPDATE CASCADE,
	user_id				INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX polesdossierspcgs66_users_poledossierpcg66_id ON polesdossierspcgs66_users(poledossierpcg66_id);
CREATE INDEX polesdossierspcgs66_users_user_id ON polesdossierspcgs66_users(user_id);
CREATE UNIQUE INDEX polesdossierspcgs66_users_poledossierpcg66_id_user_id ON polesdossierspcgs66_users(poledossierpcg66_id, user_id);

COMMENT ON TABLE polesdossierspcgs66_users IS 'Pôles chargés des dossiers PCG auxquels les utilisateurs ont appartenu par le passé';

--------------------------------------------------------------------------------
-- 20170419: ajout de la possibilité d'activer / de désactiver des paramétrages
-- liés aux PDO / dossiers PCG
--------------------------------------------------------------------------------

-- typespdos
SELECT add_missing_table_field ( 'public', 'typespdos', 'actif', 'VARCHAR(1) NOT NULL DEFAULT ''1''' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'typespdos', 'typespdos_actif_in_list_chk' );
ALTER TABLE typespdos ADD CONSTRAINT typespdos_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0', '1'] ) );

-- originespdos
SELECT add_missing_table_field ( 'public', 'originespdos', 'actif', 'VARCHAR(1) NOT NULL DEFAULT ''1''' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'originespdos', 'originespdos_actif_in_list_chk' );
ALTER TABLE originespdos ADD CONSTRAINT originespdos_actif_in_list_chk CHECK ( cakephp_validate_in_list( actif, ARRAY['0', '1'] ) );

--------------------------------------------------------------------------------
-- 20170424: déplacement de l'indication qu'un dossier PCG sera créé automatiquement
-- lors de la transmission à l'organisme auquel sera transmis les dossiers PCG traités,
-- à la place de la configuration de "Generationdossierpcg.Orgtransmisdossierpcg66.id"
-- dans le webrsa.inc.
--------------------------------------------------------------------------------

SELECT add_missing_table_field ( 'public', 'orgstransmisdossierspcgs66', 'generation_auto', 'VARCHAR(1) NOT NULL DEFAULT ''0''' );
SELECT alter_table_drop_constraint_if_exists( 'public', 'orgstransmisdossierspcgs66', 'orgstransmisdossierspcgs66_generation_auto_in_list_chk' );
ALTER TABLE orgstransmisdossierspcgs66 ADD CONSTRAINT orgstransmisdossierspcgs66_actif_in_list_chk CHECK ( cakephp_validate_in_list( generation_auto, ARRAY['0', '1'] ) );

-- Ajout d'une contrainte "NOT NULL" sur le champ "Actif ?"
ALTER TABLE orgstransmisdossierspcgs66 ALTER COLUMN isactif SET NOT NULL;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
