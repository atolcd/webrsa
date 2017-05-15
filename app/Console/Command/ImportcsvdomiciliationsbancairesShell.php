<?php
	/**
	 * Fichier source de la classe ImportcsvdomiciliationsbancairesShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe ImportcsvdomiciliationsbancairesShell ...
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvdomiciliationsbancairesShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Domiciliationsbancaire' );

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
			$parser->description( array(
				'Ce script permet d\'importer la liste complète (nationale) des domiciliations bancaires.',
				'La table "domiciliationsbancaires" est composée des champs code banque, code agence et libellé de la domiciliation.'
			) );
			$options = array(
				'headers' => array(
					'short' => 'H',
					'help' => 'précise si le fichier commence par une colonne d\'en-tête ou s\'il commence directement par des données à intégrées',
					'choices' => array( 'true', 'false' ),
					'default' => 'true'
				),
				'separator' => array(
					'short' => 's',
					'help' => 'le caractère utilisé comme séparateur',
					'default' => ';'
				)
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
		 *
		 */
		public function _showParams() {
			parent::_showParams();
			$this->out( '<info>Présence de la ligne d\'entête csv</info> : <important>'.$this->params['headers'].'</important>' );
			$this->out( '<info>Caractère de séparation</info> : <important>'.$this->params['separator'].'</important>' );
		}

		/**
		 *
		 *
		 */
		public function startup() {
			parent::startup();
			$error = false;
			$out = array( );

			$this->csv = new File( $this->args[0] );
			if( !$this->csv->exists() ) {
				$out[] = "Le fichier ".$this->args[0]." n'existe pas.";
				$error = true;
			}
			else if( !$this->csv->readable() ) {
				$out[] = "Le fichier ".$this->args[0]." n'est pas lisible.";
				$error = true;
			}



			if( !is_string( $this->params['separator'] ) ) {
				$out[] = "Le séparateur n'est pas correct, n'oubliez pas d'échapper le caractère (par exemple: \";\" plutôt que ;)";
				$error = true;
			}

			if( $error ) {
				$this->out();
				for( $i = 0; $i < count( $out ); $i++ ) {
					$this->out( '<error>'.$out[$i].'</error>' );
				}

				$this->out();
				$this->out( $this->OptionParser->help() );
				$this->_stop( 1 );
			}
		}

		/**
		 *
		 */
		public function main() {
			$this->Domiciliationsbancaire->begin();

			$fields = array(
				'codebanque',
				'codeagence',
				'libelledomiciliation'
			);
			$nLignes = 0;
			$nLignesTraitees = 0;
			$nLignesNonTraitees = 0;
			$nLignesPresentes = 0;
			$success = true;

			$this->schema = array_keys( $this->Domiciliationsbancaire->schema() );
			$lines = explode( "\n", $this->csv->read() );

			$out = array( );
			$this->XProgressBar->start( count( $lines ) );
			foreach( $lines as $nLigne => $line ) {
				if( $this->params['headers'] == 'false' || ( $nLigne != 0 ) ) {
					$nLignes++;
					$parts = explode( $this->params['separator'], $line );
					$cleanedParts = Hash::filter( (array)$parts );
					if( !empty( $cleanedParts ) ) {
						$domiciliationsbancaire = array( 'Domiciliationsbancaire' => array( ) );

						foreach( $parts as $key => $part ) {
							if( in_array( $fields[$key], $this->schema ) ) {
								$domiciliationsbancaire['Domiciliationsbancaire'][$fields[$key]] = trim( trim( $part, '"' ) );
							}
						}

						$cleanedDomiciliationsbancaire = Hash::filter( (array)$domiciliationsbancaire['Domiciliationsbancaire'] );

						$cleanedDomiciliationsbancaire['codebanque'] = str_pad( $cleanedDomiciliationsbancaire['codebanque'], 5, '0', STR_PAD_LEFT );
						$cleanedDomiciliationsbancaire['codeagence'] = str_pad( $cleanedDomiciliationsbancaire['codeagence'], 5, '0', STR_PAD_LEFT );

						if( !empty( $cleanedDomiciliationsbancaire ) ) {
							// Vérification de la présence du libellé
							$libelledomiciliation = trim( Set::classicExtract( $cleanedDomiciliationsbancaire, 'libelledomiciliation' ) );
							if( empty( $libelledomiciliation ) ) {
								$out[] = "<important>Ligne non traitée à cause de libellé manquant (ligne {$nLigne}): {$line}</important>";
								$nLignesNonTraitees++;
							}
							else {
								if( $this->Domiciliationsbancaire->find( 'count', array( 'conditions' => array( $cleanedDomiciliationsbancaire ) ) ) == 0 ) {
									$this->Domiciliationsbancaire->create( array( 'Domiciliationsbancaire' => $cleanedDomiciliationsbancaire ) );
									if( $tmpSuccess = $this->Domiciliationsbancaire->save() ) {
										$nLignesTraitees++;
									}
									$success = $tmpSuccess && $success;
								}
								else {
									$nLignesPresentes++;
								}
							}
						}
					}
				}
				$this->XProgressBar->next();
			}

			$this->out();
			$this->out( $out );

			$message = "%s: $nLignes lignes trouvées, $nLignesTraitees lignes traitées, $nLignesPresentes lignes déjà présentes, $nLignesNonTraitees lignes non traitées.\n";

			/// Fin de la transaction
			if( $success ) {
				$msg = "<success>".sprintf( $message, "Script terminé avec succès" )."</success>";
				$this->Domiciliationsbancaire->commit();
			}
			else {
				$msg = "<error>".sprintf( $message, "Script terminé avec erreurs" )."</error>";
				$this->Domiciliationsbancaire->rollback();
			}
			$this->out( $msg );
		}

	}
?>