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

-- Table: administration.rejet_historique

-- Il est possible que la table existe déjà:
CREATE OR REPLACE FUNCTION create_table_administration_rejet_historiques() RETURNS VOID AS
$$
BEGIN
	IF NOT EXISTS(
		SELECT *
			FROM pg_tables
			WHERE schemaname = 'administration'
				AND tablename = 'visionneuses'
	)
	THEN
		CREATE TABLE administration.rejet_historique
		(
			cleinfodemandersa integer NOT NULL,
			flux character varying(20) NOT NULL DEFAULT NULL::character varying,
			etape integer,
			table_en_erreur character varying(50) DEFAULT NULL::character varying,
			log character varying(1000) DEFAULT NULL::character varying,
			numdemrsa character varying(20) DEFAULT NULL::character varying,
			matricule character varying(20) DEFAULT NULL::character varying,
			"DT_INSERT" timestamp(6) without time zone NOT NULL DEFAULT now(),
			fic character varying(40),
			balisededonnee character varying(100000),
			CONSTRAINT rejet_historique_pkey PRIMARY KEY (cleinfodemandersa, flux, "DT_INSERT")
		)
		WITH (OIDS=FALSE);
		ALTER TABLE administration.rejet_historique OWNER TO webrsa;
	END IF;
END;
$$
LANGUAGE 'plpgsql';

SELECT create_table_administration_rejet_historiques();
DROP FUNCTION create_table_administration_rejet_historiques();

-- *****************************************************************************
COMMIT;
-- *****************************************************************************