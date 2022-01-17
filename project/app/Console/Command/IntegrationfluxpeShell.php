<?php
	/**
	 * Fichier source de la classe IntegrationfluxpeShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe IntegrationfluxpeShell ...
	 *
	 * Constats (CG 66, 20101217_dump_webrsaCG66_rc9.sql.gz):
	 * - une personne garde en général son identifiant PE à vie (?)
	 * - une personne peut changer d'identifiant PE lors d'une nouvelle inscription (cas rare, cf. personne 34057)
	 * // 			- les personnes qui nous viennent par le flux ne sont pas toujours DEM/CJT RSA (?)
	 * TODO:
	 * - ajouter l'aide
	 * - ne serait-il pas opportun de supprimer les NIR que l'on sait incorrects
	 * (table personnes, et les tables traitées ici) ?
	 * - bouger l'identifiant PE dans les tables d'historique ?
	 * - ajouter motif lié au code (Catégorie de l'inscription) pour les inscriptions ?
	 *
	 * @package app.Console.Command
	 */
	class IntegrationfluxpeShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Informationpe', 'Nonrespectsanctionep93' );

		/**
		 *
		 * @var type
		 */
		public $out = array( );

		/**
		 *
		 * @var type
		 */
		public $csv = false;

		/**
		 *
		 * @var type
		 */
		public $map = array(
			'cessation' => array(
				'nir',
				'identifiantpe',
				'nom',
				'prenom',
				'dtnai',
				'date',
				'code',
				'motif',
				'nir2',
			),
			'radiation' => array(
				'nir',
				'identifiantpe',
				'nom',
				'prenom',
				'dtnai',
				'date',
				'code',
				'motif',
				'nir2',
			),
			'inscription' => array(
				'nir',
				'identifiantpe',
				'nom',
				'prenom',
				'dtnai',
				'date',
				'code',
				'nir2',
			),
			'stock' => array(
				'nir',
				'identifiantpe',
				'nom',
				'prenom',
				'dtnai',
				'date',
				'code',
				'nir2',
			),
		);

		/**
		 * Nouvelles données novembre 2011
		 *
		 * @var type
		 */
		public $colonnesApdnovembre2011 = array(
			'codeinsee',
			'localite',
			'adresse',
			'ale'
		);

		/**
		 *
		 * @var type
		 */
		public $typesCsv = array(
			'cessations',
			'inscriptions',
			'radiations',
			'stock'
		);

		/**
		 *
		 * @var type
		 */
		private $_etats = array(
			'cessations' => 'cessation',
			'inscriptions' => 'inscription',
			'radiations' => 'radiation',
			'stock' => 'inscription'
		);

		/**
		 *
		 * @var type
		 */
		public $fieldsInformationpe = array(
			'nir',
			'nom',
			'prenom',
			'dtnai',
			'nir2'
		);

		/**
		 *
		 * @var type
		 */
		protected $_rejects = array( );

		/**
		 *
		 * @var type
		 */
		protected $_foreignKeysToHistoriqueetatpe = null;

		/**
		 *
		 * @var type
		 */
		protected $_foreignKeysToInformationpe = null;

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Ce script permet l\'importation des fichiers .csv inscriptions, cessation, radiations et stock transmis par Pôle Emploi.' );
			$options = array(
				'header' => array(
					'short' => 'H',
					'help' => 'Si la première ligne du fichier CSV est une ligne de titre',
					'default' => 'true',
					'choices' => array( 'true', 'false' )
				),
				'separator' => array(
					'short' => 's',
					'help' => 'Quel est le séparateur utilisé dans le fichier CSV',
					'default' => ';'
				),
				'apdnovembre2011' => array(
					'help' => 'Le fichier CSV à intégrer contient-il les colonnes \"code INSEE\", \"localité\", \"adresse\" et \"n° ALE\" (dans les flux à partir de novembre 2011)',
					'short' => 'a',
					'default' => 'true',
					'choices' => array( 'true', 'false' )
				),
				'logerror' => array(
					'help' => 'Doit-on ajouter au fichier CSV des rejets une colonne contenant la raison du rejet ?',
					'short' => 'L',
					'default' => 'true',
					'choices' => array( 'true', 'false' )
				)
			);
			$parser->addOptions( $options );
			$args = array(
				'typeCsv' => array(
					'help' => 'Choix du type d\'informations à importer à partir du fichier CSV de Pôle Emploi',
					'choices' => $this->typesCsv,
					'required' => true
				),
				'csv' => array(
					'help' => 'chemin et nom du fichier à importer',
					'required' => true
				)
			);
			$parser->addArguments( $args );
			return $parser;
		}

		/**
		 *
		 */
		public function startup() {
			parent::startup();
			$this->csv = new File( $this->args[1] );
			if( !$this->csv->exists() ) {
				$this->err( "Le fichier {$this->args[1]} n'existe pas." );
				$this->_stop( 1 );
			}
			else if( !$this->csv->readable() ) {
				$this->err( "Le fichier {$this->args[1]} n'est pas lisible." );
				$this->_stop( 1 );
			}
		}

		/**
		 * Affiche l'en-tête du shell (message d'accueil avec quelques informations)
		 */
		public function _showParams() {
			parent::_showParams();
			$this->out( '<info>La première ligne du fichier est une ligne de titre : </info><important>'.$this->params['header'].'</important>' );
			$this->out( '<info>Séparateur utilisé dans le fichier csv : </info><important>'.$this->params['separator'].'</important>' );
			$this->out( '<info>Le fichier CSV contient les colonnes "code INSEE", "localité", "adresse" et "n° ALE" : </info><important>'.$this->params['apdnovembre2011'].'</important>' );
			$this->out( '<info>Ajouter la colonne contenant la raison du rejet : </info><important>'.$this->params['logerror'].'</important>' );
		}

		/**
		 *
		 */
		protected function _rejectLine( $file, $numLine, $line, $error ) {
			$this->out[] = "Non traitement de la ligne {$numLine} du fichier {$this->csv->path} ({$error}).";
			if( $this->params['logerror'] == 'true' ) {
				$line = "{$line};\"$error\"";
			}
			$this->_rejects[] = $line;
		}

		/**
		 * Fusion des enregistrements des tables informationspe et historiqueetatspe dépendants.
		 *
		 * @param array $oldInformationpe
		 * @return boolean
		 */
		protected function _mergeInformationspe( $oldInformationpe ) {
			$success = true;
			$informationpeIdAGarder = $oldInformationpe[0]['Informationpe']['id'];

			$informationpeIds = Set::extract( $oldInformationpe, '/Informationpe/id' );
			$informationpeIdsASupprimer = $informationpeIds;
			$informationpeIdsASupprimer = array_diff( $informationpeIdsASupprimer, array( $informationpeIdAGarder ) );

			$donneesaconserver = $this->Informationpe->Historiqueetatpe->find(
					'all', array(
				'conditions' => array( 'Historiqueetatpe.informationpe_id' => $informationpeIds ),
				'order' => array( 'Historiqueetatpe.date ASC', 'Historiqueetatpe.etat ASC', 'Historiqueetatpe.informationpe_id ASC' ),
				'contain' => false,
					)
			);

			$donneesUniques = array( );
			$donneesAConserverIds = array( );
			$donneesASupprimerIds = array( );
			$columns = array( 'identifiantpe', 'date', 'etat', 'code', 'motif' );
			if( !empty( $donneesaconserver ) ) {
				foreach( $donneesaconserver as $donneeaconserver ) {
					$tmpDonnees = array( );
					foreach( $columns as $column ) {
						$tmpDonnees[$column] = trim( $donneeaconserver['Historiqueetatpe'][$column] );
						if( empty( $tmpDonnees[$column] ) && !is_int( $tmpDonnees[$column] ) ) {
							$tmpDonnees[$column] = null;
						}
					}
					$tmpDonnees = serialize( $tmpDonnees );
					$index = array_search( $tmpDonnees, $donneesUniques );
					if( $index === false ) {
						$donneesUniques[$donneeaconserver['Historiqueetatpe']['id']] = $tmpDonnees;
						$donneesAConserverIds[] = $donneeaconserver['Historiqueetatpe']['id'];
					}
					else {
						$donneesASupprimerIds[$donneeaconserver['Historiqueetatpe']['id']] = $index;
					}
				}

				if( !empty( $donneesAConserverIds ) ) {
					$success = $this->Informationpe->Historiqueetatpe->updateAllUnBound(
						array( 'Historiqueetatpe.informationpe_id' => $informationpeIdAGarder ),
						array( 'Historiqueetatpe.id' => $donneesAConserverIds )
					) && $success;
				}

				if( !empty( $donneesASupprimerIds ) ) {
					foreach( $this->_foreignKeysToHistoriqueetatpe as $foreignKeyToHistoriqueetatpe ) {
						$linkedModelName = Inflector::classify( $foreignKeyToHistoriqueetatpe['From']['table'] ); // FIXME
						$foreignKeyColumn = $foreignKeyToHistoriqueetatpe['From']['column'];
						foreach( $donneesASupprimerIds as $oldFkValue => $newFkValue ) {
							$success = $this->Informationpe->Historiqueetatpe->{$linkedModelName}->updateAllUnBound(
								array( "{$linkedModelName}.{$foreignKeyColumn}" => $newFkValue ),
								array( "{$linkedModelName}.{$foreignKeyColumn}" => $oldFkValue )
							) && $success;
						}
					}
				}

				if( !empty( $this->_foreignKeysToInformationpe ) ) {
					$this->Informationpe->rollback();
					trigger_error( "La mécanique permettant de mettre à jour les tables liées à informationspe n'est pas encore en place.", E_USER_ERROR );
					return false;
				}

				$success = $this->Informationpe->delete( $informationpeIdsASupprimer ) && $success;
			}

			return $success;
		}

		/**
		 * Initialisation des attributs_foreignKeysToInformationpe et _foreignKeysToHistoriqueetatpe
		 *
		 * @return void
		 */
		protected function _initForeignKeysTo() {
			if( false === $this->Informationpe->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
				$this->Informationpe->Behaviors->attach( 'Postgres.PostgresTable' );
			}
			$this->_foreignKeysToInformationpe = $this->Informationpe->getPostgresForeignKeysTo();
			$tableName = $this->Informationpe->Historiqueetatpe->getDatasource( $this->Informationpe->Historiqueetatpe->useDbConfig )->fullTableName( $this->Informationpe->Historiqueetatpe, false, false );

			foreach( $this->_foreignKeysToInformationpe as $i => $foreignKey ) {
				if( $foreignKey['From']['table'] == $tableName ) {
					unset( $this->_foreignKeysToInformationpe[$i] );
				}
			}

			if( false === $this->Informationpe->Historiqueetatpe->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
				$this->Informationpe->Historiqueetatpe->Behaviors->attach( 'Postgres.PostgresTable' );
			}
			$this->_foreignKeysToHistoriqueetatpe = $this->Informationpe->Historiqueetatpe->getPostgresForeignKeysTo();
		}

		/**
		 *
		 */
		protected function _import( $etat ) {
			$this->_wait( 'Initialisation' );
			$this->_initForeignKeysTo();

			// Si on veut travailler sur les flux à partir de novembre 2011, on aura
			// 4 colonne en plus; voir $this->colonnesApdnovembre2011
			if( $this->params['apdnovembre2011'] == true ) {
				$this->map[$etat] = array_merge( $this->map[$etat], $this->colonnesApdnovembre2011 );
			}

			$lines = explode( "\n", $this->csv->read() );
			$lignespresentes = 0;

			$offsets = array( );
			$offsets['dtnai'] = array_search( 'dtnai', $this->map[$etat] );
			$offsets['date'] = $offsets['dtnai'] + 1;
			$offsets['nir'] = array_search( 'nir', $this->map[$etat] );
			$offsets['nir2'] = array_search( 'nir2', $this->map[$etat] );
			$offsets['identifiantpe'] = array_search( 'identifiantpe', $this->map[$etat] );
			$offsets['code'] = array_search( 'code', $this->map[$etat] );



			$success = true;
			$this->Informationpe->Historiqueetatpe->begin();

			$this->_wait( 'Traitement' );
			$this->XProgressBar->start( count( $lines ) );
			foreach( $lines as $numLine => $line ) {
				$this->XProgressBar->next();
				$line = preg_replace( '/(,+)$/', '', trim( $line ) );
				$line = preg_replace( '/^"(.*)"$/', '\1', trim( $line ) );

				if( !( $numLine == 0 && $this->params['header'] == 'true' ) && trim( $line ) != '' ) {
					$numLine++; // La numérotation des lignes commence à 1

					$parts = explode( $this->params['separator'], $line );

					foreach( array_keys( $parts ) as $i ) {
						$parts[$i] = trim( $parts[$i], '"' );
						$parts[$i] = trim( $parts[$i], ' ' );
					}

					// Reformattage du NIR
					$parts[$offsets['nir']] = str_replace( ' ', '', $parts[$offsets['nir']] );
					$parts[$offsets['nir2']] = str_replace( ' ', '', $parts[$offsets['nir2']] );

					// Reformattage de l'identifiant Pôle Emploi
					$parts[$offsets['identifiantpe']] = str_replace( ' ', '', $parts[$offsets['identifiantpe']] );

					// Le nombre de colonnes de cette ligne ne correspond pas au nombre de colonnes attendu
					if( count( $parts ) != count( $this->map[$etat] ) ) {
						$nParts = count( $parts );
						$nPartsType = count( $this->map[$etat] );
						$this->_rejectLine( $this->csv->path, $numLine, $line, "Le nombre de colonnes de cette ligne ({$nParts}) ne correspond pas au nombre de colonnes attendu ({$nPartsType})" );
					}
					// Colonnes NIR et NIR2 différentes ?
					else if( $parts[$offsets['nir']] != $parts[$offsets['nir2']] ) {
						$this->_rejectLine( $this->csv->path, $numLine, $line, "Les deux NIR sont différents: \"{$parts[$offsets['nir']]}\" et \"{$parts[$offsets['nir2']]}\"" );
					}
					// L'identifiant PE n'est pas formatté correctement
					else if( !preg_match( '/^([0-9]{7})([A-Z0-9])([0-9]{3})$/', $parts[$offsets['identifiantpe']] ) ) {
						$this->_rejectLine( $this->csv->path, $numLine, $line, "L'identifiant Pôle Emploi \"{$parts[$offsets['identifiantpe']]}\" n'est pas formatté correctement" );
					}
					// La date de naissance n'est pas formattée corretement
					else if( !preg_match( '/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/', $parts[$offsets['dtnai']] ) ) {
						$this->_rejectLine( $this->csv->path, $numLine, $line, "La date \"{$parts[$offsets['dtnai']]}\" n'est pas correcte" );
					}
					// L'autre date n'est pas formattée correctement
					else if( !preg_match( '/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/', $parts[$offsets['date']] ) ) {
						$this->_rejectLine( $this->csv->path, $numLine, $line, "La date \"{$parts[$offsets['date']]}\" n'est pas correcte" );
					}
					// L'autre date n'est pas formattée correctement
					else if( !preg_match( '/^[^ ]+$/', $parts[$offsets['code']] ) ) {
						$this->_rejectLine( $this->csv->path, $numLine, $line, "Le code \"{$parts[$offsets['code']]}\" n'est pas présent" );
					}
					// La ligne a l'air correcte, essai de traitement
					else {
						// Ajout de la clé pour le NIR (on a le NIR sur 15 caractères d'habitude)
						$parts[$offsets['nir']] = $parts[$offsets['nir']].cle_nir( $parts[$offsets['nir']] );
						$parts[$offsets['nir2']] = $parts[$offsets['nir2']].cle_nir( $parts[$offsets['nir2']] );

						// Si le NIR n'est pas valide, on rejette la ligne
						if( strlen( $parts[$offsets['nir']] ) != 15 || !valid_nir( $parts[$offsets['nir2']] ) || !valid_nir( $parts[$offsets['nir']] ) ) {
							$parts[$offsets['nir']] = null;
						}

						// Recherche / remplissage des tables -> FIXME: en faire une fonction
						// Table informationspe
						$informationpe = array( 'Informationpe' => array( ) );
						foreach( $this->fieldsInformationpe as $column ) {
							$key = array_search( $column, $this->map[$etat] );

							if( $column == 'dtnai' ) {
								// Formattage de la date du format JJ/MM/AAAA au format SQL AAAA-MM-JJ
								// Concerne le champ dtnai -- FIXME, function
								$dateParts = explode( '/', $parts[$key] );
								$parts[$key] = "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
							}
							// Nettoyage des caractères blancs en début et en fin de champ
							$informationpe['Informationpe'][$column] = preg_replace( '/\s+$/', '', preg_replace( '/^\s+/', '', $parts[$key] ) );
						}

						$oldInformationpe = $this->Informationpe->find(
								'all', array(
							'conditions' => $this->Informationpe->qdConditionsJoinPersonneOnValues(
									'Informationpe', $informationpe['Informationpe']
							),
							'contain' => false
								)
						);

						$mergeInformationspe = false;
						if( count( $oldInformationpe ) > 1 ) {
							$success = $this->_mergeInformationspe( $oldInformationpe ) && $success;
							$mergeInformationspe = true;

							$oldInformationpe = $this->Informationpe->find(
									'all', array(
								'conditions' => $this->Informationpe->qdConditionsJoinPersonneOnValues(
										'Informationpe', $informationpe['Informationpe']
								),
								'contain' => false
									)
							);
						}

						if( !empty( $oldInformationpe ) ) {
							$oldInformationpe['Informationpe'] = $oldInformationpe[0]['Informationpe'];
						}

						$saveInformationpe = true;
						// Doit-on mettre à jour l'entrée dans informationspe ?
						if( !empty( $oldInformationpe ) ) {
							$diff = array_diff(
									$oldInformationpe['Informationpe'], $informationpe['Informationpe']
							);
							unset( $diff['id'] );

							if( empty( $diff ) ) {
								$saveInformationpe = false;
							}
							else {
								$informationpe['Informationpe']['id'] = $oldInformationpe['Informationpe']['id'];
							}
						}

						if( $saveInformationpe ) {
							$this->Informationpe->create( $informationpe );
							$tmpSuccessInformationpe = $this->Informationpe->save( null, array( 'atomic' => false ) );
							$success = $tmpSuccessInformationpe && $success;
							$informationpe_id = $this->Informationpe->id;
						}
						else {
							$tmpSuccessInformationpe = true;
							$informationpe_id = $oldInformationpe['Informationpe']['id'];
						}

						// Tables historiquecessationspe, historiqueinscriptionspe, historiqueradiationspe
						$record = array( 'Historiqueetatpe' => array( ) );
						foreach( $this->map[$etat] as $key => $column ) {
							// Formattage de la date du format JJ/MM/AAAA au format SQL AAAA-MM-JJ
							// Concerne le champ date -- FIXME, function
							if( !in_array( $column, $this->fieldsInformationpe ) ) {
								if( $column == 'date' ) {
									$dateParts = explode( '/', $parts[$key] );
									$parts[$key] = "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]}";
								}
								// Nettoyage des caractères blancs en début et en fin de champ
								$record['Historiqueetatpe'][$column] = preg_replace( '/\s+$/', '', preg_replace( '/^\s+/', '', $parts[$key] ) );
							}
						}
						$record['Historiqueetatpe']['informationpe_id'] = $informationpe_id;
						$record['Historiqueetatpe']['etat'] = $etat;

						$conditions = array(
							'informationpe_id' => $informationpe_id,
							'etat' => $etat,
							'date' => $record['Historiqueetatpe']['date'],
							'code' => $record['Historiqueetatpe']['code']
						);
						if( isset( $record['Historiqueetatpe']['motif'] ) ) {
							$conditions['motif'] = $record['Historiqueetatpe']['motif'];
						}

						$oldRecord = $this->Informationpe->Historiqueetatpe->find(
							'first', array(
								'conditions' => $conditions,
								'contain' => false
							)
						);

						if( empty( $oldRecord ) ) {
							$this->Informationpe->Historiqueetatpe->create( $record );
							$tmpSuccessModelClass = $this->Informationpe->Historiqueetatpe->save( null, array( 'atomic' => false ) );
							$success = $tmpSuccessModelClass && $success;

							if( $tmpSuccessInformationpe && $tmpSuccessModelClass ) {
								if( $mergeInformationspe ) {
									$this->out[] = "Enregistrement des données et fusion des enregistrements de la ligne {$numLine} du fichier {$this->csv->path} effectué.";
								}
								else {
									$this->out[] = "Enregistrement des données de la ligne {$numLine} du fichier {$this->csv->path} effectué.";
								}
							}
							else {
								$this->_rejectLine( $this->csv->path, $numLine, $line, "Erreur lors de l'enregistrement des données" );
							}
						}
						else {
							if( !$mergeInformationspe ) {
								$this->out[] = "Non traitement de la ligne {$numLine} du fichier {$this->csv->path} (ligne déjà présente en base).";
							}
							else {
								$this->out[] = "Fusion des enregistrements pour la ligne {$numLine} du fichier {$this->csv->path} (ligne déjà présente en base).";
							}
							$lignespresentes++;
						}
					}
				}
			}

			// A-t'on des lignes rejetées à exporter dans un fichier CSV ?
			if( !empty( $this->_rejects ) ) {
				$titleLine = "";
				if( $this->params['header'] == 'true' ) {
					$headers = rtrim( $lines[0], "\r\n" );
					if( $this->params['logerror'] == 'true' ) {
						$headers = "{$headers};\"Erreur\"";
					}
					$titleLine = "{$headers}\n";
				}
				$output = $titleLine.implode( "\n", $this->_rejects )."\n";
				$outfile = LOGS.$this->name."_rejets_{$this->args[0]}_".date( 'Ymd-His' ).'_'.str_replace( ' ', '_', $this->csv->name() ).'.log.csv';
				file_put_contents( $outfile, $output );
				$this->out[] = "<info>Le fichier de rejets se trouve dans {$outfile}</info>";
			}

			// Pour le CG 93, on doit vérifier si certains dossiers d'EP doivent être désactivés suite à inscription PE
			$departement = Configure::read( 'Cg.departement' );
			if( $departement == 93 ) {
				$success = $this->Nonrespectsanctionep93->calculSortieProcedureRelanceParInscriptionPe() && $success;
			}

			// Fin du shell, résultats
			$this->hr();
			if( $success ) {
				$nlines = ( $numLine - 1 );
				$nrejects = count( $this->_rejects );
				$nouveaux = ( $nlines - $nrejects - $lignespresentes );

				$this->Informationpe->Historiqueetatpe->commit();
				$this->out[] = "<success><important>{$nlines}</important> lignes traitées (<important>{$nouveaux}</important> nouveaux enregistrement, <important>{$nrejects}</important> rejets, <important>{$lignespresentes}</important> enregistrements déjà présents) avec succès.</success>";
			}
			else {
				$this->Informationpe->Historiqueetatpe->rollback();
				$this->out[] = "<error>Erreur lors de l'enregistrement.</error>";
			}

			if( $this->params['verbose'] ) {
				$this->out( $this->out );
			}
		}

		/**
		 * Par défaut, on affiche l'aide
		 */
		public function main() {
			$this->_import( $this->_etats[$this->args[0]] );
		}

	}
?>