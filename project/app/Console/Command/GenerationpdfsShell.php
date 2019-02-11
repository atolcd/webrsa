<?php
	/**
	 * Fichier source de la classe GenerationpdfsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe GenerationpdfsShell ...
	 *
	 * @package app.Console.Command
	 */
	class GenerationpdfsShell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell.
		 *
		 * @var array
		 */
		public $uses = array( 'Orientstruct', 'Pdf', 'Relancenonrespectsanctionep93', 'User' );

		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Ce script se charge de générer et d\'enregistrer les .pdf en base de données pour les orientations ainsi que pour les relances des personnes n\'ayant pas de contractualisation ou pour non renouvellement de contrat.' );
			$options = array(
				'limit' => array(
					'short' => 'L',
					'help' => 'Limite sur le nombre d\'enregistrements à traiter',
					'default' => 10
				),
				'username' => array(
					'short' => 'u',
					'help' => 'L\'identifiant de l\'utilisateur qui sera utilisé pour la récupération d\'informations lors de l\'impression (pour les orientations seulement)',
					'default' => ''
				)
			);
			$parser->addOptions( $options );
			$subcomands = array(
				'relancenonrespectsanctionep93' => array(
					'help' => 'Génère les impressions des relances pour pour non respect et sanctions (CG 93).'
				),
				'orientsstructs' => array(
					'help' => 'Génère les impressions des orientations (le paramètre --username (-u) est obligatoire).'
				)
			);
			$parser->addSubcommands( $subcomands );
			return $parser;
		}

		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Limite sur le nombre d\'enregistrements à traiter : </info><important>'.$this->params['limit'].'</important>' );
			$this->out( '<info>Identifiant de l\'utilisateur : </info><important>'.$this->params['username'].'</important>' );
		}

		/**
		 *
		 */
		public function relancenonrespectsanctionep93() {
			$error = false;
			$out = array( );

			$queryData = array(
				'fields' => array(
					'Relancenonrespectsanctionep93.id'
				),
				'conditions' => array(
					'Relancenonrespectsanctionep93.id NOT IN (
						SELECT pdfs.fk_value
							FROM pdfs
							WHERE
								pdfs.modele = \'Relancenonrespectsanctionep93\'
								AND pdfs.fk_value = Relancenonrespectsanctionep93.id
					)'
				)
			);

			if( !empty( $this->params['limit'] ) && is_numeric( $this->params['limit'] ) ) {
				$queryData['limit'] = $this->params['limit'];
			}

			$relances = $this->Relancenonrespectsanctionep93->find( 'all', $queryData );

			$this->_wait( sprintf( "%s impressions à générer", count( $relances ) ) );

			$this->XProgressBar->start( count( $relances ) );

			// Pour la vérification...
			$todo = Hash::extract( $relances, '{n}.Relancenonrespectsanctionep93.id' );

			$success = true;
			foreach( $relances as $i => $relance ) {
				$this->XProgressBar->next( 1, sprintf( "<info>Impression de la relance %s (id %s)</info>", $i + 1, $relance['Relancenonrespectsanctionep93']['id'] ) );
				$success = $this->Relancenonrespectsanctionep93->generatePdf( $relance['Relancenonrespectsanctionep93']['id'] ) && $success;
				if( empty( $success ) ) { // FIXME: pour les autres aussi
					$out[] = '<error>'.sprintf( "Erreur lors de l'impression de la relance %s (id %s)", $i + 1, $relance['Relancenonrespectsanctionep93']['id'] ).'</error>';
					$error = true;
				}
			}

			$missing = $this->_verify( 'Relancenonrespectsanctionep93', $todo );
			if( false === empty( $missing ) ) {
				$out[] = '<error>'.sprintf( "Erreur lors de l'impression ou du stockage des relances d\'id %s", implode( ', ', $missing ) ).'</error>';
				$error = true;
			}

			$this->_fin( false === $error, $out );
		}

		/**
		 * Retourne, pour un modèle donné et des valeurs de clés étrangères
		 * données, les valeurs des clés étrangères qui ne sont pas stockées
		 * dans la table pdfs.
		 *
		 * @param string $modele
		 * @param array $ids
		 */
		protected function _verify( $modele, array $ids ) {
			$query = array(
				'fields' => array( 'Pdf.fk_value' ),
				'conditions' => array(
					'Pdf.modele' => $modele,
					'Pdf.fk_value' => $ids
				),
				'contain' => false
			);

			$found = Hash::extract(
				$this->Pdf->find( 'all', $query ),
				'{n}.Pdf.fk_value'
			);

			return array_diff( $ids, $found );
		}

		/**
		 *
		 */
		public function orientsstructs() {
			$error = false;
			$out = array( );

			// A-t-on spécifié l'identifiant d'un utilisateur (obligatoire dans ce cas-ci) ?
			if( empty( $this->params['username'] ) ) {
				$out[] = "<error>Veuillez spécifier l'identifiant d'un utilisateur qui sera utilisé pour la récupération d'informations lors de l'impression pour les impressions d'orientations (exemple: -username webrsa).</error>";
				$error = true;
			}
			else {

				// L'utilisateur existe-t'il
				$user = $this->User->find(
						'first', array(
					'conditions' => array(
						'User.username' => $this->params['username']
					),
					'recursive' => -1
						)
				);

				if( empty( $user ) ) {
					$out[] = "<error>L'identifiant d'utilisateur spécifié n'existe pas.</error>";
					$error = true;
				}
			}

			if( !$error ) {
				$queryData = array(
					'fields' => array( 'Orientstruct.id' ),
					'conditions' => array(
						'Orientstruct.statut_orient' => 'Orienté',
						'Orientstruct.id NOT IN (
							SELECT pdfs.fk_value
								FROM pdfs
								WHERE
									pdfs.modele = \'Orientstruct\'
									AND pdfs.fk_value = Orientstruct.id
						)'
					),
					'order' => array( 'Orientstruct.date_valid ASC' ),
					'recursive' => -1
				);

				if( !empty( $this->params['limit'] ) && is_numeric( $this->params['limit'] ) ) {
					$queryData['limit'] = $this->params['limit'];
				}

				$orientsstructs = $this->Orientstruct->find( 'all', $queryData );

				$this->_wait( sprintf( "%s impressions à générer", count( $orientsstructs ) ) );

				$this->XProgressBar->start( count( $orientsstructs ) );

				// Pour la vérification...
				$todo = Hash::extract( $orientsstructs, '{n}.Orientstruct.id' );

				$success = true;
				foreach( $orientsstructs as $i => $orientstruct ) {
					$this->XProgressBar->next( 1, sprintf( "<info>Impression de l'orientation %s (id %s)</info>", $i + 1, $orientstruct['Orientstruct']['id'] ) );
					$success = $this->Orientstruct->generatePdf( $orientstruct['Orientstruct']['id'], $user['User']['id'] ) && $success;
					if( empty( $success ) ) { // FIXME: pour les autres aussi
						$out[] = '<error>'.sprintf( "Erreur lors de l'impression de l'orientation %s (id %s)", $i + 1, $orientstruct['Orientstruct']['id'] ).'</error>';
						$error = true;
					}
				}

				$missing = $this->_verify( 'Orientstruct', $todo );
				if( false === empty( $missing ) ) {
					$out[] = '<error>'.sprintf( "Erreur lors de l'impression ou du stockage des orientations d\'id %s", implode( ', ', $missing ) ).'</error>';
					$error = true;
				}
			}

			$this->_fin( false === $error, $out );
		}

		protected function _fin( $success, array $out = array() ) {
			$this->_scritpEnd();

			if( false === $success ) {
				$this->out();
				$this->out( $out );
			}

			$message = ( true === $success )
				? '<success>Script terminé avec succès</success>'
				: '<error>Script terminé avec erreur(s)</error>';
			$this->out( $message );

			$this->_stop( false === $success ? self::ERROR : self::SUCCESS );
		}
	}
?>