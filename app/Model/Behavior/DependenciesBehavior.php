<?php
	/**
	 * Code source de la classe DependenciesBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe DependenciesBehavior fournit une règle de validation supplémentaire
	 * pour les relations qui ne sont pas en troisième forme normale.
	 *
	 * @package app.Model.Behavior
	 */
	class DependenciesBehavior extends ModelBehavior
	{
		/**
		 * Règle de validation permettant, lorsqu'une table A possède une clé
		 * étrangère vers une table B et vers une table C, sachant que la table
		 * B possède une clé étrangère vers la table C, de s'assurer que les valeurs
		 * des clés étrangères de la table A soient cohérentes.
		 *
		 * Ce qui signifie que le modèle en cours (modèle A) belongsTo modèle B
		 * ($alias1) et belongsTo modèle C ($alias2), tandis que le modèle
		 * B ($alias1) belogsTo modèle C ($alias3).
		 *
		 * Si l'une et/ou l'autre des clés étrangères sont nulles, alors il n'y
		 * a pas de problème de cohérence et la méthode retourne true.
		 *
		 * Il s'agit de relations ne respectant pas la troisième forme normale.
		 *
		 * @param Model $Model
		 * @param mixed $check
		 * @param string $alias1 L'alias du premier modèle qui est lié au modèle en
		 *	cours.
		 * @param string $alias2 L'alias du second modèle qui est lié au modèle en
		 *	cours.
		 * @param string $alias3 L'alias du modèle qui fait la liaison entre le
		 *	premier et le second modèle lié.
		 * @return boolean
		 */
		public function dependentForeignKeys( Model $Model, $check, $alias1, $alias2, $alias3 = null ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$alias3 = ( ( $alias3 === null || is_array( $alias3 ) ) ? $alias2 : $alias3 );

			$return = true;

			foreach( Hash::normalize( $check ) as $field => $value ) {
				if( !in_array( $value, array( '', null ), true ) ) {
					$alias1PrimaryKey = "{$alias1}.{$Model->{$alias1}->primaryKey}";
					$alias1ForeignKey = "{$alias1}.{$Model->{$alias1}->belongsTo[$alias3]['foreignKey']}";

					$alias2Value = Hash::get( $Model->data, "{$Model->alias}.{$Model->belongsTo[$alias2]['foreignKey']}" );

					if( !in_array( $alias2Value, array( '', null ), true ) ) {
						$query = array(
							'fields' => array( $alias1PrimaryKey ),
							'recursive' => -1,
							'contain' => false,
							'conditions' => array(
								$alias1PrimaryKey => $value,
								$alias1ForeignKey => $alias2Value
							),
							'order' => array()
						);

						$result = $Model->{$alias1}->find( 'first', $query );
						$return = ( Hash::get( (array)$result, $alias1PrimaryKey ) !== null ) && $return;
					}
				}
			}

			return $return;
		}

	}
?>