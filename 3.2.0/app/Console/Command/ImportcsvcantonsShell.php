<?php
	/**
	 * Fichier source de la classe ImportcsvcantonsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe ImportcsvcantonsShell ...
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvcantonsShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Canton' );

		/**
		 *
		 * @var type
		 */
		public $csv;

		/**
		 *
		 * @var type
		 */
		public $schema = array( );

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Ce script permet, au CG66, d\'importer, via des fichiers .csv, la liste des cantons du département des Pyrénées Orientales.' );
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
		 *
		 *
		 */
		public function startup() {
			parent::startup();
			$error = false;
			$msg = '';

			$this->csv = new File( $this->args[0] );
			if( !$this->csv->exists() ) {
				$msg .= "Le fichier ".$this->args[0]." n'existe pas.";
				$error = true;
			}
			else if( !$this->csv->readable() ) {
				$msg .= "Le fichier ".$this->args[0]." n'est pas lisible.";
				$error = true;
			}


			if( $error ) {
				$this->out();
				$this->out( '<error>'.$msg.'</error>' );
				$this->out();
				$this->out( $this->OptionParser->help() );
				$this->_stop( 1 );
			}
		}

		/**
		 *
		 */
		public function main() {



			$this->Canton->begin();

			$fields = array( );
			$nLignes = 0;
			$nLignesTraitees = 0;
			$nLignesPresentes = 0;
			$success = true;

			$this->schema = $this->Canton->schema();
			$lines = explode( "\n", $this->csv->read() );

			$infosTraitement = array( );

			$this->XProgressBar->start( count( $lines ) );
			foreach( $lines as $line ) {
				$nLignes++;
				$parts = explode( ';', $line );
				$cleanedParts = Hash::filter( (array)$parts );

				if( !empty( $cleanedParts ) ) {
					if( $nLignes == 1 ) {
						foreach( $parts as $key => $part ) {
							$fields[$key] = strtolower( replace_accents( $part ) );
						}
					}
					else {
						$canton = array( 'Canton' => array( ) );

						foreach( $parts as $key => $part ) {
							if( in_array( $fields[$key], array_keys( $this->schema ) ) ) {
								$canton['Canton'][$fields[$key]] = $part;
							}
						}

						$cleanedCanton = Hash::filter( (array)$canton['Canton'] );

						if( !empty( $cleanedCanton ) ) {
							// Mise en majuscules et suppression des accents
							foreach( $this->schema as $field => $infos ) {
								if( Set::classicExtract( $infos, 'type' ) == 'string' ) {
									$canton['Canton'][$field] = strtoupper( replace_accents( $canton['Canton'][$field] ) );
								}
							}

							// Si cette entrée n'est pas encore présente, on l'insère
							if( $this->Canton->find( 'count', array( 'conditions' => array( $canton['Canton'] ) ) ) == 0 ) {
								$this->Canton->create( $canton );
								$tmpSuccess = array( );
								$validateSuccess = $this->Canton->validates();

								if( $validateSuccess ) {
									$tmpSuccess = $this->Canton->save( null, array( 'atomic' => false ) );
									if( !empty( $tmpSuccess ) ) {
										$nLignesTraitees++;
									}
								}
								$success = $validateSuccess && !empty( $tmpSuccess ) && $success;


								// Si la sauvegarde n'a pas eu lieu, on affiche les erreurs
								if( empty( $tmpSuccess ) ) {
									$infosTraitement[] = "<error>Sauvegarde échouée (ligne $nLignes): $line</error>";
									foreach( $this->Canton->validationErrors as $field => $error ) {
										$infosTraitement[] = "\t$field ('".Set::classicExtract( $canton, "Canton.$field" )."') => $error[0]";
									}
								}
							}
							else {
								$nLignesPresentes++;
							}
						}
						else {
							$infosTraitement[] = "<important>Ligne non traitée (ligne $nLignes): $line</important>";
						}
					}
				}
				else {
					$infosTraitement[] = "<important>Ligne non traitée (ligne $nLignes): $line</important>";
				}

				$this->XProgressBar->next();
			}

			$this->out();
			$this->out();
			$this->out( $infosTraitement );

			$message = "%s: $nLignes lignes trouvées, $nLignesTraitees lignes traitées, $nLignesPresentes lignes déjà présentes.\n";
			/// Fin de la transaction
			$endMsg = '';
			if( $success ) {
				$endMsg .= '<important>'.sprintf( $message, "Script terminé avec succès" ).'</important>';
				$this->Canton->commit();
			}
			else {
				$endMsg .= '<error>'.sprintf( $message, "Script terminé avec erreurs" ).'</error>';
				$this->Canton->rollback();
			}

			$this->out();
			$this->out();
			$this->out( $endMsg );
		}

	}
?>