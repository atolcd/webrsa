<?php
	/**
	 * Code source de la classe Romev3Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe Romev3Helper ...
	 *
	 * @package app.View.Helper
	 */
	class Romev3Helper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Default3' => array(
				'className' => 'Default.DefaultDefault',
				'useBuffer' => false
			),
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => false
			),
			'Xform'
		);

		/**
		 * Retourne les fieldset pour un alias du modèle Entreeromev3, utilisables
		 * dans le moteur de recherche par Dsp ainsi que dans le formulaire d'ajout
		 * ou de modification, si la variable de configuration Romev3.enabled est
		 * à true.
		 *
		 * Le fieldset contient un champ permettant de faire une recherche partielle
		 * (en Ajax, voir le paramètre URL) sur l'appellation et de remplir
		 * automatiquement les champs de type liste déroulante.
		 * Il contient un champ de type liste déroulante (liée) pour chacun des
		 * niveaux (fameille, domaine, métier, appellation).
		 *
		 * @param string $modelName L'alias du modèle Entreeromev3
		 * @param array $params Voir les clés domain, options, id, fieldset, url et required
		 * @return string
		 */
		public function fieldset( $modelName, array $params = array() ) {
			$params += array(
				'domain' => $this->request->params['controller'],
				'options' => array(),
				'id' => null,
				'fieldset' => true,
				'url' => array( 'controller' => 'cataloguesromesv3', 'action' => 'ajax_appellation' ),
				'required' => false,
				'requiredOnlyFirst' => false,
				'prefix' => null,
				'multi_domain_translator' => false,
				'disabled' => false
			);
			$params = $this->addClass( $params, 'romev3' );

			$return = '';

			if( Configure::read( 'Romev3.enabled' ) ) {
				$prefix = !empty( $params['prefix'] ) ? $params['prefix'].'.' : null;
				$fieldsetPath = implode( '.', Hash::filter( array( $params['prefix'], $modelName ) ) );
				$ajaxFieldPath = "{$prefix}{$modelName}.romev3";

				if( $params['id'] === null ) {
					$params['id'] = $this->domId( $fieldsetPath );
				}

				$return .= $this->Default3->subform(
					array(
						$ajaxFieldPath => array( 'disabled' => $params['disabled'], 'type' => 'text', 'required' => false, 'label' => ( $params['multi_domain_translator'] ? __m( $ajaxFieldPath ) : null ) ),
						"{$prefix}{$modelName}.id" => array( 'type' => 'hidden' ),
						"{$prefix}{$modelName}.familleromev3_id" => array( 'disabled' => $params['disabled'], "options" => $params['options'][$modelName]["familleromev3_id"], 'empty' => true, 'required' => ($params['required'] || $params['requiredOnlyFirst'] ? true : false), 'label' => ( $params['multi_domain_translator'] ? __m( "{$prefix}{$modelName}.familleromev3_id" ) : null ) ),
						"{$prefix}{$modelName}.domaineromev3_id" => array( 'disabled' => $params['disabled'], "options" => $params['options'][$modelName]["domaineromev3_id"], 'empty' => true, 'required' => $params['required'], 'label' => ( $params['multi_domain_translator'] ? __m( "{$prefix}{$modelName}.domaineromev3_id" ) : null ) ),
						"{$prefix}{$modelName}.metierromev3_id" => array( 'disabled' => $params['disabled'], "options" => $params['options'][$modelName]["metierromev3_id"], 'empty' => true, 'required' => $params['required'], 'label' => ( $params['multi_domain_translator'] ? __m( "{$prefix}{$modelName}.metierromev3_id" ) : null ) ),
						"{$prefix}{$modelName}.appellationromev3_id" => array( 'disabled' => $params['disabled'], "options" => $params['options'][$modelName]["appellationromev3_id"], 'empty' => true, 'required' => $params['required'], 'label' => ( $params['multi_domain_translator'] ? __m( "{$prefix}{$modelName}.appellationromev3_id" ) : null ) )
					),
					array(
						"options" => $params['options']
					)
				);

				$return .= $this->Observer->dependantSelect(
					array(
						"{$prefix}{$modelName}.familleromev3_id" => "{$prefix}{$modelName}.domaineromev3_id",
						"{$prefix}{$modelName}.domaineromev3_id" => "{$prefix}{$modelName}.metierromev3_id",
						"{$prefix}{$modelName}.metierromev3_id" => "{$prefix}{$modelName}.appellationromev3_id",
					)
				);

				//
				$return .= $this->Ajax2->observe(
					array(
						$ajaxFieldPath => array( 'event' => 'keyup' )
					),
					array(
						'url' => $params['url'],
						'onload' => false
					)
				);

				if( Hash::get( $params, 'fieldset' ) ) {
					$return = $this->Default3->DefaultHtml->tag(
						'fieldset',
						$this->Default3->DefaultHtml->tag( 'legend', $params['multi_domain_translator'] ? __m( $fieldsetPath ) : __d( $params['domain'], $fieldsetPath ) )
						.$return,
						array(
							'id' => $this->domId( "{$fieldsetPath}.Fieldset.id" ),
							'class' => $params['class']
						)
					);
				}
			}

			return $return;
		}

		/**
		 * Retourne la liste des champs de type name pour une utilisation avec
		 * Default2Helper ou DefaultDefaultHelper (Default3), si la variable de
		 * configuration Romev3.enabled est à true.
		 *
		 * Utilisé dans la visualisation d'une Dsp.
		 *
		 * @param string $modelName
		 * @param array $params
		 * @return array
		 */
		public function fields( $modelName, array $params = array() ) {
			$params += array(
				'domain' => $this->request->params['controller']
			);
			$fields = array();

			if( Configure::read( 'Romev3.enabled' ) ) {
				foreach( array( 'famille', 'domaine', 'metier', 'appellation' ) as $suffix ) {
					$path = preg_replace( '/romev3$/', '', $modelName )."{$suffix}romev3.name";
					$label = __d( $params['domain'], preg_replace( '/romev3$/', '', $modelName )."{$suffix}romev3Rev.name" );
					$fields[$path] = array( 'label' => $label, 'type' => 'text' );
				}
			}

			return $fields;
		}

		/**
		 * Retourne un filedset de visualisation, équivalent de la méthode
		 * Romev3Helper::fieldset().
		 *
		 * @param string $modelName
		 * @param array $data
		 * @param array $params
		 * @return string
		 */
		public function fieldsetView( $modelName, array $data, array $params = array() ) {
			$params += array(
				'domain' => $this->request->params['controller'],
				'options' => array(),
				'id' => null,
				'fieldset' => true,
				'legend' => null
			);

			$return = '';
			$code = '';
			$params['legend'] = ( $params['legend'] === null ? __d( $params['domain'], $modelName ) : $params['legend'] );

			// Famille
			$code .= Hash::get( $data, "{$modelName}.Familleromev3.code" );
			$return .= $this->Xform->fieldValue( "{$modelName}.familleromev3_id", $code.' - '.Hash::get( $data, "{$modelName}.Familleromev3.name" ), $params['domain'] );

			// Domaine
			$code .= Hash::get( $data, "{$modelName}.Domaineromev3.code" );
			$return .= $this->Xform->fieldValue( "{$modelName}.domaineromev3_id", $code.' - '.Hash::get( $data, "{$modelName}.Domaineromev3.name" ), $params['domain'] );

			// Métier
			$code .= Hash::get( $data, "{$modelName}.Metierromev3.code" );
			$return .= $this->Xform->fieldValue( "{$modelName}.metierromev3_id", $code.' - '.Hash::get( $data, "{$modelName}.Metierromev3.name" ), $params['domain'] );

			// Appellation
			$return .= $this->Xform->fieldValue( "{$modelName}.appellationromev3_id", Hash::get( $data, "{$modelName}.Appellationromev3.name" ), $params['domain'] );

			if( Hash::get( $params, 'fieldset' ) ) {
				$return = $this->Default3->DefaultHtml->tag(
					'fieldset',
					$this->Default3->DefaultHtml->tag( 'legend', $params['legend'] )
					.$return,
					array( 'id' => $params['id'] )
				);
			}

			return $return;
		}
	}
?>