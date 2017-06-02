<?php
	/**
	 * Code source de la classe WebrsaCohorteApre66Transfert.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteApre66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteApre66Transfert ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteApre66Transfert extends AbstractWebrsaCohorteApre66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteApre66Transfert';
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 * 
		 * @var array
		 */
		public $cohorteFields = array(
			'Aideapre66.id' => array( 'type' => 'hidden' ),
			'Apre66.personne_id' => array( 'type' => 'hidden' ),
			'Apre66.nb_fichiers_lies' => array( 'type' => 'hidden' ),
			'Apre66.id' => array( 'type' => 'text', 'style' => 'display: none;', 'before' => '<input type="button" value="Rafraîchir" class="refresh"/>' ),
			'Apre66.istransfere' => array( 'type' => 'checkbox' ),
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
				$data[$key]['Apre66']['etatdossierapre'] = 'TRA';
				
				// Si non selectionné, on retire tout
				if ( $value['Apre66']['istransfere'] === '0' ) {
					unset($data[$key]);
				}
				else {
					unset($data[$key]['Apre66']['nb_fichiers_lies']);
				}
			}
			
			$success = !empty($data) && $this->Apre66->saveAll( $data );
			
			return $success;
		}
	}
?>