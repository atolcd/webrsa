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

--------------------------------------------------------------------------------
-- Pour a table decisionssanctionseps58, on enregistre la date pour une annulation.
-- Pour la reprise des données, on se base sur la date de dernière modification.
--------------------------------------------------------------------------------
ALTER TABLE decisionssanctionseps58 DROP CONSTRAINT decisionssanctionseps58_arretsanction_datearretsanction_chk;

UPDATE decisionssanctionseps58
	SET datearretsanction = DATE_TRUNC( 'day', modified )
	WHERE
		datearretsanction IS NULL
		AND arretsanction IN ( 'annulation1', 'annulation2' );

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

UPDATE decisionssanctionseps58
	SET
		arretsanction = 'finsanction1'
	WHERE
		decision = 'sanction'
		AND decision2 IS NULL
		AND autrelistesanctionep58_id IS NULL
		AND arretsanction = 'finsanction2';

UPDATE decisionssanctionseps58
	SET
		arretsanction = 'annulation1'
	WHERE
		decision = 'sanction'
		AND decision2 IS NULL
		AND autrelistesanctionep58_id IS NULL
		AND arretsanction = 'annulation2';

--------------------------------------------------------------------------------
-- Pour a table decisionssanctionseps58, on enregistre la date pour une annulation.
-- Pour la reprise des données, on se base sur la date de dernière modification.
--------------------------------------------------------------------------------
ALTER TABLE decisionssanctionsrendezvouseps58 DROP CONSTRAINT decisionssanctionsrendezvouseps58_arretsanction_datearretsanction_chk;

UPDATE decisionssanctionsrendezvouseps58
	SET datearretsanction = DATE_TRUNC( 'day', modified )
	WHERE
		datearretsanction IS NULL
		AND arretsanction IN ( 'annulation1', 'annulation2' );

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

UPDATE decisionssanctionsrendezvouseps58
	SET
		arretsanction = 'finsanction1'
	WHERE
		decision = 'sanction'
		AND decision2 IS NULL
		AND autrelistesanctionep58_id IS NULL
		AND arretsanction = 'finsanction2';

UPDATE decisionssanctionsrendezvouseps58
	SET
		arretsanction = 'annulation1'
	WHERE
		decision = 'sanction'
		AND decision2 IS NULL
		AND autrelistesanctionep58_id IS NULL
		AND arretsanction = 'annulation2';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************