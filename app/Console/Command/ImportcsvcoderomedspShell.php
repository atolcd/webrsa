<?php
	/**
	 * Fichier source de la classe ImportcsvcoderomedspShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe ImportcsvcoderomedspShell ...
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvcoderomedspShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Coderomesecteurdsp66', 'Coderomemetierdsp66' );

		/**
		 *
		 * @var type
		 */
		public $csv;

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( array(
				'Ce script permet d\'importer, via des fichiers .csv, la liste des codes ROME fourni par le Pôle Emploi.',
				'Ceci permet d\'alimenter les listes déroulantes présentes au niveau des DSPs'
			) );
			$options = array(
				'separator' => array(
					'short' => 's',
					'help' => 'le caractère utilisé comme séparateur',
					'default' => ';'
				),
			);
			$parser->addOptions( $options );
			$args = array(
				'fichier_secteurs' => array(
					'help' => 'Chemin et nom du fichier csv à importer (secteurs)',
					'required' => true
				),
				'fichier_metiers' => array(
					'help' => 'Chemin et nom du fichier csv à importer (métiers)',
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

			$this->csv_secteurs = new File( $this->args[0] );
			if( !$this->csv_secteurs->exists() ) {
				$out[] = "Le fichier ".$this->args[0]." n'existe pas.";
				$error = true;
			}
			else if( !$this->csv_secteurs->readable() ) {
				$out[] = "Le fichier ".$this->args[0]." n'est pas lisible.";
				$error = true;
			}
			$this->csv_metiers = new File( $this->args[1] );
			if( !$this->csv_metiers->exists() ) {
				$out[] = "Le fichier ".$this->args[1]." n'existe pas.";
				$error = true;
			}
			else if( !$this->csv_metiers->readable() ) {
				$out[] = "Le fichier ".$this->args[1]." n'est pas lisible.";
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
		 * @return type
		 */
		public function main() {
			$this->connection->begin();
			$out = array( );

			$success_secteurs = true;
			$lines_secteurs = explode( "\n", $this->csv_secteurs->read() );
			$this->_wait( sprintf( 'fichier %s ouvert', $this->args[0] ) );
			$this->XProgressBar->start( count( $lines_secteurs ) );
			$cpt = 1;
			foreach( $lines_secteurs as $nLigne => $line ) {
				$data = explode( $this->params['separator'], $line );

				if( !empty( $data[0] ) && !empty( $data[1] ) ) {
					$code = $data[0];
					$name = $data[1];

					$findsecteur = $this->Coderomesecteurdsp66->find(
							'first', array(
						'conditions' => array(
							'code' => $code
						),
						'recursive' => -1
							)
					);
					if( empty( $findsecteur ) ) {
						$coderomesecteurdsp66['Coderomesecteurdsp66']['code'] = $code;
						$coderomesecteurdsp66['Coderomesecteurdsp66']['name'] = $name;
						$this->Coderomesecteurdsp66->create( $coderomesecteurdsp66 );


						$success_save = $this->Coderomesecteurdsp66->save( null, array( 'atomic' => false ) );
						if( !$success_save ) {
							$out[] = "<important>Erreur de sauvegarde : fichier ".$this->args[0]." à la ligne ".$cpt."</important>";
						}
						$success_secteurs = $success_save && $success_secteurs;
					}
				}
				else {
					$out[] = "<important>Erreur dans le fichier ".$this->args[0]." à la ligne ".$cpt."</important>";
				}
				$cpt++;
				$this->XProgressBar->next();
			}


			$success_metiers = true;
			$lines_metiers = explode( "\n", $this->csv_metiers->read() );
			$this->_wait( sprintf( 'fichier %s ouvert', $this->args[1] ) );
			$this->XProgressBar->start( count( $lines_metiers ) );
			$cpt = 1;
			foreach( $lines_metiers as $nLigne => $line ) {
				$data = explode( $this->params['separator'], $line );

				if( !empty( $data[0] ) && !empty( $data[1] ) && !empty( $data[2] ) ) {
					$codeSecteur = $data[0];
					$code = $data[1];
					$name = $data[2];

					$secteur = $this->Coderomesecteurdsp66->find(
							'first', array(
						'conditions' => array(
							'code' => $codeSecteur
						),
						'contain' => false
							)
					);

					$coderomemetierdsp66['Coderomemetierdsp66']['coderomesecteurdsp66_id'] = $secteur['Coderomesecteurdsp66']['id'];
					$coderomemetierdsp66['Coderomemetierdsp66']['code'] = $code;
					$coderomemetierdsp66['Coderomemetierdsp66']['name'] = $name;
					$this->Coderomemetierdsp66->create( $coderomemetierdsp66 );

					$success_save = $this->Coderomemetierdsp66->save( null, array( 'atomic' => false ) );
					if( !$success_save ) {
						$out[] = "<important>Erreur de sauvegarde : fichier ".$this->args[1]." à la ligne ".$cpt."</important>";
					}
					$success_metiers = $success_save && $success_metiers;
				}
				else {
					$out[] = "<important>Erreur dans le fichier ".$this->args[1]." à la ligne ".$cpt."</important>";
				}
				$cpt++;
				$this->XProgressBar->next();
			}

			if( $success_metiers && $success_secteurs ) {
				$out[] = "<success>Script terminé avec succès</success>";
				$this->connection->commit();
			}
			else {
				$out[] = "<error>Script terminé avec erreurs</error>";
				$this->connection->rollback();
			}
			$this->out();
			$this->out( $out );
		}

	}
?>