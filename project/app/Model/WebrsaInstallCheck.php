<?php
	/**
	 * Code source de la classe WebrsaInstallCheck.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaInstallCheck ...
	 *
	 * @package app.Model
	 */
	class WebrsaInstallCheck extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaInstallCheck';

		/**
		 * On n'utilise pas de table liée
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle
		 *
		 * @var array
		 */
		public $uses = array(
			'Appchecks.Check',
			'User',
			'WebrsaRecherche',
			'WebrsaCheck',
		);

		/**
		 * Vérifications concernant Apache:
		 *	- la version utilisée
		 *	- les modules nécessaires
		 *
		 * @return array
		 * @access protected
		 */
		public function apache() {
			return array(
				'Apache' => array(
					'informations' => array(
						'Version' => $this->Check->version( 'Apache', apache_version(), '2.4' )
					),
					'modules' => $this->Check->apacheModules(
						array(
							'mod_expires',
							'mod_rewrite'
						)
					),
				)
			);
		}

		/**
		 * Vérifications concernant PHP:
		 *	- la version utilisée
		 *	- les extensions nécessaires
		 *	- les variables du php.ini nécessaires
		 *
		 * @return array
		 * @access protected
		 */
		public function php() {
			return array(
				'Php' => array(
					'informations' => array(
						'Version' => $this->Check->version( 'PHP', phpversion(), '5.3' )
					),
					'extensions' => $this->Check->phpExtensions(
						array(
							'curl',
							'dom',
							'mbstring',
							'soap',
							'xml',
							'xmlrpc'
						)
					),
					'inis' => $this->Check->phpInis(
						array(
							'date.timezone',
							'post_max_size',
							 // Pour PHP >= 5.3.9, le passer à au moins 2500
							'max_input_vars' => array(
								'comparison' => array(
									'rule' => array( 'comparison', '>=', 2500 ),
									'allowEmpty' => false
								),
							),
						)
					)
 				)
			);
		}

		/**
		 *
		 * @return array
		 * @access protected
		 */
		public function environment() {
			return array(
				'Environment' => array(
					'binaries' => $this->Check->binaries(
						array(
							'pdftk'
						)
					),
					'directories' => $this->Check->directories(
						array(
							TMP => 'w',
							APP . Configure::read( 'Cohorte.dossierTmpPdfs' ) => 'w'
						),
						ROOT.DS
					),
					'files' => $this->Check->files(
						array(
							CONFIGS.'webrsa.inc',
							CSS.'webrsa.css',
							JS.'webrsa.js'
						),
						ROOT.DS
					),
					'cache' => $this->Check->cachePermissions(),
					'cache_check' => $this->Check->cacheFilePermissions(),
					'freespace' => $this->Check->freespace(
						array(
							// 1. Répertoire temporaire de CakePHP
							TMP,
							// 2. Répertoire temporaire pour les PDF.
							APP . Configure::read( 'Cohorte.dossierTmpPdfs' ),
							// 3. Répertoire de cache des wsdl
							ini_get( 'soap.wsdl_cache_dir' ),
						)
					)
				)
			);
		}

		/**
		 * Vérifications de la présence des fichiers de modèle .odt (paramétrbles et statiques).
		 *
		 * @return array
		 * @access protected
		 */
		public function modeles() {
			$modeles = $this->WebrsaCheck->allModelesOdt( Configure::read( 'Cg.departement' ) );

			return array(
				'Modelesodt' => array(
					'parametrables' => $this->Check->modelesOdt(
						$modeles['parametrables'],
						MODELESODT_DIR
					),
					'statiques' => $this->Check->modelesOdt(
						$modeles['statiques'],
						MODELESODT_DIR
					)
				)
			);
		}

		/**
		 * Vérifications concernant PostgreSQL:
		 *	- la version utilisée
		 *	- la présence des fonctions fuzzystrmatch
		 *	- la différence de date entre le serveur Web et le serveur PostgreSQL
		 *
		 * @return array
		 * @access protected
		 */
		public function postgresql() {
			$Dbo = $this->User->getDataSource();

			return array(
				'Postgresql' => array(
					'Version' => $this->Check->version( 'PostgreSQL', $Dbo->getPostgresVersion(), '8.3' ),
					'Fuzzystrmatch' => $this->WebrsaCheck->checkPostgresFuzzystrmatchFunctions(),
					'Date' => $this->WebrsaCheck->checkPostgresTimeDifference()
				)
			);
		}

		/**
		 * Vérifications concernant CakePHP:
		 *	- la version utilisée
		 *
		 * @return array
		 * @access protected
		 */
		public function cakephp() {
			return array(
				'Cakephp' => array(
					'informations' => array(
						'Version' => $this->Check->version( 'CakePHP', Configure::version(), '2.9.8' ),
						'Timeout' => $this->Check->timeout()
					),
					'cache' => $this->Check->durations()
				)
			);
		}

		/**
		 * Vérification de la présence des enregistrements dont les clés primaires
		 * sont configurées dans le webrsa.inc.
		 *
		 * @return array
		 */
		public function configurePrimaryKeys() {
			$return = $this->WebrsaCheck->allConfigurePrimaryKeys();

			if( !empty( $return ) ) {
				foreach( $return as $key => $params ) {
					if( is_string( $params ) ) {
						$params = array( 'modelName' => $params );
					}
					$params = Hash::merge( array( 'array_keys' => false ), $params );

					$return[$key] = $this->Check->configurePrimaryKey( $params['modelName'], $key, $params['array_keys'] );
				}
			}

			return $return;
		}

		/**
		 * Vérifications concernant WebRSA:
		 *	- la version utilisée
		 *  - la vérification de paramètres de configuration (Configure::read)
		 *
		 * @return array
		 * @access protected
		 */
		public function webrsa() {
			$recherches = $this->WebrsaRecherche->checks();
			foreach( $recherches as $key => $params ) {
				$recherches[$key]['config'] = $this->Check->configure( $params['config'] );
			}

			return array(
				'Webrsa' => array(
					'informations' => array(
						'Version' => $this->Check->version( 'WebRSA', app_version(), '0' ),
					),
					'configure' =>  $this->Check->configure(
						$this->WebrsaCheck->allConfigureKeys( Configure::read( 'Cg.departement' ) )
					),
					'intervals' => $this->WebrsaCheck->checkAllPostgresqlIntervals( Configure::read( 'Cg.departement' ) ),
					'querydata_fragments_errors' => $this->WebrsaCheck->allQuerydataFragmentsErrors(),
					'sqRechercheErrors' => $this->WebrsaCheck->allSqRechercheErrors(),
					'configure_primary_key' => $this->configurePrimaryKeys(),
					'configure_regexps' => $this->WebrsaCheck->allConfigureRegexpsErrors(),
					'configure_fields' => $this->WebrsaCheck->allCheckParametrage(),
					'ini_set' => $this->WebrsaCheck->allConfigureIniSet(),
					'configure_bad_keys' => $this->WebrsaCheck->allCheckBadKeys(),
					'configurable_query' => $recherches,
					'configure_evidence' => $this->WebrsaCheck->allConfigureEvidence(),
					'tableaux_conditions' => $this->WebrsaCheck->allConfigureTableauxConditions(),
					'webrsa_access' => WebrsaCheckAccess::checkWebrsaAccess(),
					'acos' => $this->WebrsaCheck->allControllersAcos(),
				)
			);
		}

		/**
		 * Retourne les enregistrements pour lesquels une erreur de paramétrage
		 * a été détectée.
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			return array(
				'Storeddata' => array(
					'errors' => $this->WebrsaCheck->allStoredDataErrors()
				)
			);
		}

		/**
		 * Vérifie la configuration des services (Gedooo, Alfresco, ...).
		 *
		 * @return array
		 */
		public function services() {
			$services = $this->WebrsaCheck->services();

			if( !empty( $services ) ) {
				foreach( $services as $serviceName => $results ) {
					if( isset( $results['configure'] ) ) {
						$services[$serviceName]['configure'] = $this->Check->configure(
							$results['configure']
						);
					}
				}
			}

			return array( 'Services' => $services );
		}

		/**
		 * Vérifie la configuration des mails.
		 *
		 * @return array
		 */
		public function emails() {
			$names = $this->WebrsaCheck->allEmailConfigs();
			$results = array();

			foreach( $names as $name ) {
				$results[$name] = $this->Check->cakeEmailConfig( $name );
			}

			return array( 'Emails' => $results );
		}

		/**
		 * Effectue l'ensemble des vérifications de l'application.
		 *
		 * @return array
		 */
		public function all() {
			$shell = defined( 'CAKEPHP_SHELL' ) && CAKEPHP_SHELL;
			return Hash::merge(
				$this->php(),
				$this->environment(),
				$this->modeles(),
				$this->postgresql(),
				$this->cakephp(),
				$this->webrsa(),
				$this->storedDataErrors(),
				$this->services(),
				$this->emails()
			);
		}
	}
?>