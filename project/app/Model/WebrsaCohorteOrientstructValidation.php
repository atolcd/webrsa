<?php
	/**
	 * Code source de la classe WebrsaCohorteOrientstructValidation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohorteOrientstruct', 'Model' );

	/**
	 * La classe WebrsaCohorteOrientstructValidation ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteOrientstructValidation extends WebrsaAbstractCohorteOrientstruct
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteOrientstructValidation';

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Orientstruct.selection' => array( 'type' => 'checkbox' ),
			'Orientstruct.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.propo_algo' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.origine' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.structureorientante_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.statut_orient' => array( 'type' => 'radio', 'fieldset' => false, 'legend' => false, 'div' => false ),
		);

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryOrientsstructs.cohorte_validation.fields',
			'ConfigurableQueryOrientsstructs.cohorte_validation.innerTable'
		);

		/**
		 * Spécifie le statut_orient pour cette cohorte-ci puisqu'on sous-classe.
		 *
		 * @see WebrsaAbstractCohorteOrientstruct::searchQuery()
		 *
		 * @var string
		 */
		public $statut_orient = 'En attente';

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchQuery( array $types = array(), $baseModelName = 'Personne', $forceBeneficiaire = true ) {
			$types += array(
				'Prestation' => 'LEFT OUTER',
				'Calculdroitrsa' => 'LEFT OUTER',
				'Dsp' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Suiviinstruction' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Orientstruct' => 'INNER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER'
			);
			$query = parent::searchQuery( $types, $baseModelName, $forceBeneficiaire );

			// Ajout des champs / jointures liées aux structures orientantes
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Orientstruct.structureorientante_id',
					'Structureorientante.lib_struc',
					'Referentorientant.nom_complet',
					'Referent.nom_complet'
				)
			);

			$query['joins'] = array_merge(
				$query['joins'],
				array(
					$this->Personne->Orientstruct->join( 'Referent', array( 'type' => 'LEFT' ) ),
					array(
						'table' => 'structuresreferentes',
						'alias' => 'Structureorientante',
						'type' => 'INNER',
						'conditions' => array(
							'Structureorientante.id = Orientstruct.structureorientante_id',
						),
					),
					array(
						'table' => 'referents',
						'alias' => 'Referentorientant',
						'type' => 'LEFT',
						'conditions' => array(
							'Referentorientant.id = Orientstruct.referentorientant_id',
						),
					)
				)
			);

			// Tri des joins pour mettre les INNER en premier
			$innerList = array();
			$leftList = array();
			foreach($query['joins'] as $key => $join) {
				if(strpos($join['type'], 'INNER') !== false) {
					$innerList[] = $join;
				} else {
					$leftList[] = $join;
				}
			}
			$query['joins'] = array_merge($innerList, $leftList);

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = parent::searchConditions( $query, $search );

			// Gestion des conditions de proposition d'orientation
			$origine = Hash::get( $search, 'Orientstruct.origine' );
			if(!empty($origine)) {
				$query['conditions'][] = array('Orientstruct.origine' => $origine);
			} else {
				$query['conditions'][] = array('Orientstruct.origine' => Configure::read('Orientation.validation.listeorigine'));
			}

			$struct = Hash::get( $search, 'Orientstruct.structureorientante_id' );
			if(!empty($struct)) {
				$query['conditions'][] = array(
					'Orientstruct.structureorientante_id' => $struct
				);
			} else {
				// On ajoute les conditions liées aux structures référentes qui ont le workflow de validation d'activer uniquement
				$listStruct = $this->Personne->Orientstruct->Structurereferente->listeStructWorkflow();

				if(!empty($listStruct)) {
					$query['conditions'][] = array(
						'Orientstruct.structureorientante_id IN' => $listStruct
					);
				}
			}

			$has_date = Hash::get( $search, 'Orientstruct.date_propo' );
			if($has_date == 1) {
				$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Orientstruct.date_propo' );
			}

			return $query;
		}

		/**
		 * Enregistrement du formulaire de cohorte: si on a choisi "Valider",
		 * l'orientation sera effective, sinon, l'orientation sera refusé
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$this->loadModel('Orientstruct');
			$success = true;

			foreach( array_keys( $data ) as $key ) {
				// Suppression des lignes non sélectionnées
				if( $data[$key]['Orientstruct']['selection'] == 0 ) {
					unset($data[$key]);
					continue;
				}

				// Ajout d'informations si besoin
				if( $data[$key]['Orientstruct']['statut_orient'] === 'Orienté' ) {
					$data[$key]['Orientstruct']['user_id'] = $user_id;
					$data[$key]['Orientstruct']['date_valid'] = date( 'Y-m-d' );
				}
				else {
					$data[$key]['Orientstruct']['date_valid'] = null;
					$data[$key]['Orientstruct']['user_id'] = null;
				}
			}
			$this->Orientstruct->begin();
			$success = !empty($data) && $this->Orientstruct->saveAll($data, array('atomic' => false));

			if ($success) {
				$this->Orientstruct->commit();
			} else {
				$this->Orientstruct->rollback();
			}
			return $success;
		}
	}
