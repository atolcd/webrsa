<?php
	/**
	 * Code source de la classe DspRev.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe DspRev ...
	 *
	 * @package app.Model
	 */
	class DspRev extends AppModel
	{
		public $name = 'DspRev';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Allocatairelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Dsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'dsp_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libderact66Metier' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'libderact66_metier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libsecactderact66Secteur' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'libsecactderact66_secteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libactdomi66Metier' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'libactdomi66_metier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libsecactdomi66Secteur' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'libsecactdomi66_secteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libemploirech66Metier' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'libemploirech66_metier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Libsecactrech66Secteur' => array(
				'className' => 'Coderomesecteurdsp66',
				'foreignKey' => 'libsecactrech66_secteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			// Début ROME V3
			'Deractromev3Rev' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'deractromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Deractdomiromev3Rev' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'deractdomiromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Actrechromev3Rev' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'actrechromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			// Fin ROME V3
		);

		public $hasMany = array(
			'DetaildifsocRev' => array(
				'className' => 'DetaildifsocRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailaccosocfamRev' => array(
				'className' => 'DetailaccosocfamRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailaccosocindiRev' => array(
				'className' => 'DetailaccosocindiRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetaildifdispRev' => array(
				'className' => 'DetaildifdispRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailnatmobRev' => array(
				'className' => 'DetailnatmobRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetaildiflogRev' => array(
				'className' => 'DetaildiflogRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailmoytransRev' => array(
				'className' => 'DetailmoytransRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetaildifsocproRev' => array(
				'className' => 'DetaildifsocproRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailprojproRev' => array(
				'className' => 'DetailprojproRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailfreinformRev' => array(
				'className' => 'DetailfreinformRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'DetailconfortRev' => array(
				'className' => 'DetailconfortRev',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Dsp\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Populationb3pdv93' => array(
				'className' => 'Populationb3pdv93',
				'foreignKey' => 'dsp_rev_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		* Permet de récupérer les dernières DSP d'une personne, en attendant l'index unique sur personne_id
		*/

		public function sqDerniere( $personneIdFied = 'Personne.id' ) {
			return $this->sq(
				array(
					'alias' => 'tmp_dsps_revs',
					'fields' => array(
						'tmp_dsps_revs.id'
					),
					'conditions' => array(
						"tmp_dsps_revs.personne_id = {$personneIdFied}"
					),
					'order' => 'tmp_dsps_revs.modified DESC',
					'limit' => 1
				)
			);
		}

		/**
		 * Retourne un querydata contenant tous les champs et les associations à
		 * utiliser dans les pages de visualisation d'une DspRev, dans la page
		 * d'historique des DspRev, dans la page de différences entre deux versions
		 * des DspRev.
		 *
		 * @deprecated since version 3.1
		 * @see WebrsaDsp::getViewQuery()
		 * @return array
		 */
		public function getViewQuery() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => $this->fields(),
					'contain' => array(
						'Personne',
						'DetaildifsocRev',
						'DetailaccosocfamRev',
						'DetailaccosocindiRev',
						'DetaildifdispRev',
						'DetailnatmobRev',
						'DetaildiflogRev',
						'DetailmoytransRev',
						'DetaildifsocproRev',
						'DetailprojproRev',
						'DetailfreinformRev',
						'DetailconfortRev',
						'Fichiermodule'
					),
					'joins' => array()
				);

				foreach( array_keys( $this->belongsTo ) as $alias ) {
					if( in_array( $alias, $query['contain'] ) ) {
						$query['fields'] = array_merge( $query['fields'], $this->{$alias}->fields() );
					}
					// Codes ROME V2
					else if( preg_match( '/66(Metier|Secteur)/', $alias ) ) {
						$key = array_search( "{$this->alias}.{$this->belongsTo[$alias]['foreignKey']}", $query['fields'] );
						if( $key !== -1 ) {
							unset( $query['fields'][$key] );
						}

						$field = $this->{$alias}->getVirtualField( 'intitule' );
						$query['fields'][] = "( {$field} ) \"{$alias}__intitule\"";
						$query['joins'][] = $this->join( $alias, array( 'type' => 'LEFT OUTER' ) );
					}
				}

				if( Configure::read( 'Romev3.enabled' ) ) {
					foreach( $this->Dsp->WebrsaDsp->romev3LinkedModels as $alias ) {
						$aliasRev = "{$alias}Rev";
						$replacements = array();

						$query['joins'][] = $this->join( $aliasRev );

						$fields = array(  );
						foreach( $this->Dsp->WebrsaDsp->suffixesRomev3 as $suffix ) {
							$prefix = preg_replace( '/^(.*)romev3Rev$/', "\\1", $aliasRev );

							$linked = Inflector::camelize( "{$suffix}romev3" );
							$linkedAlias = "{$prefix}{$suffix}romev3Rev";
							$replacements[$linked] = $linkedAlias;

							$query['joins'][] = array_words_replace( $this->{$aliasRev}->join( $linked ), $replacements );

							switch( $suffix ) {
								case 'famille':
									$fields[] = "( \"{$linkedAlias}\".\"code\" || ' - ' || \"{$linkedAlias}\".\"name\" ) AS \"{$linkedAlias}__name\"";
									break;
								case 'domaine':
									$fields[] = "( \"{$prefix}familleromev3Rev\".\"code\" || \"{$linkedAlias}\".\"code\" || ' - ' || \"{$linkedAlias}\".\"name\" ) AS \"{$linkedAlias}__name\"";
									break;
								case 'metier':
									$fields[] = "( \"{$prefix}familleromev3Rev\".\"code\" || \"{$prefix}domaineromev3Rev\".\"code\" || \"{$linkedAlias}\".\"code\" || ' - ' || \"{$linkedAlias}\".\"name\" ) AS \"{$linkedAlias}__name\"";
									break;
								case 'appellation':
									$fields[] = "{$linkedAlias}.name";
									break;
							}
						}
						$query['fields'] = Hash::merge( $query['fields'], $fields );
					}
				}

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Permet d'obtenir les différences entre deux versions des DspRev obtenues
		 * grâce au query se trouvant dans la méthode getViewQuery().
		 *
		 * @deprecated since version 3.1
		 * @see WebrsaDsp::getDiffs()
		 * @param array $old
		 * @param array $new
		 * @return array
		 */
		public function getDiffs( $old, $new ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			$return = array();

			// Suppression des champs de clés primaires et étrangères des résultats des Dsps actuelles
			foreach( $new as $Model => $values ) {
				if( $Model != 'DspRev' && preg_match( '/Rev$/', $Model ) ) {
					foreach( $new[$Model] as $key1 => $value1 ) {
						if( is_array( $new[$Model][$key1] ) ) {
							$new[$Model][$key1] = Hash::remove( $new[$Model][$key1], "id" );
							$new[$Model][$key1] = Hash::remove( $new[$Model][$key1], "dsp_rev_id" );
						}
					}
				}
			}

			// Suppression des champs de clés primaires et étrangères des résultats des Dsps précédentes
			foreach( $old as $Model => $values ) {
				if( $Model != 'DspRev' && preg_match( '/Rev$/', $Model ) ) {
					foreach( $old[$Model] as $key2 => $value2 ) {
						if( is_array( $old[$Model][$key2] ) ) {
							$old[$Model][$key2] = Hash::remove( $old[$Model][$key2], "id" );
							$old[$Model][$key2] = Hash::remove( $old[$Model][$key2], "dsp_rev_id" );
						}
					}
				}
			}

			// Suppression des champs de clés primaires et étrangères des codes ROME V3 liés
			if( Configure::read( 'Romev3.enabled' ) ) {
				foreach( $this->Dsp->WebrsaDsp->romev3LinkedModels as $alias ) {
					$foreignKey = Inflector::underscore( $alias ).'_id';
					unset( $old["DspRev"][$foreignKey] );
					unset( $new["DspRev"][$foreignKey] );

					foreach( array_keys( $this->Dsp->Deractromev3->schema() ) as $fieldName ) {
						unset( $old["{$alias}Rev"][$fieldName] );
						unset( $new["{$alias}Rev"][$fieldName] );
					}
				}
			}

			// -----------------------------------------------------------------

			foreach( $new as $Model => $values ) {
				$return[$Model] = Set::diff( $new[$Model], $old[$Model] );
				unset( $return[$Model]['id'] );
				unset( $return[$Model]['created'] );
				unset( $return[$Model]['modified'] );

				if( $Model != 'DspRev' && !empty( $new[$Model] ) && !empty( $return[$Model] ) && preg_match( '/Rev$/', $Model ) ) {
					foreach( $new[$Model] as $key1 => $value1 ) {
						foreach( $old[$Model] as $key2 => $value2 ) {
							$compare = Set::diff( $value1, $value2 );
							if( empty( $compare ) && ($key1 != $key2) ) {
								$return[$Model] = Hash::remove( $return[$Model], $key1 );
							}
						}
					}
				}

				if( empty( $return[$Model] ) ) {
					$return = Hash::remove( $return, $Model );
				}
			}

			// Suppression des fausses différences trouvées au niveau des libellés vides
			foreach( $this->Dsp->WebrsaDsp->getCheckboxes() as $alias => $params ) {
				if( $params['text'] !== false ) {
					$alias = "{$alias}Rev";
					$path = "{$alias}.{n}.{$params['text']}";

					if( Hash::extract( $return, $path ) === array( null ) ) {
						$return = Hash::remove( $return, $path );
					}
				}
				if( empty( $return[$Model] ) ) {
					$return = Hash::remove( $return, $Model );
				}
			}

			return $return;
		}

		public function sqHeberge( $personne_id = 'Personne.id' ) {
			$sq = $this->sq(
				array(
					'alias' => '"dspsrev2"',
					'fields' => array(
						'dspsrev1.id'
					),
					'conditions' => array(
						'dspsrev2.personne_id = dspsrev1.personne_id',
						'dspsrev2.id != dspsrev1.id',
						'dspsrev2.created < dspsrev1.created',
						'dspsrev2.natlog !=' => '0909',
					),
					'order' => array(
						'dspsrev2.created' => 'DESC'
					),
					'limit' => 1
				)
			);
			$query = array(
				'alias' => '"dspsrev1"',
				'fields' => array(
					'dspsrev1.id'
				),
				'joins' => array(
					array(
						'alias' => '"pers"',
						'table' => 'personnes',
						'conditions' => array(
							'pers.id = dspsrev1.personne_id'
						),
						'type' => 'INNER'
					),
					array(
						'alias' => '"foy"',
						'table' => 'foyers',
						'conditions' => array(
							'foy.id = pers.foyer_id'
						),
						'type' => 'INNER'
					),
					array(
						'alias' => '"adrfoyer"',
						'table' => 'adressesfoyers',
						'conditions' => array(
							'adrfoyer.foyer_id = foy.id',
							'adrfoyer.rgadr' => '01',
							'OR' => array(
								'adrfoyer.dtemm IS NULL',
								'adrfoyer.dtemm < dspsrev1.created'
							)
						),
						'type' => 'INNER'
					)
				),
				'conditions' => array(
					'dspsrev1.personne_id = ' . $personne_id,
					'dspsrev1.natlog' => '0909',
					"EXISTS({$sq})"
				),
				'order' => array(
					'dspsrev1.created' => 'ASC'
				),
				'limit' => 1
			);

			$sq = preg_replace('/AS ([\w]+) /', 'AS "$1" ', $this->sq($query));

			return $sq;
		}
	}
?>
