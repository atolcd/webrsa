SELECT
		E'ALTER TABLE ' || kcu.table_name || ' DROP CONSTRAINT ' || tc.constraint_name || ';\nSELECT add_missing_constraint( ''public'', ''' || kcu.table_name || ''', ''' || tc.constraint_name || ''', ''' || ccu.table_name || ''', ''' || kcu.column_name || ''' );\n\n'
	FROM information_schema.table_constraints tc
		LEFT JOIN information_schema.key_column_usage kcu ON (
			tc.constraint_catalog = kcu.constraint_catalog
			AND tc.constraint_schema = kcu.constraint_schema
			AND tc.constraint_name = kcu.constraint_name
		)
		LEFT JOIN information_schema.referential_constraints rc ON (
			tc.constraint_catalog = rc.constraint_catalog
			AND tc.constraint_schema = rc.constraint_schema
			AND tc.constraint_name = rc.constraint_name
		)
		LEFT JOIN information_schema.constraint_column_usage ccu ON (
			rc.unique_constraint_catalog = ccu.constraint_catalog
			AND rc.unique_constraint_schema = ccu.constraint_schema
			AND rc.unique_constraint_name = ccu.constraint_name
		)
		LEFT JOIN information_schema.columns kcc ON (
			kcu.table_schema = kcc.table_schema
			AND kcu.table_name = kcc.table_name
			AND kcu.column_name = kcc.column_name
		)
		LEFT JOIN information_schema.columns ccc ON (
			ccu.table_schema = ccc.table_schema
			AND ccu.table_name = ccc.table_name
			AND ccu.column_name = ccc.column_name
		)
	WHERE
		(
			kcu.table_name LIKE '%pdo%' OR ccu.table_name = '%pdo%'
			/*kcu.table_name = 'descriptionspdos' OR ccu.table_name = 'descriptionspdos'
			OR  kcu.table_name = 'decisionspropospdos' OR ccu.table_name = 'decisionspropospdos'
			OR kcu.table_name = 'propospdos' OR ccu.table_name = 'propospdos'
			OR  kcu.table_name = 'traitementspdos' OR ccu.table_name = 'traitementspdos'*/
		)
		AND tc.constraint_type = 'FOREIGN KEY'
		AND ( update_rule = 'NO ACTION' OR delete_rule = 'NO ACTION' )
	ORDER BY kcu.table_name;

-- Tables de décisions des eps ne possédant pas de champ user_id
SELECT
		DISTINCT(ics.table_name),
		( SELECT COUNT(iics.*) FROM information_schema.columns iics WHERE iics.table_name = ics.table_name AND iics.column_name = 'user_id' ),
		E'SELECT add_missing_table_field (''public'', ''' || ics.table_name || ''', ''user_id'', ''INTEGER'');\nSELECT add_missing_constraint( ''public'', ''' || ics.table_name || ''', ''' || ics.table_name || '_user_id_fk'', ''users'', ''user_id'' );\n\n\n-- FIXME\nUPDATE ' || ics.table_name || ' SET user_id = 6;\nALTER TABLE ' || ics.table_name || ' ALTER COLUMN user_id SET NOT NULL;\n \n DROP INDEX IF EXISTS ' || ics.table_name || '_user_id_isx;\nCREATE INDEX ' || ics.table_name || '_user_id_isx ON ' || ics.table_name || '(user_id);\n\n-- -----------------------------------------------------------------------------\n'
	FROM information_schema.columns ics
	WHERE
		ics.table_name ~ E'decisions.*eps([0-9]{2}){0,1}'
		AND ( SELECT COUNT(iics.*) FROM information_schema.columns iics WHERE iics.table_name = ics.table_name AND iics.column_name = 'user_id' ) = 0
	ORDER BY
		ics.table_name;

-- Enums et leurs valeurs, tables eps
-- SELECT
-- 		table_name,
-- 		column_name,
-- 		udt_name
-- 		'SELECT enum_range( null::' || udt_name ' )'
-- 	FROM information_schema.columns
-- 	WHERE
-- 		table_name ~ E'(eps([0-9]{2})$|eps([0-9]{2})_)'
-- 		AND column_name ~ E'^(decision|origine)$'
-- 	ORDER BY
-- 		table_name,
-- 		column_name,
-- 		udt_name;