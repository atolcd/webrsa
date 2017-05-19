<?php
	/**
	 * Code source de la classe WebrsaCohorteApre66Transfert.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteActioncandidatPersonne', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteApre66Transfert ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteActioncandidatPersonneEnattente extends AbstractWebrsaCohorteActioncandidatPersonne
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteActioncandidatPersonneEnattente';
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 * 
		 * @var array
		 */
		public $cohorteFields = array(
			'ActioncandidatPersonne.id' => array( 'type' => 'hidden' ),
			'ActioncandidatPersonne.selection' => array( 'type' => 'checkbox' ),
			'ActioncandidatPersonne.bilanvenu' => array( 'type' => 'radio', 'legend' => false ),
			'ActioncandidatPersonne.bilanretenu' => array( 'type' => 'radio', 'legend' => false ),
			'ActioncandidatPersonne.infocomplementaire' => array( 'type' => 'textarea' ),
			'ActioncandidatPersonne.datebilan' => array( 'type' => 'date' ),
		);
		
		/**
		 * Logique de sauvegarde de la cohorte
		 * 
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['ActioncandidatPersonne']['selection'] === '0' ) {
					unset($data[$key]);
				}
			}
			
			$success = !empty($data) && $this->ActioncandidatPersonne->saveAll( $data );
			
			return $success;
		}
	}
?>