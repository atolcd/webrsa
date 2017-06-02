<?php
	/**
	 * Code source de la classe DossierCommissionBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe DossierCommissionBehavior contient des méthodes communes aux
	 * classes Dossiercov58 et Dossierep.
	 *
	 * @todo getThematiquesReorientations()
	 *
	 * @package app.Model.Behavior
	 */
	class DossierCommissionBehavior extends ModelBehavior
	{
		/**
		 * Complète un querydata avec les jointures (LEFT OUTER) et les champs
		 * nécessaires pour connaître les propositions de Typeorient,
		 * Structurereferente et Referent contenues dans le modèle de la thématique.
		 *
		 * @param Model $Model
		 * @param array $query
		 * @return array
		 */
		public function getCompletedQueryDetailsOrientstruct( Model $Model, array $query ) {
			$details = array(
				'fields' => array(),
				'joins' => array(),
				'conditions' => Hash::normalize(
					array(
						'Typeorient',
						'Structurereferente',
						'Referent'
					)
				)
			);

			foreach( $Model->getThematiquesReorientations() as $thematique ) {
				$thematique = Inflector::classify( $thematique );
				$fields = $Model->{$thematique}->fields();

				//$query['fields'] = array_merge( $query['fields'], $fields ); // TODO: on pourra l'enlever
				$query['fields'][] = "{$Model->{$thematique}->alias}.{$Model->{$thematique}->primaryKey}";
				$query['joins'][] = $Model->join( $thematique, array( 'type' => 'LEFT OUTER' ) );

				// La thématique a-t'elle une proposition d'orientation
				if( in_array( "{$thematique}.typeorient_id", $fields ) ) {
					foreach( array_keys( $details['conditions'] ) as $modelDetail ) {
						if( isset( $Model->{$thematique}->{$modelDetail} ) ) {
							$displayField = $Model->{$thematique}->{$modelDetail}->displayField;
							$details['fields']["{$modelDetail}.{$displayField}"] = null;

							$join = $Model->{$thematique}->join( $modelDetail, array( 'type' => 'LEFT OUTER' ) );
							$details['joins'][$modelDetail] = $join;
							$details['conditions'][$modelDetail]['OR'][] = (array)$join['conditions'];
						}
					}
				}
			}

			// Ajout des champs et des jointures
			$query['fields'] = array_merge( $query['fields'], array_keys( $details['fields'] ) );
			foreach( $details['conditions'] as $alias => $conditions ) {
				if( isset( $details['joins'][$alias] ) ) {
					$join = $details['joins'][$alias];
					$join['conditions'] = $conditions;
					$query['joins'][] = $join;
				}
			}

			return $query;
		}
	}
?>