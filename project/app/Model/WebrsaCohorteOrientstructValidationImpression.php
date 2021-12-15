<?php
	/**
	 * Code source de la classe WebrsaCohorteOrientstructValidationImpression.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohorteOrientstructOrientees', 'Model' );

	/**
	 * La classe WebrsaCohorteOrientstructValidationImpression ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteOrientstructValidationImpression extends WebrsaCohorteOrientstructOrientees
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteOrientstructValidationImpression';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQuery.Orientsstructs.cohorte_orientees_validees.fields',
			'ConfigurableQuery.Orientsstructs.cohorte_orientees_validees.innerTable'
		);

		/**
		 * Spécifie le statut_orient pour cette cohorte-ci puisqu'on sous-classe.
		 *
		 * @see WebrsaAbstractCohorteOrientstruct::searchQuery()
		 *
		 * @var string
		 */
		public $statut_orient = 'Orienté';

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array(), $baseModelName = 'Personne', $forceBeneficiaire = true ) {
			$query = parent::searchQuery( $types, $baseModelName, false );

			// Ajout des champs / jointures liées aux structures orientantes
			$query['fields'] = array_merge(
				$query['fields'],
				$this->Personne->Orientstruct->fields(),
				array(
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
			if(empty($origine)) {
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
	}
