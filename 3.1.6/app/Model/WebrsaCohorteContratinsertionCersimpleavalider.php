<?php
	/**
	 * Code source de la classe WebrsaCohorteContratinsertionCersimpleavalider.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteContratinsertion', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteContratinsertionCersimpleavalider ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteContratinsertionCersimpleavalider extends AbstractWebrsaCohorteContratinsertion
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteContratinsertionCersimpleavalider';
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Contratinsertion.id' => array( 'type' => 'hidden' ),
			'Contratinsertion.personne_id' => array( 'type' => 'hidden' ),
			'Contratinsertion.selection' => array( 'type' => 'checkbox' ),
			'Contratinsertion.decision_ci',
			'Contratinsertion.datedecision' => array( 'type' => 'date' ),
			'Contratinsertion.observ_ci' => array( 'type' => 'textarea' ),
			'Propodecisioncer66.id' => array( 'type' => 'hidden' ),
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
			$propodecision = array();
			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['Contratinsertion']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}
				
				if (in_array($value['Contratinsertion']['decision_ci'], array('V', 'N'))) {
					$propodecision[$key]['Propodecisioncer66']['isvalidcer'] = $value['Contratinsertion']['decision_ci'] === 'V' 
						? 'O' : 'N'
					;
					$propodecision[$key]['Propodecisioncer66'] += array(
						'datevalidcer' => $value['Contratinsertion']['datedecision'],
						'contratinsertion_id' => $value['Contratinsertion']['id']
					);
				}
			}
			
			$success = !empty($data) && $this->Contratinsertion->saveAll( $data )
				&& (empty($propodecision) || $this->Contratinsertion->Propodecisioncer66->saveAll( $propodecision ))
			;
			
			return $success;
		}
	}
?>