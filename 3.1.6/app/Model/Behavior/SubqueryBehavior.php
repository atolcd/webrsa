<?php
	/**
	 * Subquery behavior class.
	 *
	 * Utility class to generate subqueries using a CakePHP find-like operation.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Behavior that generates a clean subquery string from a $queryData array.
	 *
	 * @package app.Model.Behavior
	 */
	class SubqueryBehavior extends ModelBehavior
	{
		/**
		* Échappe les Model.field suivant le SGBD.
		*
		* @param AppModel $model
		* @param array $array
		* @return array
		*/

		protected function _quote( Model $model, $array ) {
			if( empty( $array ) ) {
				return array();
			}

			$dbo = $model->getDataSource();

			$array = (array)$array;
			//$pattern = "/^(?<!\w)(\w+)\.{0,1}(\w+)(?!\w)(.*)$/";
			//$replacement = "{$dbo->startQuote}\\1{$dbo->endQuote}.{$dbo->startQuote}\\2{$dbo->endQuote} \\3";
			$pattern = "/^(.*)(?<!\w)(\w+)\.(\w+)(?!\w)(.*)$/";
			$replacement = "\\1{$dbo->startQuote}\\2{$dbo->endQuote}.{$dbo->startQuote}\\3{$dbo->endQuote}\\4";

			$keys = array_keys( $array );
			$values = array_values( $array );

			$keys = preg_replace( $pattern, $replacement, $keys );
			$values = preg_replace( $pattern, $replacement, $values );

			return array_combine( $keys, $values );
		}

		/**
		* Transforme les $queryData d'un appel "find all" en requête SQL,
		* ce qui permet de faire des sous-requêtes moins dépendantes du SGBD.
		*
		* Les fields sont échappés.
		*
		* INFO: http://book.cakephp.org/view/74/Complex-Find-Conditions (Sub-queries)
		* FIXME: détecter si l'alias est un nom de modèle ou un nom de table et mettre entre
		* côtes s'il s'agit du modèle
		*
		* @param AppModel $model
		* @param array $queryData
		* @return string
		*/

		public function sq( Model $model, $queryData ) {
			$dbo = $model->getDataSource();

			$defaults = array(
				'fields' => null,
				'order' => null,
				'group' => null,
				'limit' => null,
				'table' => $dbo->fullTableName( $model, true, false ),
				'alias' => $model->alias,
				'conditions' => array(),
			);

			$queryData = Set::merge( $defaults, Hash::filter( (array)$queryData ) );

			// PostgreSQL CakePHP 1.3.4: order & group OK
			$queryData['fields'] = $this->_quote( $model, $queryData['fields'] );

			// INFO: avec PostreSQL -> AS "Foo__bar"
// 			$queryData['fields'] = $dbo->fields( $model, null, $queryData['fields'], true );
// debug( $queryData['fields'] );

			/*if( isset( $queryData['joins'] ) && !empty( $queryData['joins'] ) )
			foreach( $queryData['joins'] as $key => $join ) {
				if( !isset( $join['table'] ) ) {
					if( isset( $join['model'] ) ) {
						$join['table'] = Inflector::tableize( $join['model'] );
					}
					else {
						$join['table'] = Inflector::tableize( $join['alias'] );
					}
				}

				$join['table'] = $dbo->fullTableName( $join['table'], true, false );
				$queryData['joins'][$key] = $join;
			}*/

			return $dbo->buildStatement( $queryData, $model );
		}
	}
?>