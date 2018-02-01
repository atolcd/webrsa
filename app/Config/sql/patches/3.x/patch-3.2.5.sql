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

CREATE OR REPLACE FUNCTION public.permissions_inherit_module(p_aro_model text, p_aco_alias text) RETURNS INTEGER AS
$$
	DECLARE
		v_affected integer;
		v_query text;
		v_subquery_controllers text;
		v_subquery_modules text;
	BEGIN
		v_subquery_controllers := 'SELECT controllers.id FROM acos AS controllers WHERE controllers.parent_id IS NULL AND controllers.alias = ''controllers''';
		v_subquery_modules := 'SELECT modules.id FROM acos AS modules WHERE modules.parent_id IN ( ' || v_subquery_controllers || ' ) AND modules.alias = ''' || p_aco_alias || '''';

		v_query := 'UPDATE aros_acos
			SET
				_create = ''0'',
				_read = ''0'',
				_update = ''0'',
				_delete = ''0''
			WHERE
				aros_acos.aro_id IN (
					SELECT aros.id
						FROM aros
						WHERE model = ''' || p_aro_model || '''
				)
				AND aros_acos.aco_id IN (
					SELECT acos.id
						FROM acos
						WHERE
							( acos.parent_id IN ( ' || v_subquery_controllers || ' ) AND acos.alias = ''' || p_aco_alias || ''' )
							OR ( acos.parent_id IN ( ' || v_subquery_modules || ' ) )
				)
		;';

		RAISE NOTICE  '%', v_query;
		EXECUTE v_query;

		GET DIAGNOSTICS v_affected = ROW_COUNT;
		RETURN v_affected;
	END;
$$
LANGUAGE plpgsql VOLATILE;

COMMENT ON FUNCTION public.permissions_inherit_module(p_aro_model text, p_aco_alias text)
	IS 'Mise à jour des permissions à hérité pour les ARO dont l''alias est passé en paramètre et pour l''ACO, qui n''est pas la racine, ainsi que ses descendants directs, dont l''alias est passé en paramètre';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************