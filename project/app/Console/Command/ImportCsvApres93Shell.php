<?php
	/**
	 * Code source de la classe ImportCsvApres93Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	@ini_set( 'memory_limit', '2048M' );
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'File', 'Utility' );

	// lib/Cake/Console/cake ImportCsvApres93 /tmp/APRE.csv
	// lib/Cake/Console/cake ImportCsvApres93 -d true /tmp/APRE.csv
	// lib/Cake/Console/cake ImportCsvApres93 -d true -l true -v /tmp/APRE.csv

	/**
	 * La classe ImportCsvApres93Shell s'occupe de l'importation en base de données
	 * des APRE forfaitaires du CG 93 au format CSV en provenance de Pôle Emploi.
	 *
	 * @package app.Console.Command
	 */
	class ImportCsvApres93Shell extends AppShell
	{
		/**
		 * Le fichier CSV.
		 *
		 * @var File
		 */
		protected $_Csv = null;

		/**
		 * Tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array( 'XProgressBar' );

		/**
		 * Modèles utilisés par ce shell.
		 *
		 * @var array
		 */
		public $uses = array(
			'Apre',
			'Domiciliationbancaire',
			'Integrationfichierapre'
		);

		/**
		 * Les en-têtes auxquels on s'attend dans le fichier CSV.
		 *
		 * @var array
		 */
		protected $_defaultHeaders = array(
			'matricul',
			'respdos',
			'nomrespd',
			'prerespd',
			'nudemrsa',
			'nomact',
			'preact',
			'natprof',
			'nir',
			'nbenf12',
			'adralloc',
			'modpaiac',
			'titureac',
			'nptireac',
			'banreact',
			'guireact',
			'ncptreac',
			'cribreac',
			'date'
		);

		/**
		 * Les en-têtes utilisés pour le traitement du fichier.
		 *
		 * @var array
		 */
		protected $_headers = array();

		/**
		 * Les APRE à intégrer.
		 *
		 * @var array
		 */
		protected $_apres = array();

		/**
		 * Lignes à journaliser.
		 *
		 * @var array
		 */
		protected $_log = array();

		/**
		 * Le chemin vers le fichier de journalisation.
		 *
		 * @var string
		 */
		protected $_logFile = null;

		/**
		 * Contient le dernier message d'erreur.
		 *
		 * @var string
		 */
		protected $_errorMsg = null;

		/**
		 * Contient les lignes rejetées.
		 *
		 * @var array
		 */
		protected $_rejects = array();

		/**
		 * Contient les lignes vides.
		 *
		 * @var array
		 */
		protected $_empty = array();

		/**
		 * Contient le nombre de lignes à traiter.
		 *
		 * @var integer
		 */
		protected $_nbr_atraiter = 0;

		/**
		 * Contient le nombre de lignes correctement enregistrées.
		 *
		 * @var integer
		 */
		protected $_nbr_succes = 0;

		/**
		 * Contient le nombre de lignes non enregistrées pour cause d'erreur.
		 *
		 * @var integer
		 */
		protected $_nbr_erreurs = 0;

		/**
		 * La transaction en cours est-elle correcte ?
		 *
		 * @var boolean
		 */
		protected $_success = true;

		/**
		 * Surcharge de la méthode Shell::out() avec la journalisation.
		 *
		 * @param string|array $message A string or a an array of strings to output
		 * @param integer $newlines Number of newlines to append
		 * @param integer $level The message's output level, see above.
		 * @return integer|boolean Returns the number of bytes returned from writing to stdout.
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::out
		 */
		public function out( $message = null, $newlines = 1, $level = Shell::NORMAL ) {
			$this->_log[] = $message;
			return parent::out( $message, $newlines, $level );
		}

		/**
		 * Surcharge de la méthode Shell::err() avec la journalisation et l'affichage
		 * des erreurs en mode verbeux seulement.
		 *
		 * @param string|array $message A string or a an array of strings to output
		 * @param integer $newlines Number of newlines to append
		 * @param integer $level The message's output level, see above.
		 * @return void
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::err
		 */
		public function err( $message = null, $newlines = 1, $level = Shell::NORMAL ) {
			$this->_log[] = $message;

			$currentLevel = Shell::NORMAL;
			if (!empty($this->params['verbose'])) {
				$currentLevel = Shell::VERBOSE;
			}
			if (!empty($this->params['quiet'])) {
				$currentLevel = Shell::QUIET;
			}
			if ($level <= $currentLevel) {
				parent::err( $message, $newlines );
			}
		}

		/**
		 * Surcahrge de la méthode Shell::error() pour que celles-ci apparaissent
		 * dès le mode Shell::QUIET.
		 *
		 * @param string $title Title of the error
		 * @param string $message An optional error message
		 * @return void
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::error
		 */
		public function error( $title, $message = null, $level = Shell::QUIET ) {
			$this->err( __d( 'cake_console', '<error>Error:</error> %s', $title ), 1, $level );

			if( !empty( $message ) ) {
				$this->err( $message, 1, $level );
			}
			$this->_stop( 1 );
		}

		/**
		 * Outputs a series of minus characters to the standard output, acts as a visual separator.
		 *
		 * @param integer $newlines Number of newlines to pre- and append
		 * @param integer $width Width of the line, defaults to 63
		 * @return void
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::hr
		 */
		public function hr( $newlines = 0, $width = 63, $level = Shell::QUIET ) {
			$this->out( null, $newlines, $level );
			$this->out( str_repeat( '-', $width ), 1, $level );
			$this->out( null, $newlines, $level );
		}

		/**
		 * Ajout des options et paramètres au shell.
		 *
		 * @return ConsoleOptionParser
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();

			$parser->description( "Ce script permet d'importer, via des fichiers .csv, des APRE forfaitaires transmis par la CAF. (CG 93)" );

			$options = array(
				'date' => array(
					'short' => 'd',
					'help' => 'Prise en compte du champ date',
					'choices' => array( 'true', 'false' ),
					'default' => 'true'
				),
				'headers' => array(
					'short' => 'H',
					'help' => 'précise si le fichier à importer commence par une colonne d\'en-tête ou s\'il commence directement par des données à intégrées',
					'choices' => array( 'true', 'false' ),
					'default' => 'true'
				),
				'log' => array(
					'short' => 'l',
					'help' => 'Journalisation',
					'choices' => array( 'true', 'false' ),
					'default' => 'true'
				),
				'separator' => array(
					'short' => 's',
					'help' => 'le caractère utilisé comme séparateur',
					'default' => ';'
				),
			);
			$parser->addOptions( $options );

			$args = array(
				'csv' => array(
					'help' => 'chemin et nom du fichier à importer',
					'required' => true
				)
			);
			$parser->addArguments( $args );
			return $parser;
		}

		/**
		 * Fontcion utilitaire de trim (espaces, doubles quotes) pour un chaîne
		 * ou un array, de façon récursive.
		 *
		 * @param array|string $mixed
		 * @return array|string
		 */
		protected function _trim( $mixed ) {
			$charlist = " \t\n\r\0\x0B\"";

			if( is_array( $mixed ) ) {
				foreach( $mixed as $key => $value ) {
					$mixed[$key] = trim( $value, $charlist );
				}
			}
			else {
				$mixed = trim( $mixed, $charlist );
			}

			return $mixed;
		}

		/**
		 * Obtention de toutes les valeurs de clés d'un array.
		 *
		 * @param array $data
		 * @param array $keys
		 * @return array
		 */
		protected function _hashGetAllKeys( array $data, array $keys ) {
			$return = array();

			foreach( $keys as $key ) {
				$return[$key] = Hash::get( $data, $key );
			}

			return $return;
		}

		/**
		 * Retourne une requête SQL en une ligne à partir d'un modèle et d'un
		 * querydata.
		 *
		 * @param Model $Model
		 * @param array $querydata
		 * @return string
		 */
		protected function _qdToSql( Model $Model, array $querydata ) {
			$sql = $Model->sq( $querydata );
			return preg_replace( "/([\r\n[:blank:]])+/m", ' ', "{$sql};" );
		}

		/**
		 * En-tête du shell.
		 */
		protected function _welcome() {
			$this->out( null, 1, Shell::QUIET );
			$this->out( __d( 'cake_console', '<info>WebRSA %s, CakePHP %s Console</info>', 'v'.app_version(), 'v'.Configure::version() ), 1, Shell::QUIET );
			$this->out( null, 1, Shell::QUIET );
			$this->out( "<info>{$this->name}Shell</info>", 1, Shell::QUIET );
			$this->out( null, 1, Shell::QUIET );
			$this->hr();
			$this->out( __d( 'cake_console', '<info>App: </info>%s', APP_DIR ), 1, Shell::QUIET );
			$this->out( __d( 'cake_console', '<info>Path: </info>%s', APP ), 1, Shell::QUIET );

			// Connection
			$Dbo = $this->Apre->getDatasource();
			$this->out( '<info>Connexion : </info>'.$Dbo->configKeyName, 1, Shell::QUIET );
			$this->out( '<info>Base de donnees : </info>'.$Dbo->config['database'], 1, Shell::QUIET );

			// Journalisation
			$this->out( '<info>Journalisation : </info>'.( Hash::get( $this->params, 'log' ) ? 'true' : 'false' ), 1, Shell::QUIET );
			if( Hash::get( $this->params, 'log' ) == 'true' ) {
				$this->_logFile = LOGS.Inflector::underscore( $this->name ).'-'.date( 'Ymd-His' ).'.log';
				$logFile = 'APP/'.preg_replace( '/^'.str_replace( '/', '\/', APP ).'/', '', $this->_logFile );
				$this->out( "<info>Fichier de log : </info>{$logFile}", 1, Shell::QUIET );
			}

			$this->hr();
		}

		/**
		 * Démarrage et configuration du shell.
		 */
		public function startup() {
			parent::startup();

			// 1°) Si on n'est pas le CG 93, on ne peut pas utiliser ce shell
			$this->checkDepartement( 93 );

			// 2°) Vérification du format des paramètres hors fichier CSV
			if( !is_string( $this->params['separator'] ) ) {
				$this->error( "Le séparateur \"{$this->params['separator']}\" n'est pas correct, n'oubliez pas d'échapper le caractère (par exemple: \";\" plutôt que ;)" );
			}

			foreach( array( 'headers', 'date', 'log' ) as $bool ) {
				if( $this->params[$bool] == 'true' ) {
					$this->params[$bool] = true;
				}
				else if( $this->params[$bool] == 'false' ) {
					$this->params[$bool] = false;
				}

				if( !is_bool( $this->params[$bool] ) ) {
					$this->error( "Le paramètre {$bool} n'est pas correct \"{$this->params[$bool]}\" (valeurs possibles: true et false)" );
				}
			}

			// 3°) Lecture du fichier CSV
			$this->_Csv = new File( Hash::get( $this->args, '0' ) );

			// 3.1°) Vérifications concernant le fichier CSV.
			if( !$this->_Csv->exists() ) {
				$this->error( "Le fichier \"{$this->_Csv->pwd()}\" n'existe pas." );
			}
			else if( !$this->_Csv->readable() ) {
				$this->error( "Le fichier \"{$this->_Csv->pwd()}\" n'est pas lisible." );
			}
			else if( $this->_Csv->size() == 0 ) {
				$this->error( "Le fichier \"{$this->_Csv->pwd()}\" est vide." );
			}

			// 3.2°) Lecture en ré-encodage éventuel du fichier CSV
			mb_detect_order( array( 'UTF-8', 'ISO-8859-1', 'ASCII' ) );
			$csvLines = $this->_Csv->read();
			$encoding = mb_detect_encoding( $csvLines );
			if( $encoding != 'UTF-8' ) {
				$csvLines = mb_convert_encoding( $csvLines, 'UTF-8', $encoding );
			}
			$lines = explode( "\n", $csvLines );

			// 3.3°) Traitement de la ligne d'en-tête
			if( $this->params['headers'] ) {
				$this->_headers = explode( $this->params['separator'], strtolower( $lines[0] ) );
			}
			else {
				$this->_headers = $this->_defaultHeaders;
			}
			$this->_headers = $this->_trim( $this->_headers );

			// 3.4°) Traitement du paramètre date
			if( !$this->params['date'] ) {
				$key = array_search( 'date', $this->_headers );
				if( $key !== false ) {
					unset( $this->_headers[$key] );
				}
			}

			// 3.5°) Scission des lignes d'APRE et de la ligne d'en-tête
			if( $this->params['headers'] ) {
				$this->_apres = array_slice( $lines, 1 );
			}
			else {
				$this->_apres = $lines;
			}

			// 4°) Vérifications
			// 4.1°) Vérification de la ligne d'en-tête
			$diff = array_diff( $this->_defaultHeaders, $this->_headers );
			if( !empty( $diff ) ) {
				$this->err( sprintf( "En-têtes de colonnes manquants: %s", implode( ',', $diff ) ), 1, Shell::QUIET );
				$this->_stop( self::ERROR );
			}

			// 4.2°) Si on n'a aucune APRE
			if( empty( $this->_apres ) ) {
				$this->out( '<info>Aucune APRE présente dans ce fichier</info>', 1, Shell::QUIET );
				$this->_stop( self::SUCCESS );
			}

			// 5°) Dernières configurations
			$this->Apre->Behaviors->attach( 'Conditionnable' );
		}

		/**
		 * Normlisation, explosion et vérification d'une ligne.
		 *
		 * @param integer $index
		 * @param string $data
		 * @return array
		 */
		protected function _normalizeLine( $index, $data ) {
			$line = ( $index + 1 );

			$data = $this->_trim( explode( $this->params['separator'], $data ) );
			$cleanedData = Hash::filter( (array)$data );

			if( empty( $cleanedData ) ) {
				$this->_errorMsg = "<info>Ligne vide, ligne {$line}</info>";
				$empty = '"'.implode( "\"{$this->params['separator']}\"", array_fill( 0, count( $this->_headers ) - 1, null ) ).'"';
				$this->_empty[] = "{$empty};{$this->_errorMsg}";
			}
			else if( count( $this->_headers ) != count( $data ) ) {
				$diffTooFew = array_diff( $this->_headers, array_keys( $data ) );
				$diffTooMany = array_diff( array_keys( $data ), $this->_headers );
				$this->_errorMsg = "<info>Problème de colonnes, ligne {$line}:</info> manquantes: ".implode( ',', $diffTooFew ).", surnuméraires: ".implode( ',', $diffTooMany );
				$this->_rejects[] = "{$this->_apres[$index]};{$this->_errorMsg}";
			}
			else {
				$data = array_combine( $this->_headers, $data );

				$data['nudemrsa'] = str_pad( $data['nudemrsa'], 11, '0', STR_PAD_LEFT );
				$data['banreact'] = str_pad( $data['banreact'], 5, '0', STR_PAD_LEFT );
				$data['guireact'] = str_pad( $data['guireact'], 5, '0', STR_PAD_LEFT );
				$data['ncptreac'] = str_pad( $data['ncptreac'], 11, '0', STR_PAD_LEFT );
				$data['cribreac'] = str_pad( $data['cribreac'], 2, '0', STR_PAD_LEFT );
				$data['nomact'] = strtoupper( replace_accents( $data['nomact'] ) );
				$data['preact'] = strtoupper( replace_accents( $data['preact'] ) );

				if( !$this->params['date'] ) {
					$data['date'] = date( 'd/m/Y' );
				}

				// 2°) Vérification de la ligne
				$matches = array();
				if( !validRib( $data['banreact'], $data['guireact'], $data['ncptreac'], $data['cribreac'] ) ) {
					$this->_errorMsg = "<info>RIB non valide, ligne {$line}:</info> ".implode( "-", array( $data['banreact'], $data['guireact'], $data['ncptreac'], $data['cribreac'] ) );
				}
				else if( preg_match( '/'.$this->params['separator'].'[0-9,\.]+E\+[0-9]+'.$this->params['separator'].'/i', $line, $matches ) ) {
					$this->_errorMsg = "<info>Erreur de format, ligne {$line}:</info> notation exponentielle dans une cellule";
				}
				else if( !preg_match( '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/i', $data['date'], $matches ) ) {
					$this->_errorMsg = "<info>Erreur de format de date, ligne {$line}:</info> on a \"{$data['date']}\" alors qu'on s'attend à JJ/MM/AAAA";
				}

				// 3°) Fin de normalisation de la ligne
				$data['date'] = date( 'Y-m-d', strtotime( $data['date'] ) );
			}

			return $data;
		}

		/**
		 * Traitement d'une ligne du fichier CSV.
		 *
		 * @param integer $index
		 * @param string $data
		 */
		protected function _processLine( $index, $data ) {
			$line = ( $index + 1 );
			$this->_errorMsg = null;

			// 1°) Normalisation de la ligne
			$data = $this->_normalizeLine( $index, $data );

			// 2°) Si on n'a pas d'erreur, traitement de la ligne
			if( is_null( $this->_errorMsg ) ) {
				// 2.1°) Recherche de l'allocataire
				$sqDernierdossierallocataire = $this->Apre->conditionsDernierDossierAllocataire( array(), array( 'Dossier' => array( 'dernier' => true ) ) );

				$querydata = array(
					'fields' => array_merge(
						$this->Apre->Personne->Foyer->Dossier->fields(),
						$this->Apre->Personne->Foyer->fields(),
						$this->Apre->Personne->Foyer->Personne->fields(),
						$this->Apre->Personne->Foyer->Personne->Prestation->fields()
					),
					'joins' => array(
						$this->Apre->Personne->Foyer->Dossier->join( 'Foyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre->Personne->Foyer->Dossier->Foyer->join( 'Personne', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre->Personne->Foyer->Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						'Dossier.numdemrsa' => $data['nudemrsa'],
						$sqDernierdossierallocataire,
						'OR' => array(
							"SUBSTRING( TRIM( BOTH ' ' FROM Personne.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH ' ' FROM '{$data['nir']}' ) FROM 1 FOR 13 )",
							array(
								'Personne.nom ILIKE' => $data['nomact'],
								'Personne.prenom ILIKE' => $data['preact'],
							)
						),
						'Prestation.rolepers' => array( 'DEM', 'CJT' )
					),
					'order' => null,
					'recursive' => -1
				);

				$beneficiaires = $this->Apre->Personne->Foyer->Dossier->find( 'all', $querydata );

				// 2.2°) Vérification des résultats de la recherche de l'allocataire
				if( count( $beneficiaires ) == 0 ) {
					$sql = $this->_qdToSql( $this->Apre->Personne->Foyer->Dossier,  $querydata );
					$this->_errorMsg = "<info>Pas de bénéficiaire trouvé, ligne {$line}:</info> {$sql}";
				}
				else if( count( $beneficiaires ) > 1 ) {
					$sql = $this->_qdToSql( $this->Apre->Personne->Foyer->Dossier,  $querydata );
					$this->_errorMsg = "<info>Trop de bénéficiaires trouvés, ligne {$line}:</info> {$sql}";
				}
				else {
					// 2.3°) Traitement de l'allocataire
					$beneficiaire = $beneficiaires[0];

					// 2.3.1°) Vérification de l'allocataire trouvé
					$required = $this->_hashGetAllKeys(
						$beneficiaire,
						array(
							'Dossier.id',
							'Foyer.id',
							'Personne.id',
							'Prestation.id',
							'Prestation.rolepers',
						)
					);

					if( in_array( null, $required, true ) ) {
						$sql = $this->_qdToSql( $this->Apre->Personne->Foyer->Dossier,  $querydata );
						$this->_errorMsg = "<info>Données manquantes lors du traitement du résultat, ligne {$line}:</info> {$sql}";
					}
					else {
						// 2.3.2°) Recherche demandes d'APRE précédente pour la même année
						$qdAprepcd = array(
							'conditions' => array(
								'Apre.personne_id' => $beneficiaire['Personne']['id'],
								'Apre.statutapre' => 'F',
								'Apre.datedemandeapre BETWEEN \''.date( 'Y-m-d', ( strtotime( '-1 year', strtotime( $data['date'] ) ) ) ).'\' AND \''.date( 'Y-m-d', strtotime( $data['date'] ) ).'\''
							),
							'contain' => false
						);
						$nbAprespcd = $this->Apre->find( 'count', $qdAprepcd );

						// 2.3.3°) Recherche code domiciliation bancaire
						$qdDomiciliationsbancaires = array(
							'conditions' => array(
								'Domiciliationbancaire.codebanque' => $data['banreact'],
								'Domiciliationbancaire.codeagence' => $data['guireact']
							),
							'contain' => false
						);
						$nbDomiciliationsbancaires = $this->Domiciliationbancaire->find( 'count', $qdDomiciliationsbancaires );

						if( $nbAprespcd > 0 ) {
							$sql = $this->_qdToSql( $this->Apre,  $qdAprepcd );
							$this->_errorMsg = "<info>Présence d'une demande d'APRE forfaitaire datant de moins de 12 mois, ligne {$line}:</info> {$sql}";
						}
						else if( $nbDomiciliationsbancaires == 0 ) {
							$sql = $this->_qdToSql( $this->Domiciliationbancaire,  $qdDomiciliationsbancaires );
							$this->_errorMsg = "<info>Demande d'APRE forfaitaire rejetée pour cause d'entrée non trouvée dans la table domiciliationsbancaires, ligne {$line}:</info> {$sql}";
						}
						else {
							// 2.3.4°) Tentative d'enregistrement de l'APRE
							$mtforfait = (
								Configure::read( 'Apre.forfaitaire.montantbase' )
								+ (
									Configure::read( 'Apre.forfaitaire.montantenfant12' )
									* min( $data['nbenf12'], Configure::read( 'Apre.forfaitaire.nbenfant12max' ) )
								)
							);

							$apre = array(
								'Apre' => array(
									'personne_id' => $beneficiaire['Personne']['id'],
									'numeroapre' => date( 'Ym' ).sprintf( "%010s", $this->Apre->find( 'count' ) + 1 ),
									'typedemandeapre' => 'AU',
									'datedemandeapre' => $data['date'],
									'mtforfait' => $mtforfait,
									'statutapre' => 'F',
									'nbenf12' => $data['nbenf12'],
									'etatdossierapre' => 'COM',
									'eligibiliteapre' => 'O'
								)
							);

							$this->Apre->create( $apre );
							$apreSuccess = $this->Apre->save( null, array( 'atomic' => false ) );
							if( empty( $apreSuccess ) ) {
								$this->_errorMsg = sprintf( "<error>Erreur lors de l'enregistrement d'une APRE, ligne {$line}:</error> %s", $line, var_export( $this->Apre->validationErrors, true ) );
							}
							$this->_success = !empty( $apreSuccess ) && $this->_success;

							// 2.3.5°) Tentative d'enregistrement ou de mise à jour du paiement foyer
							$topribconj = ( ( Hash::get( $beneficiaire, 'Prestation.rolepers' ) == 'CJT' ) ? true : false );

							$paiementfoyer = $this->Apre->Personne->Foyer->Paiementfoyer->find(
								'first',
								array(
									'conditions' => array(
										'foyer_id' => $beneficiaire['Foyer']['id'],
										'etaban' => $data['banreact'],
										'guiban' => $data['guireact'],
										'numcomptban' => $data['ncptreac'],
										'topribconj' => $topribconj,
										'clerib' => $data['cribreac'],
									),
									'recursive' => -1
								)
							);

							// Mise à jour du paiement foyers
							$paiementfoyer['Paiementfoyer'] = Hash::merge(
								(array)Hash::get( $paiementfoyer, 'Paiementfoyer' ),
								array(
									'foyer_id' => $beneficiaire['Foyer']['id'],
									'titurib' => $data['titureac'],
									'nomprenomtiturib' => $data['nptireac'],
									'etaban' => $data['banreact'],
									'guiban' => $data['guireact'],
									'numcomptban' => $data['ncptreac'],
									'clerib' => $data['cribreac'],
									'topribconj' => $topribconj,
									'modepai' => $data['modpaiac']
								)
							);

							$this->Apre->Personne->Foyer->Paiementfoyer->create( $paiementfoyer );
							$paiementfoyerSuccess = $this->Apre->Personne->Foyer->Paiementfoyer->save( null, array( 'atomic' => false ) );
							if( empty( $paiementfoyerSuccess ) ) {
								$this->_errorMsg = sprintf( "<error>Erreur lors de l'enregistrement d'un paiement foyer, ligne {$line}; erreurs de validation:</error> %s", var_export( $this->Apre->Personne->Foyer->Paiementfoyer->validationErrors, true ) );
							}
							$this->_success = !empty( $paiementfoyerSuccess ) && $this->_success;
						}
					}
				}
			}

			// Si on a une erreur, enregistrement de la ligne dans les rejets
			if( !is_null( $this->_errorMsg ) ) {
				$this->err( $this->_errorMsg, 1, Shell::VERBOSE );
				$errorLine = trim( $this->_apres[$index] );
				$this->_rejects[] = "{$errorLine}{$this->params['separator']}{$this->_errorMsg}";
				$this->_nbr_erreurs++;
			}
			else {
				$this->_nbr_succes++;
			}
		}

		/**
		 * Méthode principale, traitement des lignes du fichier.
		 */
		public function main() {
			$this->_nbr_atraiter = count( $this->_apres );
			$this->XProgressBar->start( $this->_nbr_atraiter );

			$this->Apre->begin();

			// Boucle de traitement des lignes
			foreach( $this->_apres as $index => $data ) {
				$this->_processLine( $index, $data );
				$this->XProgressBar->next();
			}

			// Résumé du traitement
			$integrationfichierapre = array(
				'Integrationfichierapre' => array(
					'date_integration' => date( 'Y-m-d  H:i:s' ),
					'nbr_atraiter' => $this->_nbr_atraiter - count( $this->_empty ),
					'nbr_succes' => $this->_nbr_succes,
					'nbr_erreurs' => $this->_nbr_erreurs,
					'fichier_in' => basename( $this->args[0] ),
					'erreurs' => implode( $this->params['separator'], $this->_headers )."{$this->params['separator']}erreur\n".implode( "\n", $this->_rejects )
				)
			);

			$this->Integrationfichierapre->create( $integrationfichierapre );
			$integrationfichierapreSuccess = $this->Integrationfichierapre->save( null, array( 'atomic' => false ) );
			if( empty( $integrationfichierapreSuccess ) ) {
				$this->err( sprintf( "<error>Erreur lors de l'enregistrement du résumé du traitement:</error> %s", var_export( $this->Integrationfichierapre->validationErrors, true ) ), 1, Shell::QUIET );
			}
			$this->_success = !empty( $integrationfichierapreSuccess ) && $this->_success;

			$nb_empty = count( $this->_empty );
			$message = "%s {$this->_nbr_atraiter} lignes à traiter, {$this->_nbr_succes} lignes traitées, {$this->_nbr_erreurs} lignes rejetées, {$nb_empty} lignes vides.";

			$this->out();
			$this->hr( 0, 63, Shell::NORMAL );
			$this->out();

			if( $this->_success ) {
				$this->Apre->commit();
				$this->out( sprintf( $message, "<success>Script terminé avec succès:</success>" ), 1, Shell::QUIET );
			}
			else {
				$this->Apre->rollback();
				$this->out( sprintf( $message, "<error>Script terminé avec erreurs:</error>" ), 1, Shell::QUIET );
			}

			$this->_stop( $this->_success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Journalisation éventuelle lors de l'arrêt du shell.
		 *
		 * @param integer $status
		 */
		protected function _stop( $status = 0 ) {
			if( $this->params['log'] ) {
				file_put_contents( $this->_logFile, implode( "\n", $this->_log ) );
			}

			parent::_stop( $status );
		}
	}
?>