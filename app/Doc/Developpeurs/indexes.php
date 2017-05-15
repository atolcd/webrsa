<?php
	/**
	* Script permettant de trouver les champs sur lesquels il devrait exister
	* un index (unique dans certains cas) et pour lesquels l'index n'existe
	* pas en base.
	*/

	$conn = "host=localhost port=5432 dbname=cg93_20110621_0359_eps user=webrsa password=webrsa";
	$conn = pg_connect( $conn );
	if( !$conn ) {
		echo "Une erreur est survenue.\n";
		exit;
	}

	/// 1°) Pour les colonnes ressemblant à une clé étrangère (se terminant par _id, suivant la convention CakePHP)
	$sql = 'SELECT
					DISTINCT information_schema.columns.table_name,
					information_schema.columns.column_name,
					( information_schema.columns.table_name || \'_\' || information_schema.columns.column_name || \'_idx\' ) AS index_name
				FROM information_schema.columns
				WHERE
					column_name ~ \'_id$\'
					AND table_schema = \'public\'
					AND NOT EXISTS (
						SELECT n.nspname as "Schema",
						c.relname as "Name",
						CASE c.relkind WHEN \'r\' THEN \'table\' WHEN \'v\' THEN \'view\' WHEN \'i\' THEN \'index\' WHEN \'S\' THEN \'sequence\' WHEN \'s\' THEN \'special\' END as "Type",
						c2.relname as "Table",
						c.relname AS "Column"
						FROM pg_catalog.pg_class c
							JOIN pg_catalog.pg_index i ON i.indexrelid = c.oid
							JOIN pg_catalog.pg_class c2 ON i.indrelid = c2.oid
							LEFT JOIN pg_catalog.pg_user u ON u.usesysid = c.relowner
							LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
						WHERE c.relkind IN (\'i\',\'\')
							AND n.nspname NOT IN (\'pg_catalog\', \'pg_toast\')
							AND pg_catalog.pg_table_is_visible(c.oid)
							AND  c.relname LIKE \'%_idx\'
							AND c2.relname = information_schema.columns.table_name
					)
				ORDER BY table_name, column_name;';
	$results = pg_query( $conn, $sql );
	if( !$results ) {
		echo "Une erreur est survenue.\n";
		exit;
	}

	$results = pg_fetch_all( $results );

	$lastTable = null;
	if( !empty( $results ) ) {
		echo "-- Ajout des indexes pour les clés étrangères\n";
		echo "BEGIN;\n";
		foreach( $results as $result ) {
			if( $lastTable != $result['table_name'] ) {
				if( !is_null( $lastTable ) ) {
					echo "\n";
				}
				$lastTable = $result['table_name'];
			}
			echo "DROP INDEX IF EXISTS {$result['index_name']};\n";
			echo "CREATE INDEX {$result['index_name']} ON {$result['table_name']} ({$result['column_name']});\n";
		}
		echo "COMMIT;\n\n";
	}

	/// 2°) Pour les colonnes de type libellés
	$sql = 'SELECT
					DISTINCT information_schema.columns.table_name,
					information_schema.columns.column_name,
					( information_schema.columns.table_name || \'_\' || information_schema.columns.column_name || \'_idx\' ) AS index_name
				FROM information_schema.columns
				WHERE
					(
						column_name IN ( \'name\', \'libelle\' )
						OR column_name ~ \'^lib\'
					)
					AND table_schema = \'public\'
					AND NOT EXISTS (
						SELECT n.nspname as "Schema",
						c.relname as "Name",
						CASE c.relkind WHEN \'r\' THEN \'table\' WHEN \'v\' THEN \'view\' WHEN \'i\' THEN \'index\' WHEN \'S\' THEN \'sequence\' WHEN \'s\' THEN \'special\' END as "Type",
						c2.relname as "Table",
						c.relname AS "Column"
						FROM pg_catalog.pg_class c
							JOIN pg_catalog.pg_index i ON i.indexrelid = c.oid
							JOIN pg_catalog.pg_class c2 ON i.indrelid = c2.oid
							LEFT JOIN pg_catalog.pg_user u ON u.usesysid = c.relowner
							LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
						WHERE c.relkind IN (\'i\',\'\')
							AND n.nspname NOT IN (\'pg_catalog\', \'pg_toast\')
							AND pg_catalog.pg_table_is_visible(c.oid)
							AND  c.relname LIKE \'%_idx\'
							AND c2.relname = information_schema.columns.table_name
					)
				ORDER BY table_name, column_name;';
	$results = pg_query( $conn, $sql );
	if( !$results ) {
		echo "Une erreur est survenue.\n";
		exit;
	}

	$results = pg_fetch_all( $results );

	$lastTable = null;
	if( !empty( $results ) ) {
		echo "\n\n-- Ajout des indexes uniques pour les libellés\n";
		echo "BEGIN;\n";
		foreach( $results as $result ) {
			if( $lastTable != $result['table_name'] ) {
				if( !is_null( $lastTable ) ) {
					echo "\n";
				}
				$lastTable = $result['table_name'];
			}
			echo "DROP INDEX IF EXISTS {$result['index_name']};\n";
			echo "CREATE UNIQUE INDEX {$result['index_name']} ON {$result['table_name']} ({$result['column_name']});\n";
		}
		echo "COMMIT;\n";
	}

	pg_close( $conn );
?>