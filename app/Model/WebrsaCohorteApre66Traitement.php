<?php
	/**
	 * Code source de la classe WebrsaCohorteApre66Traitement.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteApre66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteApre66Traitement ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteApre66Traitement extends AbstractWebrsaCohorteApre66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteApre66Traitement';
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 * 
		 * @var array
		 */
		public $cohorteFields = array(
			'Aideapre66.id' => array( 'type' => 'hidden' ),
			'Apre66.personne_id' => array( 'type' => 'hidden' ),
			'Apre66.id' => array( 'type' => 'hidden' ),
			'Apre66.istraite' => array( 'type' => 'checkbox' ),
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
				if ( $value['Apre66']['istraite'] === '0' ) {
					unset($data[$key]);
				}
			}
			
			$success = !empty($data) && $this->Apre66->saveAll( $data );
			
			return $success;
		}
	}
?>