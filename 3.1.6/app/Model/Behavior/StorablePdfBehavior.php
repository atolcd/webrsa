<?php
	/**
	 * Code source de la classe StorablePdfBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import( 'Behavior', 'Gedooo.Gedooo' );
	require_once( APPLIBS.'cmis.php' );

	/**
	 * Classe StorablePdfBehavior, nécessite le plugin Gedooo.
	 *
	 * Cette classe permet
	 *	- d'automatiser le génération et le stockage de documents PDF à l'enregistrement
	 *	- d'automatiser la suppression de documents PDF stockés à la suppression
	 *	- fournit des fonctions utilitaires permettant de stocker et de récupérer un document PDF
	 *
	 * Il est nécessaire de ne stocker qu'un seul documentdans la table pdfs pour un enregistrement du
	 * modèle lié donné.
	 *
	 * Il est possible de passer une configuration lors de l'attachement du behavior (ici avec les valeurs par
	 * défaut):
	 * <pre>
	 * $actsAs = array(
	 *	'StorablePdf' => array(
	 *		'afterSave' => 'generatePdf', (valeurs possibles: 'generatePdf', 'deleteAll', null/false)
	 *		'afterDelete' => 'deleteAll', (valeurs possibles: 'deleteAll', null/false)
	 *	)
	 * );
	 * </pre>
	 *
	 * @package app.Model.Behavior
	 */
	class StorablePdfBehavior extends GedoooBehavior
	{
		/**
		 * Configuration.
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Valeurs de configuration par défaut.
		 *
		 * @var type
		 */
		public $defaultSettings = array(
			'active' => true,
			'afterSave' => 'generatePdf',
			'afterDelete' => 'deleteAll',
		);

		/**
		 * Configuration du behavior en fonction du modèle auquel il est attaché.
		 *
		 * public $actsAs = array( 'StorablePdf' );
		 *     -> Tous les CG
		 * public $actsAs = array( 'StorablePdf' => array( 'active' => 66 ) );
		 *     -> Seulement le CG 66
		 * public $actsAs = array( 'StorablePdf' => array( 'active' => array( 58, 66 ) ) );
		 *     -> Les CG 58 et 66
		 *
		 * @param Model $model
		 * @param array $settings
		 */
		public function setup( Model $model, $settings = array() ) {
			$this->settings[$model->alias] = Set::merge( $this->defaultSettings, $settings );

			if( is_array( $this->settings[$model->alias]['active'] ) || !is_bool( $this->settings[$model->alias]['active'] ) ) {
				$this->settings[$model->alias]['active'] = (array)$this->settings[$model->alias]['active'];
				$cg = Configure::read( 'Cg.departement' );
				$this->settings[$model->alias]['active'] = in_array( $cg, $this->settings[$model->alias]['active'] );
			}
		}

		/**
		 * Stocke un PDF (si besoin en écrasant l'ancien enregistrement) dans la table pdfs.
		 *
		 * @param Model $model
		 * @param integer $id
		 * @param string $modeledoc
		 * @param mixed $pdf
		 * @return boolean
		 */
		public function storePdf( Model $model, $id, $modeledoc, $pdf ) {
			$Pdf = ClassRegistry::init( 'Pdf' );

			$oldRecord = $Pdf->find(
				'first',
				array(
					'fields' => array( 'id' ),
					'conditions' => array(
						'modele' => $model->alias,
						'fk_value' => $id
					)
				)
			);

			$oldRecord['Pdf']['modele'] = $model->alias;
			$oldRecord['Pdf']['modeledoc'] = $modeledoc;
			$oldRecord['Pdf']['fk_value'] = $id;
			$oldRecord['Pdf']['document'] = $pdf;

			$Pdf->create( $oldRecord );
			return $Pdf->save();
		}

		/**
		 * Génère et stocke un PDF pour un enregistrement donné.
		 * Fait appel aux méthodes getDataForPdf et modeleOdt du modèle.
		 *
		 * @param Model $model
		 * @param integer $id
		 * @return boolean
		 */
		public function generatePdf( Model $model, $id ) {
			if( !$this->settings[$model->alias]['active'] ) {
				return true;
			}

			$success = true;
			$data = $model->getDataForPdf( $id );

			if( !empty( $data ) ) {
				$modeledoc = $model->modeleOdt( $data );

				$pdf = $model->ged( $data, $modeledoc );

				if( $pdf ) {
					$success = $this->storePdf( $model, $id, $modeledoc, $pdf ) && $success;
				}
				else {
					$success = false;
				}
			}
			else {
				$pdfModel = ClassRegistry::init( 'Pdf' );
				$success = $pdfModel->deleteAll( array( 'modele' => $model->alias, 'fk_value' => $id ) ) && $success;
			}

			return $success;
		}
		/**
		 * Automatisation de l'enregistrement ou de la suppression du PDF (possibilité de ne pas exécuter d'action).
		 * Le return ne sert à rien: même si on retourne false c'est comme si ça s'était bien passé
		 *
		 * @param Model $model
		 * @param boolean $created
		 * @return boolean
		 */
		public function afterSave( Model $model, $created ) {
			if( !$this->settings[$model->alias]['active'] ) {
				return true;
			}

			$function = $this->settings[$model->alias][__FUNCTION__];

			if( $function == 'generatePdf' ) {
				return $this->generatePdf( $model, $model->id );
			}
			else if( $function == 'deleteAll' ) {
				return ClassRegistry::init( 'Pdf' )->deleteAll( array( 'modele' => $model->alias, 'fk_value' => $model->id ) );
			}
			else if( $function == false || is_null( $function ) ) {
				return true;
			}
			else {
				$this->log( "La configuration de ".__FUNCTION__." pour la classe ".__CLASS__." n'est pas correct ( '".var_export( $function, true )."' ).", LOG_ERROR );
				return false;
			}
		}

		/**
		 * Automatisation de la suppression du PDF (possibilité de ne pas exécuter d'action).
		 * Le return ne sert à rien: même si on retourne false c'est comme si ça s'était bien passé
		 *
		 * INFO:
		 * 	- fonctionne avec Model::delete
		 * 	- fonctionne avec Model::deleteAll SSI le paramètre callbacks est à true (false par défaut)
		 *
		 * @param Model $model
		 * @return boolean
		 */
		public function afterDelete( Model $model ) {
			if( !$this->settings[$model->alias]['active'] ) {
				return true;
			}

			$function = $this->settings[$model->alias][__FUNCTION__];

			if( $function == 'deleteAll' ) {
				return ClassRegistry::init( 'Pdf' )->deleteAll( array( 'modele' => $model->alias, 'fk_value' => $model->id ) );
			}
			else if( $function == false || is_null( $function ) ) {
				return true;
			}
			else {
				$this->log( "La configuration de ".__FUNCTION__." pour la classe ".__CLASS__." n'est pas correct ( '".var_export( $function, true )."' ).", LOG_ERROR );
				return false;
			}
		}

		/**
		 * Retourne l'enregistrement de la table PDF correspondant au modèle et
		 * à la clé primaire donnés. Si le document PDF n'est pas dans l'enregistrement,
		 * on essaie de le récupérer sur le serveur CMS.
		 * Il est possible de mettre à jour la date d'impression dans la table liée
		 * au modèle.
		 *
		 * @param $model Model Le modèle auquel ce behavior est attaché.
		 * @param $id integer La valeur de la clé primaire de l'enregistrement recherché.
		 * @param $printDateColumn string La colonne qui contient la date d'impression
		 *        devant être mise à jour, null sinon.
		 * @return array
		 */
		public function getStoredPdf( Model $model, $id, $printDateColumn = null ) {
			if( !$this->settings[$model->alias]['active'] ) {
				return array();
			}

			if( !empty( $printDateColumn ) ) {
				$recursive = $model->recursive;
				$model->recursive = -1;

				$model->updateAllUnBound(
						array( "{$model->alias}.{$printDateColumn}" => date( "'Y-m-d'" ) ), array(
					"\"{$model->alias}\".\"{$model->primaryKey}\"" => $id,
					"\"{$model->alias}\".\"{$printDateColumn}\" IS NULL"
						)
				);

				$model->recursive = $recursive;
			}

			$pdf = ClassRegistry::init( 'Pdf' )->find(
				'first',
				array(
					'conditions' => array(
						'Pdf.modele' => $model->alias,
						'Pdf.fk_value' => $id,
					)
				)
			);

			if( !empty( $pdf ) && empty( $pdf['Pdf']['document'] ) ) {
				$cmisPdf = Cmis::read( "/{$model->alias}/{$id}.pdf", true );
				$pdf['Pdf']['document'] = $cmisPdf['content'];
			}

			return $pdf;
		}

	}
?>