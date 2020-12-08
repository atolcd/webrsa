<?php
	/**
	 * Code source de la classe AllocatairesHelper.
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe AllocatairesHelper fournit des blocs de champs pour construire
	 * des moteurs de recherche concernant les allocataires du RSA.
	 *
	 * Chaque méthode accepte une clé 'skip' dans les paramètres, permettant de
	 * ne pas obtenir un ou plusieurs champs du bloc.
	 *
	 * @package app.View.Helper
	 */
	class AllocatairesHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Search.SearchForm',
			'Xform',
			'Xhtml',
		);

		/**
		 * Les paramètres par défaut utilisés dans chacune des méthodes.
		 *
		 * @var array
		 */
		public $default = array(
			'prefix' => 'Search',
			'domain' => 'search_plugin',
			'options' => array(),
			'fieldset' => true,
			'skip' => array(),
			'configSkipPrefix' => 'ConfigurableQuery'
		);

		/**
		 * Lecture des champs à ne pas afficher ("skip") à partir de la
		 * configuration.
		 *
		 * Par exemple, pour l'URL "/orientsstructs/cohorte_nouvelles", la valeur
		 * de "ConfigurableQueryOrientsstructs.cohorte_nouvelles.skip" sera lue.
		 *
		 * @see Valeurs de configSkipPrefix et de prefix.
		 */
		protected function _readSkipConfig() {
			$configurePath = $this->default['configSkipPrefix'].'.'.Inflector::camelize($this->request->params['controller']).'.'.$this->request->params['action'];
			$skip = (array)Configure::read( "{$configurePath}.filters.skip" );
			if( !empty( $skip ) ) {
				if( !empty( $this->default['prefix'] ) ) {
					foreach( $skip as $key => $value ) {
						$skip[$key] = "{$this->default['prefix']}.{$value}";
					}
				}

				$this->default['skip'] = array_merge( $this->default['skip'], $skip );
			}
		}

		/**
		 * Surcharge du constructeur avec possibilité de choisir les paramètres
		 * par défaut.
		 *
		 * @param View $View
		 * @param array $settings
		 */
		public function __construct( View $View, $settings = array( ) ) {
			parent::__construct( $View, $settings );
			$this->default = $settings + $this->default;

			$this->_readSkipConfig();
		}

		/**
		 * Permet de savoir si un champ doit être affiché ou non, suivant les
		 * champs présents dans l'attribut 'skip' des paramètres.
		 *
		 * Utilisé dans les méthodes blocDossier(), blocAdresse() et blocAllocataire().
		 *
		 * @param string $path
		 * @param array $params
		 * @return boolean
		 */
		protected function _isSkipped( $path, array $params = array() ) {
			if( isset( $params['skip'] ) && in_array( $path, (array)$params['skip'] ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Retourne le contenu, éventuellement entouré d'un fieldset (suivant la
		 * valeur de la clé fieldset dans les paramètres) dont la légende
		 * sera traduite dans le domaine spécifié par la clé domaine des paramètres.
		 *
		 * @param string $legend L texte de la légende du fieldset
		 * @param string $content Le contenu
		 * @param array $params Les paramètres
		 * @return string
		 */
		protected function _fieldset( $legend, $content, array $params = array() ) {
			if( $params['fieldset'] == false || $content == '' ) {
				return $content;
			}
			else {
				return $this->Xhtml->tag(
					'fieldset',
					$this->Xhtml->tag( 'legend', __d( $params['domain'], $legend ) )
					.$content
				);
			}

			return $content;
		}

		/**
		 * Retourne le résultat de Xform::input() si le champ ne se trouve pas
		 * dans la clé skip des paramètres.
		 *
		 * @param string $path Le chemin préfixé du champ
		 * @param array $params Les paramètres de l'appel à la méthode blocXXX()
		 * @param array $inputParams Les paramètres spécifiques à l'input
		 * @return string
		 */
		protected function _input( $path, array $params, array $inputParams = array() ) {
			if( !$this->_isSkipped( $path, $params ) ) {
				if( !isset( $inputParams['domain'] ) ) {
					$inputParams['domain'] = $params['domain'];
				}

				if( !isset( $inputParams['label'] ) ) {
					$inputParams['label'] = __d( $params['domain'], $path );
				}

				return $this->Xform->input( $path, $inputParams );
			}

			return null;
		}

		/**
		 * Retourne le résultat de SearchForm::dependantCheckboxes() si le champ
		 * ne se trouve pas dans la clé skip des paramètres.
		 *
		 * @param string $path Le chemin préfixé du champ
		 * @param array $params La clé skip est utilisée
		 * @param array $inputParams Les clés domain et options sont utilisées
		 * @return string
		 */
		protected function _dependantCheckboxes( $path, array $params, array $inputParams = array() ) {
			if( !$this->_isSkipped( $path, $params ) ) {
				if( !isset( $inputParams['domain'] ) ) {
					$inputParams['domain'] = $params['domain'];
				}

				return $this->SearchForm->dependantCheckboxes( $path, $inputParams );
			}

			return null;
		}

		/**
		 * Retourne le résultat de SearchForm::dateRange() si le champ
		 * ne se trouve pas dans la clé skip des paramètres.
		 *
		 * @param string $path Le chemin préfixé du champ
		 * @param array $params La clé skip est utilisée, domain est copiée
		 * @param array $inputParams Les clés domain et options sont utilisées
		 * @return string
		 */
		protected function _dateRange( $path, array $params, array $inputParams = array() ) {
			if( !$this->_isSkipped( $path, $params ) ) {
				if( !isset( $inputParams['domain'] ) ) {
					$inputParams['domain'] = $params['domain'];
				}

				return $this->SearchForm->dateRange( $path, $inputParams );
			}

			return null;
		}

		/**
		 * Retourne une groupe de filtres par dossier contenant les champs:
		 *	- Dossier.numdemrsa
		 *	- Dossier.matricule
		 *	- Dossier.dernier
		 *	- Dossier.dtdemrsa
		 *	- Situationdossierrsa.etatdosrsa
		 *	- Dossier.anciennete_dispositif
		 *	- Serviceinstructeur.id
		 *	- Dossier.fonorg
		 *	- Foyer.sitfam
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocDossier( array $params = array() ) {
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );

			$content = $this->_input( "{$params['prefix']}Dossier.numdemrsa", $params );
			$content .= $this->_input( "{$params['prefix']}Dossier.matricule", $params );
			$content .= $this->_input( "{$params['prefix']}Dossier.dernier", $params, array( 'type' => 'checkbox' ) );
			$content .= $this->_dateRange( "{$params['prefix']}Dossier.dtdemrsa", $params );

			$dcbPaths = array(
				'Situationdossierrsa.etatdosrsa',
				'Detailcalculdroitrsa.natpf',
				'Detaildroitrsa.oridemrsa'
			);
			foreach( $dcbPaths as $dcbPath ) {
				if( Hash::check( $params, "options.{$dcbPath}" ) ) {
					$content .= $this->_dependantCheckboxes(
						 "{$params['prefix']}{$dcbPath}",
						  $params,
						  array( 'options' => (array)Hash::get( $params, "options.{$dcbPath}" ),
						'hide' => TRUE  ) );
				}
			}

			$paths = array(
				'Dossier.anciennete_dispositif',
				'Serviceinstructeur.id',
				'Suiviinstruction.typeserins',
				'Dossier.fonorg',
				'Foyer.sitfam'
			);
			foreach( $paths as $path ) {
				if( Hash::check( $params, "options.{$path}" ) ) {
					$content .= $this->_input( "{$params['prefix']}{$path}", $params, array( 'type' => 'select', 'options' => (array)Hash::get( $params, "options.{$path}" ), 'empty' => true ) );
				}
			}

			return $this->_fieldset( 'Search.Dossier', $content, $params );
		}

		/**
		 * Retourne une groupe de filtres par adresse contenant les champs:
		 *	- Adresse.nomvoie
		 *	- Adresse.nomcom
		 *	- Adresse.numcom
		 *	- Canton.canton
		 *	- Sitecov58.name
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocAdresse( array $params = array() ) {
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );

			$content = $this->_input( "{$params['prefix']}Adresse.nomvoie", $params, array( 'type' => 'text' ) );
			$content .= $this->_input( "{$params['prefix']}Adresse.nomcom", $params, array( 'type' => 'text' ) );

			$numscoms = (array)Hash::get( $params, 'options.Adresse.numcom' );
			$multiple = Configure::read( 'ConfigurableQuery.common.filters.Adresse.numcom.multiple' );
			$multiple_larger_1 = Configure::read( 'ConfigurableQuery.common.filters.Adresse.numcom.multiple_larger_1' );
			if( $multiple && ( !$multiple_larger_1 || count( $numscoms ) > 1 ) ) {
				$content .= $this->_dependantCheckboxes( 
					"{$params['prefix']}Adresse.numcom", 
					$params,
					array( 
						'type' => 'select', 
						'multiple' => 'checkbox',
						'options' => $numscoms,
						'class' => 'divideInto3Columns',
						'buttons' => true,
						'autoCheck' => true,
						'hide' => TRUE 
					 )
				);
			}
			else {
				$content .= $this->_input( "{$params['prefix']}Adresse.numcom", $params, array( 'type' => 'select', 'options' => $numscoms, 'empty' => true ) );
			}

			if( Configure::read( 'CG.cantons' ) ) {
				$content .= $this->_input( "{$params['prefix']}Canton.canton", 
									$params,
									array( 	'type' => 'select',
											'options' => (array)Hash::get( $params, 'options.Canton.canton' ),
									  		'empty' => true 
									) );
			}

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$content .= $this->_input( "{$params['prefix']}CantonSitecov58.id", $params, array( 'type' => 'select', 'options' => (array)Hash::get( $params, 'options.Sitecov58.id' ), 'empty' => true ) );
			}

			return $this->_fieldset( 'Search.Adresse', $content, $params );
		}

		/**
		 * Retourne une groupe de filtres par allocataire contenant les champs:
		 *	- Personne.dtnai
		 *	- Personne.nom
		 *	- Personne.nomnai
		 *	- Personne.prenom
		 *	- Personne.nir
		 *	- Personne.sexe
		 *	- Personne.trancheage (si les options sont renseignées)
		 *	- Calculdroitrsa.toppersdrodevorsa
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocAllocataire( array $params = array() ) {
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );

			$dateParams = array(
				'type' => 'date', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'empty' => true
			);
			$content = $this->_input( "{$params['prefix']}Personne.dtnai", $params, $dateParams + array('dateFormat' => 'DMY') );

			if (Configure::read('Cg.departement') == 66) {
				$content .= $this->_input("{$params['prefix']}Personne.dtnai_month", $params, $dateParams + array('dateFormat' => 'M'));
				$content .= $this->_input("{$params['prefix']}Personne.dtnai_year", $params, $dateParams + array('dateFormat' => 'Y'));
			}

			$content .= $this->_input( "{$params['prefix']}Personne.nom", $params );
			$content .= $this->_input( "{$params['prefix']}Personne.nomnai", $params );
			$content .= $this->_input( "{$params['prefix']}Personne.prenom", $params );
			$content .= $this->_input( "{$params['prefix']}Personne.nir", $params, array( 'maxlength' => 15 ) );
			$content .= $this->_input( "{$params['prefix']}Personne.sexe", $params, array( 'options' => (array)Hash::get( $params, 'options.Personne.sexe' ), 'empty' => true ) );

			if ($trancheage = Configure::read('Search.Options.enums.Personne.trancheage')) {
				$params['options']['Personne']['trancheage'] = $trancheage;
			}

			if( Hash::check( $params, 'options.Personne.trancheage' ) ) {
				$content .= $this->_input( "{$params['prefix']}Personne.trancheage", $params, array( 'options' => (array)Hash::get( $params, 'options.Personne.trancheage' ), 'empty' => true ) );
			}
			$content .= $this->_input( "{$params['prefix']}Calculdroitrsa.toppersdrodevorsa", $params, array( 'options' => (array)Hash::get( $params, 'options.Calculdroitrsa.toppersdrodevorsa' ), 'empty' => true ) );

			if( true === Hash::check($params, 'configurableQueryParams.searchKeyPrefix') ) {
				$hasPrestation = (array)Configure::read(
					$params['configurableQueryParams']['searchKeyPrefix'].'.common.filters.has_prestation'
				);
				if (empty($hasPrestation)) {
					$hasPrestation = (array)Configure::read(
						$params['configurableQueryParams']['searchKeyPrefix'].'.'
						.$params['configurableQueryParams']['configurableQueryFieldsKey'].'.filters.has_prestation'
					);
				}

				if (!empty($hasPrestation)) {
					$options = array(
						'0' => 'Sans prestation',
						'1' => 'Demandeur ou Conjoint du RSA'
					) + (array)Hash::get($params, 'options.Prestation.rolepers');

					foreach (array_keys($options) as $option) {
						if (!in_array((string)$option, $hasPrestation, true)
							&& (!is_numeric($option)
							|| (is_numeric($option)
								&& !in_array((integer)$option, $hasPrestation, true))
							)
						) {
							unset($options[$option]);
						}
					}

					$label = __m("{$params['prefix']}Prestation.rolepers");
					if ($label === "{$params['prefix']}Prestation.rolepers") {
						$label = __m("Prestation.rolepers");
					}

					$content .= $this->_input(
						"{$params['prefix']}Prestation.rolepers",
						$params,
						array(
							'options' => $options,
							'empty' => true,
							'label' => $label
						)
					);
				}
			}

			return $this->_fieldset( 'Search.Personne', $content, $params );
		}

		/**
		 * Filtres de recherche configuré de type "possède un..."
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocHave( array $params = array() ) {
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );
			$params['configPath'] = ( isset($params['configPath']) && !empty( $params['configPath'] )
				? $params['configPath']
				: 'ConfigurableQuery.'.Inflector::camelize($this->params['controller']).'.' ).$this->params['action'].'.filters.has'
			;

			$configs = array_keys(Hash::normalize((array)Configure::read($params['configPath'])));
			unset($params['configPath']);
			if ( empty($configs) ) {
				return '';
			}

			$content = '';
			foreach ($configs as $modelName) {
				$inputName = "{$params['prefix']}Personne.has_".Inflector::underscore($modelName);
				$option = Hash::get($params, "options.Personne.has_".Inflector::underscore($modelName)) ? Hash::get($params, "options.Personne.has_".Inflector::underscore($modelName)) : array('Non', 'Oui');
				$content .= $this->_input( $inputName, $params,
					array(
						'label' => __d('allocataire', $inputName),
						'type' => 'select',
						'empty' => true,
						'options' => $option
					)
				);
			}

			return $this->_fieldset( __d('allocataire', 'Search.Personne.have'), $content, $params );
		}

		/**
		 * Retourne une liste déroulante de projets de villes communautaires
		 * permettant de lier ce filtre à un filtre de structures référentes.
		 * Uniquement au CG 93, pour les utilisateurs de type CG et CPDVCOM.
		 *
		 * @param string $modelName Le nom du modèle à utiliser (ex. Orientstruct)
		 * @param array $params
		 * @return string
		 */
		public function communautesrSelect( $modelName, array $params = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? rtrim($params['prefix'], '.')."." : null );
			$communautesrPrefix = array_key_exists( 'communautesr_prefix', $params ) ? $params['communautesr_prefix'] : $params['prefix'];
			$result = null;

			if( 93 == $departement && Hash::check( $params, "options.{$communautesrPrefix}{$modelName}.communautesr_id" ) ) {
				$label = Hash::get( $params, 'label' );
				$result = $this->_input(
				   "{$params['prefix']}{$modelName}.communautesr_id",
				   $params,
				   array(
					   'label' => empty( $label ) ? __m( "{$params['prefix']}{$modelName}.communautesr_id" ) : $label,
					   'type' => 'select',
					   'options' => (array)Hash::get( $params, "options.{$communautesrPrefix}{$modelName}.communautesr_id" ),
					   'empty' => true
				   )
				);
			}

			return $result;
		}

		/**
		 * Retourne le code javascript permettant de lier un filtre de projets de
		 * villes communautaires à un filtre de structures référentes.
		 * Uniquement au CG 93, pour les utilisateurs de type CG et CPDVCOM.
		 *
		 * @param string $modelName Le nom du modèle à utiliser (ex. Orientstruct)
		 * @param array $params
		 * @return string
		 */
		public function communautesrScript( $modelName, array $params = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? rtrim($params['prefix'], '.')."." : null );
			$communautesrPrefix = array_key_exists( 'communautesr_prefix', $params ) ? $params['communautesr_prefix'] : $params['prefix'];
			$params['hide'] = isset( $params['hide'] ) ? $params['hide'] : true;
			$result = null;

			if( 93 == $departement && Hash::check( $params, "options.{$communautesrPrefix}{$modelName}.communautesr_id" ) ) {
			   $result = "document.observe( 'dom:loaded', function() {
							try {
								dependantSelectsCommunautesr(
									'".$this->domId( "{$params['prefix']}{$modelName}.communautesr_id" )."',
									'".$this->domId( "{$params['prefix']}{$modelName}.structurereferente_id" )."',
									".json_encode( Hash::get( $params, "options.{$communautesrPrefix}{$modelName}.links" ) ).",
									".json_encode($params['hide'])."
								);
							} catch(e) {
								console.log(e);
							}
						} );\n";
				$result = $this->Xhtml->scriptBlock( $result, array( 'inline' => true, 'safe' => true ) );
			}

			return $result;
		}

		/**
		 * ...
		 *
		 * @param string $modelName Le nom du modèle à utiliser (ex. Orientstruct)
		 * @param array $params
		 * @return string
		 */
		public function communautesrScriptReferent( $modelName, array $params = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? rtrim($params['prefix'], '.')."." : null );
			$communautesrPrefix = array_key_exists( 'communautesr_prefix', $params ) ? $params['communautesr_prefix'] : $params['prefix'];
			$params['hide'] = isset( $params['hide'] ) ? $params['hide'] : true;
			$result = null;

			if( 93 == $departement && Hash::check( $params, "options.{$communautesrPrefix}{$modelName}.communautesr_id" ) ) {
			   $result = "document.observe( 'dom:loaded', function() {
							try {
								dependantSelectsCommunautesrReferent(
									'".$this->domId( "{$params['prefix']}{$modelName}.communautesr_id" )."',
									'".$this->domId( "{$params['prefix']}{$modelName}.referent_id" )."',
									".json_encode( Hash::get( $params, "options.{$communautesrPrefix}{$modelName}.links" ) ).",
									".json_encode($params['hide'])."
								);
							} catch(e) {
								console.log(e);
							}
						} );\n";
				$result = $this->Xhtml->scriptBlock( $result, array( 'inline' => true, 'safe' => true ) );
			}

			return $result;
		}

		/**
		 * Retourne une liste déroulante de projets de villes communautaires et
		 * le code javascript permettant de lier ce filtre à un filtre de
		 * structures référentes.
		 * Uniquement au CG 93, pour les utilisateurs de type CG et CPDVCOM.
		 *
		 * @param string $modelName Le nom du modèle à utiliser (ex. Orientstruct)
		 * @param array $params
		 * @return string
		 */
		public function communautesr( $modelName, array $params = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );
			$params['communautesr_prefix'] = $params['prefix'];
			$params['hide'] = isset( $params['hide'] ) ? $params['hide'] : true;
			$result = null;

			if( 93 == $departement ) {
				$result = $this->communautesrSelect( $modelName, $params )
					.$this->communautesrScript( $modelName, $params );
			}

			return $result;
		}

		/**
		 * Retourne une groupe de filtres par référent du parcours contenant les champs:
		 *	- PersonneReferent.communautesr_id: au CD 93 pour les utilisateurs CG et CPDVCOM
		 *	- PersonneReferent.structurereferente_id
		 *	- PersonneReferent.referent_id
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocReferentparcours( array $params = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );

			$script = "document.observe( 'dom:loaded', function() {
				try {
					dependantSelect( '".$this->domId( "{$params['prefix']}PersonneReferent.referent_id" )."', '".$this->domId( "{$params['prefix']}PersonneReferent.structurereferente_id" )."' );
				} catch(e) {
					console.log(e);
				}
			} );";
			$script = $this->Xhtml->scriptBlock( $script, array( 'inline' => true, 'safe' => true ) );

			$domain_search_plugin = ( $departement == 93 ) ? 'search_plugin_93' : 'search_plugin';

			$content = $this->_input(
				"{$params['prefix']}PersonneReferent.structurereferente_id",
				$params,
				array(
					'label' => __d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
					'type' => 'select',
					'options' => (array)Hash::get( $params, 'options.PersonneReferent.structurereferente_id' ),
					'empty' => true
				)
			)
			.$this->_input(
				"{$params['prefix']}PersonneReferent.referent_id",
				$params,
				array(
					'label' => __d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
					'type' => 'select',
					'options' => (array)Hash::get( $params, 'options.PersonneReferent.referent_id' ),
					'empty' => true
				)
			);

			if( !empty( $content ) ) {
				// Si on a les options (pour les CG et CPDVCOM au CG 93)
				if( 93 == $departement && Hash::check( $params, 'options.PersonneReferent.communautesr_id' ) ) {
					$label = __d( 'search_plugin_93', 'Communautesrparcours.lib_struc' );
					$content = $this->communautesrSelect( 'PersonneReferent', array( 'label' => $label ) + $params + array( 'communautesr_prefix' => null ) ) . $content;
					$script .= $this->communautesrScript( 'PersonneReferent', array( 'hide' => false ) + $params + array( 'communautesr_prefix' => null ) ) . $script;
				}

				$content .= $script;
			}

			return $this->_fieldset( 'Search.Referentparcours', $content, $params );
		}

		/**
		 * Retourne une groupe de contrôles de la pagination contenant le champ:
		 *	- Pagination.nombre_total
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocPagination( array $params = array() ) {
			$params = $params + $this->default;
			$params['prefix'] = ( !empty( $params['prefix'] ) ? "{$params['prefix']}." : null );

			$content = $this->Xform->input( "{$params['prefix']}Pagination.nombre_total", array( 'label' =>  __d( $params['domain'], 'Search.Pagination.nombre_total' ), 'type' => 'checkbox' ) );

			$value = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
			if (!is_null(Configure::read( 'ConfigurableQuery.'.ucfirst ($this->params['controller']).'.limit' ))) {
				$value = Configure::read( 'ConfigurableQuery.'.ucfirst ($this->params['controller']).'.limit' );
			}
			if (isset ($this->request->data['limit'])) {
				$value = $this->request->data['limit'];
			} elseif (isset ($this->request->data['Search']['limit'])) {
				$value = $this->request->data['Search']['limit'];
			}

			$content .= '<br />'.$this->Xform->input(
				"limit",
				array(
					'label' =>  __d( $params['domain'], "{$params['prefix']}Pagination.resultats_par_page" ),
					'type' => 'radio',
					'options' => Configure::read( 'ResultatsParPage.nombre_de_resultats' ),
					'value' => $value
				)
			);

			return $this->_fieldset( 'Search.Pagination', $content, $params );
		}

		/**
		 * Retourne un ensemble de scripts pour un formulaire permettant:
		 *	- de cacher le formulaire de recherche
		 *	- de désactiver le bouton de soumission à l'envoi du formulaire
		 *
		 * @param array $params
		 * @return string
		 */
		public function blocScript( array $params = array() ) {
			$default = array(
				'id' => Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_form" ),
				'prefix' => $this->default['prefix'],
				'domain' => $this->default['domain'],
			);

			$params = $params + $default;

			$content = '';

			if( ( isset( $this->request->data[$params['prefix']] ) && !empty( $this->request->params['named'] ) ) ) {
				$out = "document.observe( 'dom:loaded', function() { \$('{$params['id']}').hide(); } );";
				$content .= $this->Xhtml->scriptBlock( $out );
			}

			$content .= $this->SearchForm->observeDisableFormOnSubmit( $params['id'] );

			return $content;
		}
	}
?>