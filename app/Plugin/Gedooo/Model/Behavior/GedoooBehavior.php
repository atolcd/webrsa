<?php
	/**
	 * Fichier source de la classe GedoooBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	if( !defined( 'GEDOOO_PLUGIN_DIR' ) ) {
		define( 'GEDOOO_PLUGIN_DIR', dirname( __FILE__ ).DS.'..'.DS.'..'.DS );
	}
	if( !defined( 'GEDOOO_WSDL' ) ) {
		define( 'GEDOOO_WSDL', Configure::read( 'Gedooo.wsdl' ) );
	}
	if( !defined( 'GEDOOO_TEST_FILE' ) ) {
		define( 'GEDOOO_TEST_FILE', GEDOOO_PLUGIN_DIR.'Vendor'.DS.'modelesodt'.DS.'test_gedooo.odt' );
	}

	App::uses( 'ModelBehavior', 'Model' );

	/**
	 * La classe GedoooBehavior correspond au patron de conception "fabrique"
	 * (Factory method pattern) et permet d'attacher le bon behavior suivant la
	 * configuration utilisée.
	 *
	 *  Exemple:
	 * <pre>
	 *	$this->User->Behaviors->attach( 'Gedooo.Gedooo' );
	 *	$pdf = $this->User->ged( array( 'Personne' => array( 'nom' => 'Buffin' ) ), 'APRE/apre66.odt' );
	 *	$this->Gedooo->sendPdfContentToClient( $pdf, 'foo.pdf' );
	 *	return;
	 * </pre>
	 *
	 * TODO: ajouter une version check, la conf (les define), les fichiers Vendor
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	class GedoooBehavior extends ModelBehavior
	{
		protected $_gedoooBehavior = null;

		/**
		 * Setup this behavior with the specified configuration settings.
		 *
		 * @param Model $model Model using this behavior
		 * @param array $config Configuration settings for $model
		 * @return void
		 */
		public function setup( Model $model, $config = array() ) {
			$method = Configure::read( 'Gedooo.method' );

			switch( $method ) {
				case 'classic':
					$this->_gedoooBehavior = 'GedoooClassic';
					break;
				case 'cloudooo':
				case 'cloudooo_ancien':
					$this->_gedoooBehavior = 'GedoooCloudooo';
					break;
				case 'unoconv':
				case 'unoconv_ancien':
					$this->_gedoooBehavior = 'GedoooUnoconv';
					break;
				default:
					$model->log( "Paramétrage incorrect: la méthode de Gedooo '{$method}' n'existe pas.", LOG_ERROR );
			}

			if( !empty( $this->_gedoooBehavior ) ) {
				if( !defined( 'PHPGEDOOO_DIR' ) ) {
					if( $method == 'classic' ) {
						define( 'PHPGEDOOO_DIR', GEDOOO_PLUGIN_DIR.'Vendor'.DS.'phpgedooo_ancien'.DS );
					}
					else if ( false === strpos( $method, '_ancien' ) ) {
						define( 'PHPGEDOOO_DIR', GEDOOO_PLUGIN_DIR.'Vendor'.DS.'phpgedooo_client'.DS.'src'.DS );
					}
					else {
						define( 'PHPGEDOOO_DIR', GEDOOO_PLUGIN_DIR.'Vendor'.DS.'phpgedooo_nouveau'.DS );
					}
				}

				if( !$model->Behaviors->attached( "Gedooo.{$this->_gedoooBehavior}" ) ) {
					$model->Behaviors->attach( "Gedooo.{$this->_gedoooBehavior}" );
				}
			}
		}

		/**
		 * Clean up any initialization this behavior has done on a model.  Called when a behavior is dynamically
		 * detached from a model using Model::detach().
		 *
		 * @param AppModel $model Model using this behavior
		 * @return void
		 * @see BehaviorCollection::detach()
		 */
		public function cleanup( Model $model ) {
			if( !empty( $this->_gedoooBehavior ) && $model->Behaviors->attached( "Gedooo.{$this->_gedoooBehavior}" ) ) {
				$model->Behaviors->detach( "Gedooo.{$this->_gedoooBehavior}" );
			}

			parent::cleanup( $model );
		}

		/**
		 * Retourne la liste des clés de configuration pour le plugin Gedooo.
		 *
		 * @return array
		 */
		public function gedConfigureKeys( Model $model ) {
			$keys = array(
				'Gedooo.method' => array(
					array( 'rule' => 'string', 'allowEmpty' => false ),
					array( 'rule' => 'inList', array( 'classic', 'cloudooo', 'cloudooo_ancien', 'unoconv', 'unoconv_ancien' ) )
				),
				'Gedooo.debug_export_data' => array(
					array( 'rule' => 'boolean', 'allowEmpty' => true )
				),
				'Gedooo.dont_force_newlines' => array(
					array( 'rule' => 'boolean', 'allowEmpty' => true )
				),
				'Gedooo.filter_vars' => array(
					array( 'rule' => 'boolean', 'allowEmpty' => true )
				)
			);

			if( !is_null( $this->_gedoooBehavior ) ) {
				$keys = array_merge(
					$keys,
					$model->Behaviors->{$this->_gedoooBehavior}->gedConfigureKeys( $model )
				);
			}

			return $keys;
		}
	}
?>