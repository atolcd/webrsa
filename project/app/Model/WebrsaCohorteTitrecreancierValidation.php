<?php
	/**
	 * Code source de la classe WebrsaCohorteTitrecreancierValidation.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteTitrecreancier', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteTitrecreancierValidation ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteTitrecreancierValidation extends AbstractWebrsaCohorteTitrecreancier
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteTitrecreancierValidation';

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Creance.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.id' => array( 'type' => 'hidden' ),
			'Titrecreancier.motifemissiontitrecreancier_id' => array( 'type' => 'hidden' ),
			'Motifemissiontitrecreancier.emissiontitre' => array( 'type' => 'hidden' ),
			'Titrecreancier.selection' => array( 'type' => 'checkbox'),
			'Titrecreancier.validation' => array(
				'type' => 'select',
				'options' => array( '1' => 'OUI', '2' => 'NON')
			),
			'Titrecreancier.commentairevalidateur' => array( 'type' => 'text'),
			'Titrecreancier.dtvalidation' => array( 'type' => 'date'),
		);

		/**
		 * Tentative de sauvegarde de nouveaux dossiers de COV pour la thématique
		 * à partir de la cohorte.
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;

			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['Titrecreancier']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}else{
					//Initialisation
					$this->Titrecreancier->begin();
					$creance_id = $value['Creance']['id'];
					if ( $value['Titrecreancier']['validation'] == 1 ){
						if ( $value['Motifemissiontitrecreancier']['emissiontitre'] == 1 ){
							$value['Titrecreancier']['etat'] = 'ATTENVOICOMPTA';
						}else{
							$value['Titrecreancier']['etat'] = 'NONVALID';
						}
					}else{
						$value['Titrecreancier']['etat'] = 'ATTAVIS';
					}
					unset( $value['Motifemissiontitrecreancier']);

					//Validation de la sauvegarde
					if( $success && $this->Titrecreancier->saveAll( $value, array( 'validate' => 'only' ) ) ) {
						if( $this->Titrecreancier->saveAll( $value, array( 'atomic' => false ) ) ) {
							if (
								!$this->Creance->setEtatOnForeignChange($creance_id,$value['Titrecreancier']['etat'],'cohorte_validation') &&
								!$this->Historiqueetat->setHisto(
									$this->Titrecreancier->name,
									$value['Titrecreancier']['id'],
									$creance_id,
									'cohorte_validation',
									$value['Titrecreancier']['etat'],
									$this->Titrecreancier->foyerId($creance_id)
									)
							){
								$success = false;
								break;
							}
						} else {
							$success = false;
							break;
						}
					} else {
						$success = false;
						break;
					}
				}
			}

			// Si aucun n'a échoué et qu'on as pas tout vidé
			if($success && !empty($data) ){
				//On commit à la BDD
				$this->Creance->commit();
				$this->Titrecreancier->commit();
			}else{
				//Sinon nétoyage total
				$this->Creance->rollback();
				$this->Titrecreancier->rollback();
				$success = false;
			}

			return $success;
		}
	}
