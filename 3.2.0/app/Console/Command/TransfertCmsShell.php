<?php
	/**
	 * Fichier source de la classe TransfertCmsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once  APPLIBS.'cmis.php' ;
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe TransfertCmsShell ...
	 *
	 * @package app.Console.Command
	 */
	class TransfertCmsShell extends XShell
	{

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
			$this->out( '<info>Nombre d\'enregistrements à traiter : </info><important>'.$this->params['limit'].'</important>' );
			$this->out( '<info>Type de fichier à déplacer : </info><important>'.$this->params['model'].'</important>' );
			$this->out( '<info>Temps d\'attente entre deux envois (micro-secondes): </info><important>'.$this->params['usleep'].'</important>' );
		}

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Permet de transférer les fichiers .pdf de la table pdfs vers le CMS (Alfresco) et d\'enregistrer le chemin au sein du serveur CMS dans la table pdfs.' );
			$this->Pdf = ClassRegistry::init( 'Pdf' );

			$modeles = $this->Pdf->find(
					'all', array(
				'fields' => array( 'DISTINCT( "Pdf"."modele" )' ),
				'conditions' => array(
					'Pdf.cmspath IS NULL',
					'Pdf.document IS NOT NULL'
				)
					)
			);

			$this->modeles = Set::classicExtract( $modeles, '{n}.0.modele' );
			$options = array(
				'limit' => array(
					'short' => 'L',
					'help' => 'Limite sur le nombre d\'enregistrements à traiter.',
					'default' => 10
				),
				'model' => array(
					'short' => 'm',
					'help' => 'Le type de fichiers à déplacer.',
					'default' => 'Orientstruct',
					'choices' => $this->modeles
				),
				'usleep' => array(
					'short' => 'u',
					'help' => 'Le temps d\'attente entre deux envois (en micro-secondes).',
					'default' => 200000
				)
			);

			$parser->addOptions( $options );
			return $parser;
		}

		/**
		 *
		 */
		protected function _transfertPdfs( $modele ) {
			$pdfs = $this->Pdf->find(
					'all', array(
				'conditions' => array(
					'Pdf.modele' => $modele,
					'Pdf.cmspath IS NULL',
					'Pdf.document IS NOT NULL'
				),
				'limit' => $this->params['limit']
					)
			);

			$this->out( sprintf( "%s documents (%s) à traiter", count( $pdfs ), $modele ) );

			$success = true;
			if( !empty( $pdfs ) ) {
				$this->XProgressBar->start( count( $pdfs ) );
				foreach( $pdfs as $i => $pdf ) {
					$progressBarAdditionalInfos = sprintf( "<info>Traitement du document %s (%s %s)</info>", $i + 1, $modele, $pdf['Pdf']['fk_value'] );
					$this->XProgressBar->next( 1, $progressBarAdditionalInfos );
					$cmsPath = "/{$modele}/{$pdf['Pdf']['fk_value']}.pdf";

					$tmpSuccess = Cmis::write( $cmsPath, $pdf['Pdf']['document'], 'application/pdf', true );

					if( $tmpSuccess ) {
						$pdf['Pdf']['cmspath'] = $cmsPath;
						$this->Pdf->create( $pdf );
						$tmpSuccess = $this->Pdf->save( null, array( 'atomic' => false ) ) && $tmpSuccess;
						if( !$tmpSuccess ) {
							Cmis::delete( $cmsPath );
						}
					}

					if( !$tmpSuccess ) {
						$this->err( sprintf( "Erreur lors de l'écriture du document %s (%s %s)", $i + 1, $modele, $pdf['Pdf']['fk_value'] ) );
						$this->_stop( 1 );// FIXME
					}
					$success = $tmpSuccess && $success;
					usleep( $this->params['usleep'] ); // FIXME: param
				}
			}
		}

		/**
		 *
		 */
		public function main() { // FIXME: fonctions ?
			$this->_transfertPdfs( $this->params['model'] );
		}

	}
?>