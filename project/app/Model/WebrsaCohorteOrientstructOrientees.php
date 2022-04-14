<?php
	/**
	 * Code source de la classe WebrsaCohorteOrientstructOrientees.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohorteOrientstruct', 'Model' );

	/**
	 * La classe WebrsaCohorteOrientstructOrientees ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteOrientstructOrientees extends WebrsaAbstractCohorteOrientstruct
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteOrientstructOrientees';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryOrientsstructs.cohorte_orientees.fields',
			'ConfigurableQueryOrientsstructs.cohorte_orientees.innerTable'
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

			$query = parent::searchQuery( $types, $baseModelName, false );

			if( !in_array( Configure::read( 'Cg.departement' ), array( 66, 976 ) ) ) {
				$Pdf = ClassRegistry::init( 'Pdf' );
				$sq = array(
					'fields' => array( 'Pdf.fk_value' ),
					'contain' => false,
					'recursive' => -1,
					'conditions' => array(
						'Pdf.fk_value = Orientstruct.id',
						'Pdf.modele' => 'Orientstruct'
					)
				);
				$query['conditions'][] = 'Orientstruct.id IN ( '.$Pdf->sq( $sq ).' )';
				$query['conditions'] = array_words_replace( $query['conditions'], array( 'Pdf' => 'pdfs' ) );
			}

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

			// Filtrer par impression
			$impression = (string)Hash::get( $search, 'Orientstruct.impression' );
			if( $impression === '1' ) {
				$query['conditions'][] = 'Orientstruct.date_impression IS NOT NULL';
			}
			else if( $impression === '0' ) {
				$query['conditions'][] = 'Orientstruct.date_impression IS NULL';
			}

			$query['conditions'] = $this->conditionsDates(
				$query['conditions'],
				$search,
				array( 'Orientstruct.date_valid', 'Orientstruct.date_impression' )
			);

			foreach( array( 'typeorient_id', 'origine', 'structureorientante_id' ) as $field ) {
				$value = (string)Hash::get( $search, "Orientstruct.{$field}" );
				if( $value !== '' ) {
					$query['conditions']["Orientstruct.{$field}"] = $value;
				}
			}

			return $query;
		}
	}
?>