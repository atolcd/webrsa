<?php
	/**
	 * Fichier source de la classe VerificationpdfcmsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( APPLIBS.'cmis.php' );
	App::uses( 'XShell', 'Console/Command' );
	App::uses( 'Pdf', 'Model' );

	/**
	 * La classe VerificationpdfcmsShell ...
	 *
	 * @package app.Console.Command
	 */
	class VerificationpdfcmsShell extends XShell
	{

		/**
		 *
		 * @var type
		 */
		public $modele;

		/**
		 *
		 * @var type
		 */
		public $modeles = array( );

		/**
		 *
		 * @var type
		 */
		public $uses = array( 'Pdf' );

		/**
		 *
		 * @var type
		 */
		public $Pdf;

		/**
		 *
		 */
		public function initialize() {
			if( !Cmis::configured() ) {
				$this->err( 'Veuillez configurer la connexion au serveur CMS dans votre fichier app/config/webrsa.inc' );
				$this->_stop( 0 );
			}
			parent::initialize();
		}

		/**
		 *
		 */
		protected function _showParams() {
			parent::_showParams();
			$this->out( '<info>Valeur de la colone modele : </info><important>'.(!empty( $this->params['model'] ) ? $this->params['model'] : '').'</important>' );
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Permet de vérifier l\'existance sur le serveur CMS des fichiers dont le chemin se trouve dans la colonne cmspath de la table pdfs.' );
			$this->Pdf = ClassRegistry::init( 'Pdf' );
			$modeles = $this->Pdf->find(
					'all', array(
				'fields' => array( 'DISTINCT( "Pdf"."modele" )' ),
				'contain' => false
					)
			);
			$this->modeles = Set::classicExtract( $modeles, '{n}.0.modele' );
			$parser->addOption( 'model', array(
				'short' => 'm',
				'help' => 'Valeur de la colonne modele de la table pdfs',
				'choices' => $this->modeles
			) );
			$parser->description( "Shell permettant de vérifier l'existance sur le serveur CMS des fichiers dont le chemin se trouve dans la colonne cmspath de la table pdfs. Si le fichier n'existe pas sur le serveur CMS, la ligne de la table pdfs est supprimée. Il faudra utiliser le shell generationpdfs pour regénérer les fichiers." );
			return $parser;
		}

		/**
		 *
		 */
		public function main() {
			if( empty( $this->params['model'] ) ) {
				$this->out( $this->OptionParser->help() );
				$this->_stop( 0 );
			}

			$this->_wait( 'Récupération des informations ...' );
			$this->Pdf->begin();
			$pdfs = $this->Pdf->find(
					'all', array(
				'conditions' => array(
					'Pdf.cmspath IS NOT NULL',
					'Pdf.modele' => $this->params['model']
				),
				'contain' => false
					)
			);
			$this->_wait( 'Analyse ...' );
			$idsASupprimer = array( );
			if( !empty( $pdfs ) ) {
//				foreach( $pdfs as $pdf ) {
				$count = count( $pdfs );
				$this->XProgressBar->start( $count );
				for( $i = 0; $i < $count; $i++ ) {
					$this->XProgressBar->next();
					$cmisDocument = Cmis::read( $pdfs[$i]['Pdf']['cmspath'] );
					if( empty( $cmisDocument ) ) {
						$idsASupprimer[] = $pdfs[$i]['Pdf']['id'];
					}
				}
			}

			$success = true;
			$this->out();
			$this->_wait( 'Suppression ...' );
			if( !empty( $idsASupprimer ) ) {
//				$conditions = array( 'Pdf.id' => $idsASupprimer );
				$count = count( $idsASupprimer );
				$this->XProgressBar->start( $count );
				for( $i = 0; $i < $count; $i++ ) {
					$this->XProgressBar->next();
					$conditions = array( 'Pdf.id' => $idsASupprimer[$i] );
					$this->Pdf->deleteAll( $conditions, false, false );
				}

				$success = ( $this->Pdf->find( 'count', array( 'conditions' => $conditions, 'contain' => false ) ) == 0 );
				$this->out();
				$this->out( sprintf( 'Suppression de %s enregistrements invalides dans la table pdfs: %s.', count( $idsASupprimer ), ( $success ? 'succès' : 'erreur' ) ) );
			}
			else {
				$this->out( 'Aucun enregistrement invalide dans la table pdfs.' );
			}

			if( $success ) {
				$this->Pdf->commit();
			}
			else {
				$this->Pdf->rollback();
			}
		}

	}
?>