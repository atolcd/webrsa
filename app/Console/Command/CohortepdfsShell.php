<?php
	/**
	 * Fichier source de la classe CohortepdfsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'Component', 'Controller' );
	App::uses( 'GedoooComponent', 'Gedooo.Controller/Component' );

	/**
	 * La classe CohortepdfsShell ...
	 *
	 * @package app.Console.Command
	 */
	class CohortepdfsShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Pdf', 'Orientstruct', 'User' );

		/**
		 *
		 * @var type
		 */
		public $Gedooo;

		/**
		 *
		 * @var type
		 */
		public $user_id = null;

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( array(
				'Ce script se charge de créer une impression d\'orientation en .pdf en base de données pour toutes les personnes ayant été orientées avant la version 2.0rc2.',
				'Ces fichiers doivent être enregistrés en base pour voir les personnes orientées depuis le menu «  Cohorte -> Orientation -> Demandes orientées  " ou le menu «  Recherches -> Par Orientation  ".',
				'Ce script n\'est à exécuter qu\'une seule fois.'
			) );
			$options = array(
				'username' => array(
					'short' => 'u',
					'help' => 'L\'identifiant de l\'utilisateur qui sera utilisé pour la récupération d\'informations lors de l\'impression.',
					'default' => ''
				),
				'limit' => array(
					'short' => 'L',
					'help' => 'Nombre d\'enregistrements à traiter. Doit être un nombre entier positif. Par défaut: 10. Utiliser 0 ou null pour ne pas avoir de limite et traiter tous les enregistrements.',
					'default' => 10
				),
				'order' => array(
					'short' => 'o',
					'help' => 'Permet de trier les enregistrements à traiter par date de validation de l\'orentation (date_valid) en ordre ascendant ou descendant.',
					'default' => 'asc',
					'choices' => array( 'asc', 'desc' )
				)
			);
			$parser->addOptions( $options );
			return $parser;
		}

		/**
		 *
		 */
		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Identifiant de l\'utilisateur</info> : <important>'.$this->params['username'].' </important>' );
			$this->out( '<info>Nombre d\'enregistrements à traiter</info> : <important>'.$this->params['limit'].'</important>' );
			$this->out( '<info>Ordre de tri</info> : <important>'.$this->params['order'].'</important>' );
		}

		/**
		 *
		 *
		 */
		public function startup() {
			// Username
			$user_id = $this->User->field( 'id', array( 'username' => $this->params['username'] ) );
			if( isset( $this->params['username'] ) && is_string( $this->params['username'] ) && !empty( $user_id ) ) {
				$this->user_id = $user_id;
			}
			else {
				$this->out();
				$this->err( sprintf( "Veuillez entrer un identifiant valide", $this->params['username'] ) );
				$this->out();
				$this->out( $this->OptionParser->help() );
				$this->_stop( 1 );
			}
			parent::startup();
		}

		/**
		 *
		 */
		public function main() {
			$success = true;

			$nSuccess = 0;
			$nErrors = 0;
			$this->Gedooo = new GedoooComponent( new ComponentCollection() );

			$orientsstructsQuerydatas = array(
				'conditions' => array(
					'Orientstruct.statut_orient' => 'Orienté',
					'Orientstruct.id NOT IN ( SELECT pdfs.fk_value FROM pdfs WHERE pdfs.modele = \'Orientstruct\' )'
				),
				'order' => array( 'Orientstruct.date_valid '.$this->params['order'] )
			);

			if( !empty( $this->params['limit'] ) ) {
				$orientsstructsQuerydatas['limit'] = $this->params['limit'];
			}

			$orientsstructs_ids = $this->Orientstruct->find( 'list', $orientsstructsQuerydatas );

			$this->_wait( sprintf( "%d enregistrements à traiter.", count( $orientsstructs_ids ) ) );
			$this->XProgressBar->start( count( $orientsstructs_ids ) );

			$cpt = 0;
			foreach( $orientsstructs_ids as $orientstruct_id ) {
				$this->Orientstruct->begin();
				$orientstruct = $this->Orientstruct->find(
						'first', array(
					'conditions' => array(
						'Orientstruct.id' => $orientstruct_id
					),
					'recursive' => -1,
					'contain' => false
						)
				);
				$orientstruct['Orientstruct']['user_id'] = $this->user_id;

				$this->Orientstruct->create( $orientstruct );
				$tmpSuccess = $this->Orientstruct->save( null, array( 'atomic' => false ) );

				if( empty($tmpSuccess) ) {
					$nErrors++;
				}

				if( !empty($tmpSuccess) ) {
					$this->Orientstruct->commit();
				}
				else {
					$this->Orientstruct->rollback();
					$success = false;
				}
				$this->XProgressBar->next( 1, "<info>Génération PDFs (Orientstruct.id=".$orientstruct_id.")</info>" );
				$cpt++;
			}

			/// Fin de la transaction
			$this->out();
			$this->out();
			$message = "%s (<important>{$cpt}</important> pdfs d'orientation à générer, <important>{$nSuccess}</important> succès, <important>{$nErrors}</important> erreurs)";
			if( $success ) {
				$this->out( sprintf( $message, "<success>Script terminé avec succès</success>" ) );
			}
			else {
				$this->out( sprintf( $message, "<error>Script terminé avec erreurs</error>" ) );
			}
		}

	}
?>