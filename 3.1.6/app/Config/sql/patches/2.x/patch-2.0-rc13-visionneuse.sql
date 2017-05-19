-- *****************************************************************************
BEGIN;
-- *****************************************************************************

CREATE OR REPLACE FUNCTION create_schema_administration() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(SELECT * FROM pg_namespace
		WHERE
			nspname = 'administration'
	)
	THEN
		CREATE SCHEMA administration;
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_schema_administration();
DROP FUNCTION create_schema_administration();

-- -----------------------------------------------------------------------------

-- Table: administration.visionneuses
-- DROP TABLE administration.visionneuses;

-- Il est possible que la table existe déjà:
CREATE OR REPLACE FUNCTION create_table_administration_visionneuses() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(
		SELECT *
			FROM pg_tables
			WHERE schemaname = 'administration'
				AND tablename = 'visionneuses'
	)
	THEN
		CREATE TABLE administration.visionneuses
		(
			id serial NOT NULL,
			flux character(15),
			nomfic character(40),
			dtdeb timestamp without time zone,
			dtfin timestamp without time zone,
			nbrejete numeric(6),
			nbinser numeric(6),
			nbmaj numeric(6),
			perscree numeric(6),
			persmaj numeric(6),
			dspcree numeric(6),
			dspmaj numeric(6)
		)
		WITH (OIDS=FALSE);
		ALTER TABLE administration.visionneuses OWNER TO webrsa;
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_table_administration_visionneuses();
DROP FUNCTION create_table_administration_visionneuses();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************