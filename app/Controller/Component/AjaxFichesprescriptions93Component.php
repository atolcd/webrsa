<?php
	/**
	 * Code source de la classe AjaxFichesprescriptions93Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AjaxComponent', 'Controller/Component' );

	/**
	 * La classe AjaxFichesprescriptions93Component ...
	 *
	 * @package app.Controller.Component
	 */
	class AjaxFichesprescriptions93Component extends AjaxComponent
	{
		/**
		 * Contrôleur utilisant ce component.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( );

		/**
		 * Lazy loading du modèle de fiche de prescription.
		 *
		 * @param string $name
		 * @return mixed
		 */
		public function __get( $name ) {
			if( $name === 'Ficheprescription93' ) {
				$this->Ficheprescription93 = ClassRegistry::init( 'Ficheprescription93' );
				return $this->Ficheprescription93;
			}
			if( isset( $this->{$name} ) ) {
				return $this->{$name};
			}

			return parent::__get( $name );
		}

		/**
		 * Dans la liste des adresses du prestataire PDI, il faut afficher tel,
		 * fax et email dans l'attribut title.
		 *
		 * @param array $options
		 * @return array
		 */
		protected function _formatOptionsAdressePrestataire( array $options ) {
			foreach( $options as $i => $option ) {
				$title = array();
				$details = array(
					'tel' => 'Tél.: %s',
					'fax' => 'Fax.: %s',
					'email' => 'Email: %s',
				);
				foreach( $details as $fieldName => $label ) {
					if( !empty( $option[$fieldName] ) ) {
						$title[] = sprintf( $label, $option[$fieldName] );
					}
				}
				$option = array(
					'id' => $option['id'],
					'name' => $option['name'],
					'title' => implode( ', ', $title )
				);
				$options[$i] = $option;
			}

			return $options;
		}

		/**
		 * Traite l'événement Ajax d'un champ de formulaire ayant changé.
		 *
		 * @param array $data
		 * @return array
		 */
		public function ajaxOnChange( array $data ) {
			$data = $this->unprefixAjaxRequest( $data );
			$data['Target']['path'] = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $data['Target']['name'] ) );

			$value = Hash::get( $data, $data['Target']['path'] );

			// Suivant le niveau, on supprime les clés précédentes pour ne pas remettre à zéro
			$paths = array_keys( $this->Ficheprescription93->correspondances );
			$current = array_search( $data['Target']['path'], $paths );
			$invertedPaths = array_flip( $paths );

			$events = array( 'changed:Ficheprescription93.actionfp93_id', 'changed:Ficheprescription93.adresseprestatairefp93_id' );

			$fields = array();
			for( $i = $current + 1 ; $i < count($paths) ; $i++ ) {
				$fields[$paths[$i]] = array(
					'id' => Inflector::camelize( str_replace( '.', '_', $paths[$i] ) ),
					'value' => Hash::get( $data, $paths[$i] ),
					'type' => 'select',
					'options' => array()
				);
			}

			$fields['Ficheprescription93.numconvention']['type'] = 'text';
			if( 'Ficheprescription93.numconvention' !== $data['Target']['path'] ) {
				$fields['Ficheprescription93.numconvention']['value'] = null;
			}

			/*// Si on change l'action, on de doit pas modifier l'adresse du prestataire hors PDI
			if( $data['Target']['path'] === 'Ficheprescription93.actionfp93_id' ) {
				unset( $fields['Ficheprescription93.adresseprestatairefp93_id'] );
			}*/

			// Si on change l'adresse du prestataire PDI, on ne doit pas modifier le n° de convention
			if( $data['Target']['path'] === 'Ficheprescription93.adresseprestatairefp93_id' ) {
				unset( $fields['Ficheprescription93.numconvention'] );
			}

			$conditionsActionfp93 = array();
			$typethematiquefp93_id = Hash::get( $data, 'Ficheprescription93.typethematiquefp93_id' );
			$action = Hash::get( $data, 'Ficheprescription93.action' );
			// SSI PDI ET formulaire add_edit
			if( $typethematiquefp93_id === 'pdi' ) {
				if( $action === 'add' ) {
					$conditionsActionfp93 = array(
						'Actionfp93.actif' => '1'
					);
				}
			}

			$vfsFicheprescription93 = array(
				// Type
				'Ficheprescription93.typethematiquefp93_id' => array(
					'real' => 'Thematiquefp93.type',
					'modelName' => 'Thematiquefp93',
					'next' => 'Ficheprescription93.thematiquefp93_id'
				),
				// Thématique
				'Ficheprescription93.thematiquefp93_id' => array(
					'real' => 'Categoriefp93.thematiquefp93_id',
					'modelName' => 'Categoriefp93',
					'next' => 'Ficheprescription93.categoriefp93_id'
				),
				// Catégorie
				'Ficheprescription93.categoriefp93_id' => array(
					'real' => 'Filierefp93.categoriefp93_id',
					'modelName' => 'Filierefp93',
					'next' => 'Ficheprescription93.filierefp93_id'
				)
			);

			if( !empty( $value ) ) {
				// On sélectionne le type, la thématique ou la catégorie
				if( in_array( $data['Target']['path'], array( 'Ficheprescription93.typethematiquefp93_id', 'Ficheprescription93.thematiquefp93_id', 'Ficheprescription93.categoriefp93_id' ) ) ) {
					$virtual = $vfsFicheprescription93[$data['Target']['path']];
					$query = array( 'conditions' => array( $virtual['real'] => $value ) );

					$query['conditions'][] = ClassRegistry::init( $virtual['modelName'] )->getDependantListCondition(
						Hash::get( $data, 'Ficheprescription93.typethematiquefp93_id' ),
						$conditionsActionfp93
					);

					$fields[$virtual['next']]['options'] = $this->ajaxOptions( $virtual['modelName'], $query );
				}
				// On sélectionne la filière
				else if( $current == $invertedPaths['Ficheprescription93.filierefp93_id'] ) {
					// Liste des actions
					$query = array(
						'conditions' => array(
							'Actionfp93.filierefp93_id' => Hash::get( $data, 'Ficheprescription93.filierefp93_id' )
						)
					);

					if( !empty( $conditionsActionfp93 ) ) {
						$query['conditions'][] = $conditionsActionfp93;
					}

					$fields['Ficheprescription93.actionfp93_id']['options'] = $this->ajaxOptions( 'Actionfp93', $query );

					// Liste des prestataires
					$Prestatairefp93 = ClassRegistry::init( 'Prestatairefp93' );
					$query = array(
						'joins' => array(
							$Prestatairefp93->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) ),
							$Prestatairefp93->Adresseprestatairefp93->join( 'Actionfp93', array( 'type' => 'INNER' ) ),
						),
						'conditions' => array(
							'Actionfp93.filierefp93_id' => Hash::get( $data, 'Ficheprescription93.filierefp93_id' )
						)
					);

					if( $typethematiquefp93_id === 'pdi' && $action === 'add' ) {
						$query['conditions']['Actionfp93.actif'] = '1';
					}

					$query['conditions'][] = $Prestatairefp93->getDependantListCondition(
						Hash::get( $data, 'Ficheprescription93.typethematiquefp93_id' ),
						$conditionsActionfp93
					);

					$fields['Ficheprescription93.prestatairefp93_id']['options'] = $this->ajaxOptions( 'Prestatairefp93', $query );
				}
				// On sélectionne le prestataire
				else if( $current == $invertedPaths['Ficheprescription93.prestatairefp93_id'] ) {
					// Liste des actions liées au prestataire
					$query = array(
						'conditions' => array(
							'Actionfp93.filierefp93_id' => Hash::get( $data, 'Ficheprescription93.filierefp93_id' ),
							'Adresseprestatairefp93.prestatairefp93_id' => Hash::get( $data, 'Ficheprescription93.prestatairefp93_id' ),
						),
						'joins' => array(
							ClassRegistry::init( 'Actionfp93' )->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) )
						)
					);

					if( !empty( $conditionsActionfp93 ) ) {
						$query['conditions'][] = $conditionsActionfp93;
					}

					$fields['Ficheprescription93.actionfp93_id']['options'] = $this->ajaxOptions( 'Actionfp93', $query );

					// Liste des adresses du prestataire
					// Début FIXME
					$query = array(
						'fields' => array(
							'Adresseprestatairefp93.tel',
							'Adresseprestatairefp93.fax',
							'Adresseprestatairefp93.email'
						),
						'conditions' => array(
							'Adresseprestatairefp93.prestatairefp93_id' => Hash::get( $data, 'Ficheprescription93.prestatairefp93_id' ),
						)
					);

					$fields['Ficheprescription93.adresseprestatairefp93_id']['options'] = $this->ajaxOptions( 'Adresseprestatairefp93', $query );
					$fields['Ficheprescription93.adresseprestatairefp93_id']['options'] = $this->_formatOptionsAdressePrestataire( $fields['Ficheprescription93.adresseprestatairefp93_id']['options'] );

					// S'il n'existe qu'une adresse pour ce prestataire, on la pré-sélectionne
					if( count( $fields['Ficheprescription93.adresseprestatairefp93_id']['options'] ) === 1 ) {
						$fields['Ficheprescription93.adresseprestatairefp93_id']['value'] = $fields['Ficheprescription93.adresseprestatairefp93_id']['options'][0]['id'];
						$events[] = 'changed:Ficheprescription93.adresseprestatairefp93_id';
					}
					// Fin FIXME
				}
				// On sélectionne l'action
				else if( $current == $invertedPaths['Ficheprescription93.actionfp93_id'] ) {
					$Actionfp93 = ClassRegistry::init( 'Actionfp93' );
					$result = $Actionfp93->find(
						'first',
						array(
							'fields' => array(
								'Adresseprestatairefp93.prestatairefp93_id',
								'Actionfp93.adresseprestatairefp93_id',
								'Actionfp93.numconvention',
								'Actionfp93.duree',
							),
							'joins' => array(
								$Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) )
							),
							'conditions' => array(
								'Actionfp93.id' => Hash::get( $data, 'Ficheprescription93.actionfp93_id' )
							)
						)
					);

					foreach( array( 'Ficheprescription93.adresseprestatairefp93_id', 'Ficheprescription93.prestatairefp93_id' ) as $path ) {
						$fields[$path] = array(
							'id' => Inflector::camelize( str_replace( '.', '_', $path ) ),
							'value' => null,
							'type' => 'select'
						);
					}

					$fields['Ficheprescription93.numconvention']['value'] = Hash::get( $result, 'Actionfp93.numconvention' );
					$fields['Ficheprescription93.prestatairefp93_id']['value'] = Hash::get( $result, 'Adresseprestatairefp93.prestatairefp93_id' );
					$fields['Ficheprescription93.adresseprestatairefp93_id']['value'] = Hash::get( $result, 'Actionfp93.adresseprestatairefp93_id' );

					// On re-sélectionne la liste des adresses du prestataire
					$sqQuery = array(
						'alias' => 'adressesprestatairesfps93',
						'fields' => array(
							'adressesprestatairesfps93.prestatairefp93_id'
						),
						'joins' => array(
							array_words_replace(
								$Actionfp93->Adresseprestatairefp93->join( 'Actionfp93', array( 'type' => 'INNER' ) ),
								array(
									'Actionfp93' => 'actionsfps93',
									'Adresseprestatairefp93' => 'adressesprestatairesfps93'
								)
							)
						),
						'conditions' => array(
							'actionsfps93.id' => Hash::get( $data, 'Ficheprescription93.actionfp93_id' ),
						),
					);
					$sq = $Actionfp93->Adresseprestatairefp93->sq( $sqQuery );

					$query = array(
						'fields' => array(
							'Adresseprestatairefp93.tel',
							'Adresseprestatairefp93.fax',
							'Adresseprestatairefp93.email'
						),
						'joins' => array(
							$Actionfp93->Adresseprestatairefp93->join( 'Actionfp93', array( 'type' => 'INNER' ) )
						),
						'conditions' => array(
							"Adresseprestatairefp93.prestatairefp93_id IN ( {$sq} )"
						)
					);

					$fields['Ficheprescription93.adresseprestatairefp93_id']['options'] = $this->ajaxOptions( 'Adresseprestatairefp93', $query );
					$fields['Ficheprescription93.adresseprestatairefp93_id']['options'] = $this->_formatOptionsAdressePrestataire( $fields['Ficheprescription93.adresseprestatairefp93_id']['options'] );
				}
				// Si on sélectionne l'adresse d'un prestaire PDI, il ne faut rien faire
				else if( $current == $invertedPaths['Ficheprescription93.adresseprestatairefp93_id'] ) {
					$fields = array();
					$events = array( 'changed:Ficheprescription93.adresseprestatairefp93_id' );
				}
			}

			//------------------------------------------------------------------

			// Si on change le type, il faut réinitialiser les champs partenaire Hors PDI...
			$reinitHorsPdi = ( $data['Target']['path'] === 'Ficheprescription93.typethematiquefp93_id' )
					// ou si on est en hors pdi et que l'on change une des listes déroulantes de l'action
				|| ( $typethematiquefp93_id === 'horspdi' && in_array( $data['Target']['path'], array( 'Ficheprescription93.thematiquefp93_id', 'Ficheprescription93.categoriefp93_id', 'Ficheprescription93.filierefp93_id' ) ) );
			if( $reinitHorsPdi ) {
				foreach( Hash::normalize( ClassRegistry::init( 'Ficheprescription93' )->fieldsHorsPdi ) as $fieldHorsPdi => $optionsHorsPdi ) {
					$optionsHorsPdi = (array)$optionsHorsPdi + array( 'type' => 'text' );

					$fields[$fieldHorsPdi] = array(
						'id' => Inflector::camelize( str_replace( '.', '_', $fieldHorsPdi ) ),
						'value' => null
					) + $optionsHorsPdi;
				}

				// ...ainsi que l'adresse de RDV
				$paths = array(
					'Ficheprescription93.rdvprestataire_adresse_check' => array( 'type' => 'checkbox' ),
					'Ficheprescription93.rdvprestataire_adresse' => array( 'type' => 'text' )
				);
				foreach( $paths as $path => $params ) {
					$fields[$path] = array(
						'id' => Inflector::camelize( str_replace( '.', '_', $path ) ),
						'value' => null,
					) + $params;
				}
			}

			//------------------------------------------------------------------

			// Si on a un préfixe, on l'ajoute à ce que l'on retourne
			$fields = $this->prefixAjaxResult( $data['prefix'], $fields );

			if( in_array( 'changed:Ficheprescription93.actionfp93_id', $events ) ) {
				$events[] = 'reset:Ficheprescription93.actionprestataire';
			}

			return array( 'success' => true, 'fields' => $fields, 'events' => $events );
		}

		/**
		 * Traite l'événement Ajax du chargement de la page et de pré-remplissage
		 * de formulaire.
		 *
		 * @param array $data
		 * @return array
		 * @throws LogicException
		 */
		public function ajaxOnLoad( array $data ) {
			$return = array();
			$data = $this->unprefixAjaxRequest( $data );

			$typethematiquefp93_id = Hash::get( $data, 'Ficheprescription93.typethematiquefp93_id' );

			$fieldKeys = array_keys( $this->Ficheprescription93->correspondances );
			foreach( $this->Ficheprescription93->correspondances as $path => $field ) {
				$pathOffset = array_search( $path, $fieldKeys );

				if( $pathOffset === false ) {
					throw new LogicException();
				}

				$value = Hash::get( $data, $path );
				$elmt = array(
					'id' => Inflector::camelize( str_replace( '.', '_', $path ) ),
					'value' => $value,
					'type' => 'select',
					'options' => array()
				);

				if( $pathOffset === 0 ) {
					$types = ClassRegistry::init( 'Thematiquefp93' )->enum( 'type' );
					$options = array();
					foreach( $types as $id => $name ) {
						$options[] = compact( 'id', 'name' );
					}
					$elmt['options'] = $options;
				}
				else if( $path == 'Ficheprescription93.prestatairefp93_id' ) {
					$Prestatairefp93 = ClassRegistry::init( 'Prestatairefp93' );
					$query = array(
						'joins' => array(
							$Prestatairefp93->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) ),
							$Prestatairefp93->Adresseprestatairefp93->join( 'Actionfp93', array( 'type' => 'INNER' ) )
						),
						'conditions' => array(
							'Actionfp93.filierefp93_id' => (int)Hash::get( $data, 'Ficheprescription93.filierefp93_id' )
						)
					);

					$elmt['options'] = $this->ajaxOptions( 'Prestatairefp93', $query );
				}
				else if( $path == 'Ficheprescription93.adresseprestatairefp93_id' ) {
					$query = array(
						'fields' => array(
							'Adresseprestatairefp93.tel',
							'Adresseprestatairefp93.fax',
							'Adresseprestatairefp93.email'
						),
						'conditions' => array(
							'Adresseprestatairefp93.prestatairefp93_id' => Hash::get( $data, 'Ficheprescription93.prestatairefp93_id' )
						)
					);

					$elmt['options'] = $this->ajaxOptions( 'Adresseprestatairefp93', $query );
					$elmt['options'] = $this->_formatOptionsAdressePrestataire( $elmt['options'] );
				}
				else if( $path == 'Ficheprescription93.actionfp93_id' ) {
					$Actionfp93 = ClassRegistry::init( 'Actionfp93' );

					$sqAnnee = $Actionfp93->sq(
						array(
							'alias' => 'actionsfps93',
							'fields' => array( 'actionsfps93.annee' ),
							'conditions' => array(
								'actionsfps93.id' => Hash::get( $data, 'Ficheprescription93.actionfp93_id' )
							)
						)
					);

					$query = array(
						'joins' => array(
							$Actionfp93->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
							$Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) ),
							$Actionfp93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'INNER' ) )
						),
						'conditions' => array(
							'Actionfp93.filierefp93_id' => Hash::get( $data, 'Ficheprescription93.filierefp93_id' ),
							'Adresseprestatairefp93.prestatairefp93_id' => Hash::get( $data, 'Ficheprescription93.prestatairefp93_id' ),
							"Actionfp93.annee IN ( {$sqAnnee} )",
						)
					);

					$typethematiquefp93_id = Hash::get( $data, 'Ficheprescription93.typethematiquefp93_id' );
					$action = Hash::get( $data, 'Ficheprescription93.action' );

					if( $typethematiquefp93_id === 'pdi' && $action === 'add' ) {
						$query['conditions']['Actionfp93.actif'] = 1;
					}

					$elmt['options'] = $this->ajaxOptions( 'Actionfp93', $query );
				}
				else if( $path !== 'Ficheprescription93.numconvention' ) {
					$parentPath = $fieldKeys[$pathOffset-1];
					$parentField = $this->Ficheprescription93->correspondances[$parentPath];

					list( $modelName, $fieldName ) = model_field( $field );
					list( $parentModelName, $parentFieldName ) = model_field( $parentField );

					if( !( $typethematiquefp93_id === 'horspdi' && in_array( $parentModelName, array( 'Prestatairefp93' ) ) ) ) {
						$query = array(
							'conditions' => array(
								$parentField => Hash::get( $data, $parentPath )
							)
						);

						if( $parentModelName !== $modelName ) {
							$query['joins'] = array(
								ClassRegistry::init( $modelName )->join( $parentModelName, array( 'type' => 'INNER' ) )
							);
						}

						$elmt['options'] = $this->ajaxOptions( $modelName, $query );
					}
				}

				$return[$path] = $elmt;
			}

			$return = $this->prefixAjaxResult( $data['prefix'], $return );

			return array( 'success' => true, 'fields' => $return, 'events' => array( 'changed:Ficheprescription93.actionfp93_id', 'changed:Ficheprescription93.adresseprestatairefp93_id' ) );
		}

		/**
		 *
		 * @param array $data
		 * @return array
		 */
		public function ajaxOnKeyup( array $data ) {
			$data = $this->unprefixAjaxRequest( $data );
			$data['Target']['path'] = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $data['Target']['name'] ) );
			$value = Hash::get( $data, $data['Target']['path'] );
			$events = array();

			if( $data['Target']['path'] === 'Ficheprescription93.numconvention' ) {
				$Actionfp93 = ClassRegistry::init( 'Actionfp93' );

				$query = array(
					'fields' => array(
						'Actionfp93.id',
						'( NOACCENTS_UPPER( "Actionfp93"."numconvention" ) || \': \' || "Actionfp93"."name" ) AS "Actionfp93__name"',
					),
					'conditions' => array(
						'OR' => array(
							'NOACCENTS_UPPER( "Actionfp93"."numconvention" ) LIKE' => '%'.noaccents_upper( $value ).'%',
							'NOACCENTS_UPPER( "Actionfp93"."name" ) LIKE' => '%'.noaccents_upper( $value ).'%',
						),
						'Actionfp93.actif' => '1' // FIXME: si c'est un add
					),
					'order' => array(
						'Actionfp93.numconvention ASC'
					)
				);

				if( trim( $value ) == '' ) {
					$query['conditions'] = '1 = 2';
				}

				$results = $Actionfp93->find( 'all', $query );

				$fields = array();

				if( trim( $value ) == '' ) {
					foreach( array_keys( $this->Ficheprescription93->correspondances ) as $field ) {
						$fields[$field] = array(
							'id' => Inflector::camelize( str_replace( '.', '_', "{$data['prefix']}{$field}" ) ),
							'value' => null,
							'type' => 'select',
							'prefix' => $data['prefix'],
							'options' => array()
						);
					}

					// Cas particulier du premier élément de la liste
					$types = ClassRegistry::init( 'Thematiquefp93' )->enum( 'type' );
					$options = array();
					foreach( $types as $id => $name ) {
						$options[] = compact( 'id', 'name' );
					}
					$fields['Ficheprescription93.typethematiquefp93_id']['options'] = $options;
				}

				$fields['Ficheprescription93.numconvention'] = array(
					'id' => "{$data['prefix']}Ficheprescription93Numconvention",
					// INFO: On n'envoie pas la valeur pour ne pas perturber la saisie
					// 'value' => $value,
					'type' => 'ajax_select',
					'prefix' => $data['prefix'],
					'options' => Hash::extract( $results, '{n}.Actionfp93' )
				);

				if( trim( $value ) == '' ) {
					$events = array( 'changed:Ficheprescription93.actionfp93_id', 'changed:Ficheprescription93.adresseprestatairefp93_id', 'reset:Ficheprescription93.actionprestataire' );
				}
			}

			return array( 'success' => true, 'fields' => $fields, 'events' => $events );
		}

		/**
		 * Retourne le json permettant de remplir les champs de la fiche de
		 * prescription.
		 *
		 * @param array $data
		 * @return array
		 */
		public function ajaxOnClick( array $data ) {
			$data = $this->unprefixAjaxRequest( $data );
			$path = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $data['name'] ) );

			if( in_array( $path, array( 'Ficheprescription93.numconvention', 'Search.Ficheprescription93.numconvention' ) ) ) {
				$Actionfp93 = ClassRegistry::init( 'Actionfp93' );

				$query = array(
					'fields' => array(
						'"Actionfp93"."numconvention" AS "Ficheprescription93__numconvention"',
						'"Actionfp93"."id" AS "Ficheprescription93__actionfp93_id"',
						'"Filierefp93"."id" AS "Ficheprescription93__filierefp93_id"',
						'"Prestatairefp93"."id" AS "Ficheprescription93__prestatairefp93_id"',
						'"Adresseprestatairefp93"."id" AS "Ficheprescription93__adresseprestatairefp93_id"',
						'"Categoriefp93"."id" AS "Ficheprescription93__categoriefp93_id"',
						'"Thematiquefp93"."id" AS "Ficheprescription93__thematiquefp93_id"',
						'"Thematiquefp93"."type" AS "Ficheprescription93__typethematiquefp93_id"',
					),
					'joins' => array(
						$Actionfp93->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
						$Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => 'INNER' ) ),
						$Actionfp93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'INNER' ) ),
						$Actionfp93->Filierefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
						$Actionfp93->Filierefp93->Categoriefp93->join( 'Thematiquefp93', array( 'type' => 'INNER' ) ),
					),
					'conditions' => array(
						'Actionfp93.id' => $data['value']
					)
				);

				$result = $Actionfp93->find( 'first', $query );

				$prefix = Hash::get( $data, 'prefix' );
				if( !empty( $prefix ) ) {
					$result = array( $prefix => $result );
				}
				$result['prefix'] = $prefix;

				// Ajout d'événements
				$return = $this->ajaxOnLoad( $result );
				$return['events'] = array(
					'changed:Ficheprescription93.actionfp93_id',
					'changed:Ficheprescription93.adresseprestatairefp93_id',
					'reset:Ficheprescription93.actionprestataire',
				);

				// S'il n'existe qu'une adresse pour ce prestataire, on la pré-sélectionne
				if( isset( $return['fields']['Ficheprescription93.adresseprestatairefp93_id'] ) ) {
					if( count( $return['fields']['Ficheprescription93.adresseprestatairefp93_id']['options'] ) === 1 ) {
						$return['fields']['Ficheprescription93.adresseprestatairefp93_id']['value'] = $return['fields']['Ficheprescription93.adresseprestatairefp93_id']['options'][0]['id'];
					}
				}

				return $return;
			}
			else {
				$pdiField = "{$path}_id";

				if( isset( $this->Ficheprescription93->correspondances[$pdiField] ) ) {
					list( $modelName, $fieldName ) = model_field( $this->Ficheprescription93->correspondances[$pdiField] );

					$Model = ClassRegistry::init( $modelName );
					$displayField = "{$Model->alias}.{$Model->displayField}";

					$query = array(
						'fields' => array( $displayField ),
						'conditions' => array(
							"{$Model->alias}.{$Model->primaryKey}" => $data['value']
						)
					);
					$result = $Model->find( 'first', $query );

					$fields = array(
						$path => array(
							'id' => domId( "{$data['prefix']}{$path}" ),
							'value' => Hash::get( $result, $displayField ),
							'type' => 'select',
							'options' => array()
						)
					);

					// On met à vide les champs qui dépendent de nous
					$delete = false;
					foreach( array_keys( $this->Ficheprescription93->correspondances ) as $key ) {
						if( !$delete ) {
							$delete = ( $pdiField === $key );
						}
						else {
							$newPath = preg_replace( '/_id$/', '', $key );

							$fields[$newPath] = array(
								'id' => domId( "{$data['prefix']}{$newPath}" ),
								'value' => '',
								'type' => 'select',
								'options' => array()
							);
						}
					}

					return array( 'success' => true, 'fields' => $fields, 'events' => array( 'changed:Ficheprescription93.actionfp93_id', 'changed:Ficheprescription93.adresseprestatairefp93_id' ) );
				}
			}
		}
	}
?>