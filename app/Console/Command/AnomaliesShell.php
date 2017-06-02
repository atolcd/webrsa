<?php
	/**
	 * Fichier source de la classe AnomaliesShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe AnomaliesShell ...
	 *
	 * @package app.Console.Command
	 */
	class AnomaliesShell extends XShell
	{
		protected $_checks = array(
			array(
				'text' => 'dossiers sans foyer',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( dossiers.id )
										FROM dossiers
								EXCEPT
									SELECT DISTINCT( foyers.dossier_id )
										FROM foyers
							) AS FOO'
			),
			array(
				'text' => 'foyers sans aucune personne',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( foyers.id )
										FROM foyers
								EXCEPT
									SELECT DISTINCT( personnes.foyer_id )
										FROM personnes
							) AS FOO'
			),
			array(
				'text' => 'foyers sans demandeur RSA',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( foyers.id )
										FROM foyers
								EXCEPT
									SELECT DISTINCT( personnes.foyer_id )
										FROM personnes
										INNER JOIN prestations ON (
											prestations.personne_id = personnes.id
											AND prestations.natprest = \'RSA\'
											AND prestations.rolepers = \'DEM\'
										)
							) AS FOO'
			),
			array(
				'text' => 'foyers sans adressefoyer',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( foyers.id )
										FROM foyers
								EXCEPT
									SELECT DISTINCT( adressesfoyers.foyer_id )
										FROM adressesfoyers
							) AS FOO;'
			),
			array(
				'text' => 'foyers sans adressefoyer de rang 01',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( foyers.id )
										FROM foyers
								EXCEPT
									SELECT DISTINCT( adressesfoyers.foyer_id )
										FROM adressesfoyers
										WHERE adressesfoyers.rgadr = \'01\'
							) AS FOO;'
			),
			array(
				'text' => 'adressesfoyers de rang incorrect',
				'sql' => 'SELECT COUNT(adressesfoyers.*)
							FROM adressesfoyers
							WHERE
								adressesfoyers.rgadr NOT IN ( \'01\', \'02\', \'03\' );'
			),
			array(
				'text' => 'adressesfoyers en doublons',
				'sql' => 'SELECT COUNT(a1.*)
							FROM adressesfoyers AS a1,
								adressesfoyers AS a2
							WHERE
								a1.id < a2.id
								AND a1.foyer_id = a2.foyer_id
								AND a1.rgadr = a2.rgadr;'
			),
			array(
				'text' => 'adressesfoyers faisant reference au meme adresse_id',
				'sql' => 'SELECT COUNT(a1.*)
							FROM adressesfoyers AS a1,
								adressesfoyers AS a2
							WHERE
								a1.id < a2.id
								AND a1.adresse_id = a2.adresse_id;'
			),
			array(
				'text' => 'adresses sans adressesfoyers',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( adresses.id )
										FROM adresses
								EXCEPT
									SELECT DISTINCT( adressesfoyers.adresse_id )
										FROM adressesfoyers
							) AS FOO;'
			),
			array(
				'text' => 'personnes en doublons',
				'sql' => 'SELECT COUNT(DISTINCT(p1.id))
							FROM personnes p1,
								personnes p2
							WHERE p1.id < p2.id
								AND
								(
									( LENGTH(TRIM(p1.nir)) = 15 AND p1.nir = p2.nir )
									OR ( p1.nom = p2.nom AND p1.prenom = p2.prenom AND p1.dtnai = p2.dtnai )
								)'
			),
			array(
				'text' => 'personnes sans prestation RSA',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( personnes.id )
										FROM personnes
								EXCEPT
									SELECT DISTINCT( prestations.personne_id )
										FROM prestations
										WHERE prestations.natprest = \'RSA\'
							) AS FOO'
			),
			array(
				'text' => 'prestations de meme nature et de meme role pour une personne donnee',
				'sql' => 'SELECT COUNT(p1.*)
							FROM prestations p1,
								prestations p2
							WHERE p1.id < p2.id
								AND p1.personne_id = p2.personne_id
								AND p1.natprest = p2.natprest
								AND p1.rolepers = p2.rolepers'
			),
			array(
				'text' => 'prestations de meme nature pour une personne donnee',
				'sql' => 'SELECT COUNT(p1.*)
							FROM prestations p1,
								prestations p2
							WHERE p1.id < p2.id
								AND p1.personne_id = p2.personne_id
								AND p1.natprest = p2.natprest'
			),
			array(
				'text' => 'non demandeurs ou non conjoints RSA possedant des orientsstrcuts orientees',
				'sql' => 'SELECT COUNT(*)
							FROM
							(
									SELECT DISTINCT( orientsstructs.personne_id )
										FROM orientsstructs
										WHERE orientsstructs.statut_orient = \'Orienté\'
								EXCEPT
									SELECT DISTINCT( prestations.personne_id )
										FROM prestations
										WHERE prestations.natprest = \'RSA\'
											AND prestations.rolepers IN ( \'DEM\', \'CJT\' )
							) AS FOO'
			),
		);
		protected $_personnesLinkedQuery = array(
			'text' => 'non demandeurs ou non conjoints RSA possedant des %table%',
			'sql' => 'SELECT COUNT(*)
						FROM
						(
								SELECT DISTINCT( %table%.personne_id )
									FROM %table%
							EXCEPT
								SELECT DISTINCT( prestations.personne_id )
									FROM prestations
									WHERE prestations.natprest = \'RSA\'
										AND prestations.rolepers IN ( \'DEM\', \'CJT\' )
						) AS FOO'
		);

		/**
		 * INFO: SELECT
		 * 		--		tc.constraint_name,
		 * 				tc.table_name,
		 * 		--		kcu.column_name,
		 * 		--		ccu.table_name AS foreign_table_name,
		 * 		--		ccu.column_name AS foreign_column_name
		 * 			FROM
		 * 				information_schema.table_constraints AS tc
		 * 				JOIN information_schema.key_column_usage AS kcu ON tc.constraint_name = kcu.constraint_name
		 * 				JOIN information_schema.constraint_column_usage AS ccu ON ccu.constraint_name = tc.constraint_name
		 * 			WHERE constraint_type = 'FOREIGN KEY'
		 * 				AND kcu.column_name='personne_id';
		 */
		protected $_personnesLinkedTables = array(
			'apres',
			'avispcgpersonnes',
			'calculsdroitsrsa',
			'contratsinsertion',
			'dsps',
			'informationseti',
			'infosagricoles',
			'orientations',
			'parcours',
			'personnes_referents',
			'rendezvous',
			'suivisappuisorientation',
		);

		/**
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * valide
		 */
		public function startup() {
			parent::startup();
			try {
				$this->connection = ConnectionManager::getDataSource( $this->params['connection'] );
			}
			catch( Exception $e ) {

			}
		}

		/**
		 * Par défaut, on affiche l'aide
		 */
		public function main() {
			$this->out();

			foreach( $this->_checks as $check ) {
				$result = $this->connection->query( $check['sql'] );
				$result = Set::classicExtract( $result, '0.0.count' );
				$this->out(
						sprintf(
								"%s\t%s\t (%s ms)", str_pad( $check['text'], 80, " ", STR_PAD_RIGHT ), $result, $this->connection->took
						)
				);
			}

			// Tables liéées à un demandeur ou conjoint RSA
			foreach( $this->_personnesLinkedTables as $table ) {
				$result = $this->connection->query( str_replace( '%table%', $table, $this->_personnesLinkedQuery['sql'] ) );
				$result = Set::classicExtract( $result, '0.0.count' );
				$this->out(
						sprintf(
								"%s\t%s\t (%s ms)", str_pad( str_replace( '%table%', $table, $this->_personnesLinkedQuery['text'] ), 80, " ", STR_PAD_RIGHT ), $result, $this->connection->took
						)
				);
			}
			$this->out();
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Détection des anomalies sur une BDD webrsa' );
			return $parser;
		}

	}
?>