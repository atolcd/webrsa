<?php
	/**
	 * Code source de la classe LinkedRecordsBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe LinkedRecordsBehavior ...
	 *
	 * @todo rechercher toutes les tables ayant un personne_id
	 * => SELECT table_name, column_name FROM information_schema.columns WHERE column_name = 'personne_id' ORDER BY table_name;
	 * @todo dans le plugin Pgsqlcake car ça ne fonctionne qu'avec Postgresql (à vérifier) ?
	 *
	 * Exemples, dans la recherche par dossier/allocataire:
	 *	Exemple 1:
	 *		$this->Foyer->Personne->Behaviors->attach( 'LinkedRecords' );
	 *		$this->forceVirtualFields = true;
	 *
	 *		$vfName = $this->Foyer->Personne->linkedRecordVirtualFieldName( 'Contratinsertion' );
	 *		$this->Foyer->Personne->virtualFields[$vfName] = $this->Foyer->Personne->linkedRecordVirtualField( 'Contratinsertion' );
	 *
	 *		$vfName = $this->Foyer->Personne->linkedRecordVirtualFieldName( 'PersonneReferent' );
	 *		$this->Foyer->Personne->virtualFields[$vfName] = $this->Foyer->Personne->linkedRecordVirtualField( 'PersonneReferent', array( 'conditions' => array( 'PersonneReferent.dfdesignation IS NULL' ) ) );
	 *		$conditions[] = "NOT Personne.{$vfName}"; // INFO: NOT ou pas NOT :)
	 *
	 *	Exemple 2:
	 *		$this->Foyer->Personne->Behaviors->attach( 'LinkedRecords' );
	 *		$this->forceVirtualFields = true;
	 *		$this->Foyer->Personne->linkedRecordsLoadVirtualFields(
	 *			array(
	 *				'Contratinsertion' => array( 'conditions' => array( 'Contratinsertion.decision_ci' => 'V' ) ),
	 *				'PersonneReferent' => array( 'conditions' => array( 'PersonneReferent.dfdesignation IS NULL' ) ),
	 *				'Orientstruct' => array( 'conditions' => array( 'Orientstruct.statut_orient' => 'Orienté' ) ),
	 *			)
	 *		);
	 *		$conditions[] = 'Personne.has_contratinsertion';
	 *		$conditions[] = 'NOT Personne.has_personne_referent';
	 *		conditions[] = 'Personne.has_orientstruct';
	 *
	 * @package app.Model.Behavior
	 */
	class LinkedRecordsBehavior extends ModelBehavior
	{
		/**
		 * La masque du format des champs virtuels.
		 *
		 * @var string
		 */
		public $fieldNameFormat = 'has_%s';

		/**
		 * Retourne le nom du champ virtuel qui sera utilisé pour un modèle lié
		 * donné.
		 *
		 * @param Model $Model
		 * @param string $linkedModelName
		 * @return string
		 */
		public function linkedRecordVirtualFieldName( Model $Model, $linkedModelName ) {
			$linkedModelName = Inflector::classify( $linkedModelName );
			$tableName = Inflector::tableize( $linkedModelName );
			return sprintf( $this->fieldNameFormat, Inflector::singularize( $tableName ) );
		}

		/**
		 * Complète la partie fields d'un querydata avec les champs virtuels sur
		 * les enregistrements liés passés en paramètre.
		 *
		 * @param Model $Model
		 * @param array $querydata
		 * @param array|string $links
		 * @return array
		 */
		public function linkedRecordsCompleteQuerydata( Model $Model, array $querydata, $links ) {
			$links = Hash::normalize( (array)$links );

			foreach( $links as $linkedModelName => $linkQuerydata ) {
				$virtualFieldName = $this->linkedRecordVirtualFieldName( $Model, $linkedModelName );
				$virtualField = $this->linkedRecordVirtualField( $Model, $linkedModelName, $linkQuerydata );

				$querydata['fields'][] = "( {$virtualField} ) AS \"{$Model->alias}__{$virtualFieldName}\"";
			}

			return $querydata;
		}

		/**
		 * Complète l'attribut virtualFields du modèle avec les champs virtuels
		 * sur les enregistrements liés passés en paramètre.
		 *
		 * @param Model $Model
		 * @param array|string $links
		 */
		public function linkedRecordsLoadVirtualFields( Model $Model, $links ) {
			$links = Hash::normalize( (array)$links );

			foreach( $links as $linkedModelName => $linkQuerydata ) {
				$virtualFieldName = $this->linkedRecordVirtualFieldName( $Model, $linkedModelName );
				$virtualField = $this->linkedRecordVirtualField( $Model, $linkedModelName, $linkQuerydata );

				$Model->virtualFields[$virtualFieldName] = $virtualField;
			}
		}

		/**
		 * Retourne la requête SQL à utiliser en tant que champ virtuel pour un
		 * modèle lié donné. Des conditions et des jointures sont possibles dans
		 * le querydata.
		 *
		 * @param Model $Model
		 * @param string $modelName
		 * @param array $querydata
		 * @param boolean $aliasJoins
		 * @return string
		 */
		public function linkedRecordVirtualField( Model $Model, $modelName, $querydata = null, $aliasJoins = true ) {
			$tableName = Inflector::tableize( $modelName );
			$replacements = array( $modelName => $tableName );
			$conditions = (array)Hash::get( (array)$querydata, 'conditions' );
			$joins = (array)Hash::get( (array)$querydata, 'joins' );

			$join = $Model->join( $modelName );
			$conditions[] = $join['conditions'];

			$qdSubquery = array(
				'alias' => $tableName,
				'fields' => array( "{$tableName}.{$Model->{$modelName}->primaryKey}" ),
				'conditions' => $conditions,
				'contain' => false,
				'joins' => array(),
			);

			if( !empty( $joins ) && $aliasJoins ) {
				foreach( $joins as $join ) {
					$tableName = Inflector::tableize( $join['alias'] );
					$replacements[$join['alias']] = $tableName;
					$qdSubquery['joins'][] = $join;
				}
			}

			$qdSubquery = array_words_replace( $qdSubquery, $replacements );

			$subquery = $Model->{$modelName}->sq( $qdSubquery );

			// Permet de remplacer les mentions à {$__cakeID__$} dans la sous-requête
			$Dbo = $Model->getDataSource();
			$cakeId = array_words_replace( array( "{$Dbo->startQuote}{$Model->alias}{$Dbo->endQuote}.{$Dbo->startQuote}{$Model->primaryKey}{$Dbo->endQuote}" ), $replacements );
			$subquery = str_replace( '{$__cakeID__$}', $cakeId[0], $subquery );

			return "EXISTS( {$subquery} )";
		}
	}
?>