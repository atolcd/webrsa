<?php
	/**
	 * Fichier source de la classe GenerationpdfsShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake generationpdfs <fonction> -app app -u <username>
	 *
	 * avec fonction :
	 * - orientsstructs
	 * - relancenonrespectsanctionep93
	 * - cers93
	 *
	 * Et avec limit optionnel sous la forme : -L 20
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
		public $uses = array('Pdf', 'Relancenonrespectsanctionep93', 'User', 'Cer93' );

		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( __d ('shells', 'Shells:GenerationPDF:Infos') );
			$options = array(
				'limit' => array(
					'short' => 'L',
					'help' =>  __d ('shells', 'Shells:GenerationPDF:limit:help') ,
					'default' => 10
				),
				'username' => array(
					'short' => 'u',
					'help' =>  __d ('shells', 'Shells:GenerationPDF:username:help') ,
					'default' => ''
				)
			);
			$parser->addOptions( $options );
			$subcomands = array(
				'relancenonrespectsanctionep93' => array(
					'help' =>   __d ('shells', 'Shells:GenerationPDF:relancenonrespectsanctionep93:help')
				),
				'orientsstructs' => array(
					'help' =>   __d ('shells', 'Shells:GenerationPDF:orientsstructs:help')
				),
				'cers93' => array(
					'help' =>   __d ('shells', 'Shells:GenerationPDF:cers93:help')
				)
			);
			$parser->addSubcommands( $subcomands );
			return $parser;
		}

		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info> '. __d ('shells', 'Shells:GenerationPDF:limit:info').' </info><important>'.$this->params['limit'].'</important>' );
			$this->out( '<info> '. __d ('shells', 'Shells:GenerationPDF:username:info').' </info><important>'.$this->params['username'].'</important>' );
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

			$this->_wait( sprintf( __d ('shells', 'Shells:GenerationPDF:impression:info'), count( $relances ) ) );

			$this->XProgressBar->start( count( $relances ) );

			// Pour la vérification...
			$todo = Hash::extract( $relances, '{n}.Relancenonrespectsanctionep93.id' );

			$success = true;
			foreach( $relances as $i => $relance ) {
				$this->XProgressBar->next( 1, sprintf( "<info>".__d ('shells', 'Shells:GenerationPDF:relancenonrespectsanctionep93:progressbar')."</info>", $i + 1, $relance['Relancenonrespectsanctionep93']['id'] ) );
				$success = $this->Relancenonrespectsanctionep93->generatePdf( $relance['Relancenonrespectsanctionep93']['id'] ) && $success;
				if( empty( $success ) ) { // FIXME: pour les autres aussi
					$out[] = '<error>'.sprintf(__d ('shells', 'Shells:GenerationPDF:relancenonrespectsanctionep93:error'), $i + 1, $relance['Relancenonrespectsanctionep93']['id'] ).'</error>';
					$error = true;
				}
			}

			$missing = $this->_verify( 'Relancenonrespectsanctionep93', $todo );
			if( false === empty( $missing ) ) {
				$out[] = '<error>'.sprintf( __d ('shells', 'Shells:GenerationPDF:relancenonrespectsanctionep93:errorstockage'), implode( ', ', $missing ) ).'</error>';
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
			// Mise en place de l'activation de l'impression automatique pour avoir accès à StorablePdf
			Configure::write( 'Orientation.impression_auto', true );
			$this->loadModel("Orientstruct");

			$error = false;
			$out = array( );

			// A-t-on spécifié l'identifiant d'un utilisateur (obligatoire dans ce cas-ci) ?
			if( empty( $this->params['username'] ) ) {
				$out[] = "<error>".__d ('shells', 'Shells:username:error')."</error>";
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
					$out[] = "<error>".__d ('shells', 'Shells:username:notexists')."</error>";
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

				$this->_wait( sprintf(__d ('shells', 'Shells:GenerationPDF:impression:info'), count( $orientsstructs ) ) );

				$this->XProgressBar->start( count( $orientsstructs ) );

				// Pour la vérification...
				$todo = Hash::extract( $orientsstructs, '{n}.Orientstruct.id' );

				$success = true;
				foreach( $orientsstructs as $i => $orientstruct ) {
					$this->XProgressBar->next( 1, sprintf( "<info>".__d ('shells', 'Shells:GenerationPDF:orientsstructs:progressbar')."</info>", $i + 1, $orientstruct['Orientstruct']['id'] ) );
					$success = $this->Orientstruct->generatePdf( $orientstruct['Orientstruct']['id'], $user['User']['id'] ) && $success;
					if( empty( $success ) ) { // FIXME: pour les autres aussi
						$out[] = '<error>'.sprintf(__d ('shells', 'Shells:GenerationPDF:orientsstructs:error'), $i + 1, $orientstruct['Orientstruct']['id'] ).'</error>';
						$error = true;
					}
				}

				$missing = $this->_verify( 'Orientstruct', $todo );
				if( false === empty( $missing ) ) {
					$out[] = '<error>'.sprintf(__d ('shells', 'Shells:GenerationPDF:orientsstructs:errorstockage'), implode( ', ', $missing ) ).'</error>';
					$error = true;
				}
			}

			$this->_fin( false === $error, $out );
		}

		/**
		 *
		 */
		public function cers93() {
			$error = false;
			$out = array( );

			// A-t-on spécifié l'identifiant d'un utilisateur (obligatoire dans ce cas-ci) ?
			if( empty( $this->params['username'] ) ) {
				$out[] = "<error>".__d ('shells', 'Shells:username:error')."</error>";
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
					$out[] = "<error>".__d ('shells', 'Shells:username:notexists')."</error>";
					$error = true;
				}
			}

			if( !$error ) {
				//Get list of CERs
				/*
				 * Les options de validation dans le formulaire de recherche sont :
				 * \'99decisioncg\', =>
				 * \'99valide\', =>
				 * \'99rejete\' =>
				*/
				$queryData = array(
					'fields' => array( 'Cer93.id' ),
					'conditions' => array(
						'Cer93.positioncer IN (\'99decisioncg\', \'99valide\', \'99rejete\')',
						'Cer93.id NOT IN (
							SELECT pdfs.fk_value
								FROM pdfs
								WHERE
									pdfs.modele = \'Cer93\'
									AND pdfs.fk_value = Cer93.id
						)'
					),
					'order' => array( 'Cer93.modified ASC' ),
					'recursive' => -1
				);

				if( !empty( $this->params['limit'] ) && is_numeric( $this->params['limit'] ) ) {
					$queryData['limit'] = $this->params['limit'];
				}

				$cer93s = $this->Cer93->find( 'all', $queryData );

				$this->_wait( sprintf(__d ('shells', 'Shells:GenerationPDF:impression:info'), count( $cer93s ) ) );

				$this->XProgressBar->start( count( $cer93s ) );

				// Pour la vérification...
				$todo = Hash::extract( $cer93s, '{n}.Cer93.id' );

				$success = true;
				foreach( $cer93s as $i => $cer93s ) {
					$this->XProgressBar->next( 1, sprintf( "<info>".__d ('shells', 'Shells:GenerationPDF:cers93:progressbar')."</info>", $i + 1, $cer93s['Cer93']['id'] ) );
					$success = $this->Cer93->generatePdf( $cer93s['Cer93']['id'], $user['User']['id'] ) && $success;
					if( empty( $success ) ) { // FIXME: pour les autres aussi
						$out[] = '<error>'.sprintf(__d ('shells', 'Shells:GenerationPDF:cers93:error') , $i + 1, $cer93s['Cer93']['id'] ).'</error>';
						$error = true;
					}
				}

				$missing = $this->_verify( 'Cer93', $todo );
				if( false === empty( $missing ) ) {
					$out[] = '<error>'.sprintf(__d ('shells', 'Shells:GenerationPDF:cers93:errorstockage') , implode( ', ', $missing ) ).'</error>';
					$error = true;
				}
			}

			$this->_fin( false === $error, $out );
		}

		/*
		 *
		 */
		protected function _fin( $success, array $out = array() ) {
			$this->_scritpEnd();

			if( false === $success ) {
				$this->out();
				$this->out( $out );
			}

			$message = ( true === $success )
				? '<success>'.__d ('shells', 'Shells:GenerationPDF:finish:success').'</success>'
				: '<error>'.__d ('shells', 'Shells:GenerationPDF:finish:errors').'</error>';
			$this->out( $message );

			$this->_stop( false === $success ? self::ERROR : self::SUCCESS );
		}
	}
?>