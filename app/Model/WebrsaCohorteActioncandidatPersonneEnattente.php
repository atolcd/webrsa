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
		 * Règles de validation à utiliser dans la cohorte uniquement.
		 *
		 * bilanvenu, bilanretenu, infocomplementaire, datebilan
		 *
		 * @var array
		 */
		public $validateCohorte = array(
			'bilanvenu' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'allowEmpty' => false,
					'required' => true
				)
			)
		);

		/**
		 * Logique de sauvegarde de la cohorte.
		 *
		 * On remplace temporairement pour la validation les règles de validation
		 * du modèle ActioncandidatPersonne avec celles spécifiques à cette cohorte
		 * qui se trouvent dans l'attribut $validateCohorte.
		 *
		 * A cause de ce qui se trouve dans ActioncandidatPersonne::afterSave,
		 * on est obligés de compléter les données de la cohorte avec ce qui se
		 * trouve en base de données.
		 *
		 * @see ActioncandidatPersonne::beforeSave
		 * @see WebrsaActioncandidatPersonne::bilanAccueil
		 *
		 *
		 * @param array $data
		 * @param array $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['ActioncandidatPersonne']['selection'] === '0' ) {
					unset($data[$key]);
				}
			}

			$success = false === empty( $data );

			if( true === $success ) {
				// Validation spécifique à la cohorte
				$validate = $this->ActioncandidatPersonne->validate;
				foreach($this->validateCohorte as $fieldName => $rules) {
					$this->ActioncandidatPersonne->validate[$fieldName] = $rules
						+ $this->ActioncandidatPersonne->validate[$fieldName];
				}

				$success = $this->ActioncandidatPersonne->saveAll( $data, array( 'validate' => 'only' ) );
				$this->ActioncandidatPersonne->validate = $validate;

				// On complète les données avec ce que l'on a en base de données
				// pour enregistrer proprement (@see ActioncandidatPersonne::beforeSave)
				if( true === $success ) {
					$query = array(
						'contain' => false,
						'conditions' => array(
							'ActioncandidatPersonne.id' => Hash::extract($data, '{n}.ActioncandidatPersonne.id')
						)
					);
					$records = $this->ActioncandidatPersonne->find('all', $query);
					$records = Hash::combine($records, '{n}.ActioncandidatPersonne.id', '{n}');

					foreach($data as $key => $values) {
						$id = $values['ActioncandidatPersonne']['id'];
						$data[$key]['ActioncandidatPersonne'] = array_merge(
							$records[$id]['ActioncandidatPersonne'],
							$values['ActioncandidatPersonne']
						);
					}

					// Tentative d'enregistrement des données complétées
					$success = $this->ActioncandidatPersonne->saveAll( $data );
				}
			}

			return $success;
		}
	}
?>