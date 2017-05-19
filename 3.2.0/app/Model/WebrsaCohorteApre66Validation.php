<?php
	/**
	 * Code source de la classe WebrsaCohorteApre66Validation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteApre66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteApre66Validation ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteApre66Validation extends AbstractWebrsaCohorteApre66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteApre66Validation';

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Aideapre66.id' => array( 'type' => 'hidden' ),
			'Aideapre66.montantpropose' => array( 'type' => 'hidden' ),
			'Aideapre66.datemontantpropose' => array( 'type' => 'hidden' ),
			'Aideapre66.typeaideapre66_id' => array( 'type' => 'hidden' ),
			'Apre66.personne_id' => array( 'type' => 'hidden' ),
			'Apre66.selection' => array( 'type' => 'checkbox' ),
			'Aideapre66.decisionapre' => array( 'value' => 'ACC' ),
			'Aideapre66.montantaccorde' => array( 'type' => 'text' ),
			'Aideapre66.motifrejetequipe' => array( 'type' => 'textarea' ),
			'Aideapre66.datemontantaccorde' => array( 'type' => 'date' ),
		);

		/**
		 * Préremplissage du formulaire en cohorte
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = parent::prepareFormDataCohorte($results, $params);
			for ($i=0; $i<count($results); $i++) {
				$data[$i]['Aideapre66']['montantaccorde'] = $data[$i]['Aideapre66']['montantpropose'];
			}

			return $data;
		}

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
				if ( $value['Apre66']['selection'] === '0' ) {
					unset($data[$key]);
				}

				// Si rejet, on retire le montant accordé
				elseif ( Hash::get($value, 'Aideapre66.decisionapre') === 'REF' ) {
					unset($data[$key]['Aideapre66']['montantaccorde']);
				}

				// Si accord, on retire le commentaire de rejet
				else {
					unset($data[$key]['Aideapre66']['motifrejetequipe']);
				}
				
				unset($data[$key]['Aideapre66']['montantpropose']);
			}

			$success = !empty($data) && $this->Apre66->Aideapre66->saveAll( $data );

			return $success;
		}
	}
?>