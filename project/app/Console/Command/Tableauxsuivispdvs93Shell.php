<?php
	/**
	 * Fichier source de la classe Tableauxsuivispdvs93Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe Tableauxsuivispdvs93Shell ...
	 *
	 * @package app.Console.Command
	 */
	class Tableauxsuivispdvs93Shell extends XShell
	{
		const SUCCESS = 0;

		const ERROR = 1;

		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array( 'Tableausuivipdv93', 'WebrsaTableausuivipdv93' );

		/**
		 * Paramètres par défaut pour ce shell
		 *
		 * @var array
		 */
		public $defaultParams = array(
			'log' => false,
			'logpath' => LOGS,
			'verbose' => false,
		);

		/**
		 * Affiche l'en-tête du shell
		 */
		public function _welcome() {
			$this->out();
			$this->out( 'Shell de photographie des tableaux de suivi PIE' );
			$this->out();
			$this->hr();
		}

		/**
		 * Opions disponibles pour ce shell.
		 *
		 * @var array
		 */
		public $options = array(
			'annee' => array(
				'short' => 'a',
				'help' => 'Année pour laquelle enregistrer les tableaux de suivi (n\'a de sens qu\'en tout début d\'année suivante)',
				'default' => null
			),
			'memory_limit' => array(
				'short' => 'm',
				'help' => 'Mémoire maximale pouvant être utilisée par le shell, surcharge ce qui a été défini dans le php.ini (memory_limit)',
				'default' => '-1'
			),
			'test' => array(
				'short' => 't',
				'help' => 'Active le mode test, les enregistrements ne sont pas effectifs (la transaction SQL est annulée)',
				'default' => 'false'
			),
		);

		/**
		 * Sous-commandes disponibles pour ce shell.
		 *
		 * @var array
		 */
		public $subcomands = array(
			'historisation' => array(
				'help' => 'Historisation automatique des tableaux de suivi PIE (commande par défaut)'
			),
			'update' => array(
				'help' => 'Mise à jour des résultats des anciens tableaux de suivi'
			)
		);

		/**
		 * Surcharge du constructeur pour définir la valeur par défaut de l'option
		 * "annee" à l'année en cours.
		 *
		 * @param ConsoleOutput $stdout A ConsoleOutput object for stdout.
		 * @param ConsoleOutput $stderr A ConsoleOutput object for stderr.
		 * @param ConsoleInput $stdin A ConsoleInput object for stdin.
		 */
		public function __construct( $stdout = null, $stderr = null, $stdin = null ) {
			parent::__construct( $stdout, $stderr, $stdin );

			$this->options['annee']['default'] = date( 'Y' );
		}

		/**
		 * Surcharge de la méthode startup pour vérifier que le département soit
		 * uniquement le 93, modifier la valeur de memory_limit et inclure le
		 * fichier de configuration.
		 */
		public function startup() {
			parent::startup();

			$this->params['test'] = $this->params['test'] === 'true';

			$memory_limit = $this->params['memory_limit'];
			ini_set( 'memory_limit', $memory_limit );
			if( (string)ini_get( 'memory_limit') !== (string)$memory_limit ) {
				$msgstr = __( 'Impossible de modifier la valeur de memory_limit à \'%s\'' );
				$this->error( sprintf( $msgstr, $memory_limit ) );
			}

			$this->checkDepartement( 93 );

			// Chargement du fichier de configuration lié, s'il existe
			$path = APP.'Config'.DS.'Cg'.Configure::read( 'Cg.departement' ).DS.$this->name.'.php';
			if( file_exists( $path ) ) {
				include_once $path;
			}
		}

		/**
		 * Ajout des valeurs par défaut des filtres de recherche (cases à cocher)
		 * se trouvant dans la configuration sous la clé
		 * Tableauxsuivispdvs93.<tableau>.defaults.
		 *
		 * @param string $tableau
		 * @param array $search
		 * @return array
		 */
		protected function _filters( $tableau, array $search ) {
			$configureKey = "{$this->name}.{$tableau}.defaults";
			return Hash::merge(
				$search,
				(array)Configure::read( $configureKey )
			);
		}

		/**
		 * Tente l'historisation d'un tableau de suivi avec les paramètres de
		 * recherche et les extra.
		 *
		 * @param string $tableau
		 * @param array $search
		 * @param array $extra
		 * @return boolean
		 */
		protected function _historiserTableau( $tableau, array $search, array $extra ) {
			$labels = $this->Tableausuivipdv93->enum( 'name' );
			$this->out( "\t\tTableau {$labels[$tableau]}" );
			$filters = Hash::merge(
				$this->_filters( $tableau, $search ),
				$extra
			);

			return $this->WebrsaTableausuivipdv93->historiser(
				$tableau,
				$filters
			);
		}

		/**
		 * Tente l'historisation de tableaux de suivi avec les paramètres de
		 * recherche et les extra, éventuellement avec une précision sur l'intitulé.
		 *
		 * @param array $tableaux
		 * @param array $search
		 * @param array $extra
		 * @param string $label
		 * @return boolean
		 */
		protected function _historiserTableaux( array $tableaux, array $search, array $extra, $label = null ) {
			$labels = $this->Tableausuivipdv93->enum( 'type' );
			$success = true;

			$this->out( "\t{$labels[$extra['Search']['type']]}" . ( $label === null ? '' : ": {$label}" ) );
			foreach( $tableaux as $tableau ) {
				$success = $success && $this->_historiserTableau( $tableau, $search, $extra );
			}

			return $success;
		}

		/**
		 * Historisation réelle des tableaux de suivi.
		 */
		public function historisation() {
			$communautessrs = $this->Tableausuivipdv93->Communautesr->find( 'list', array( 'conditions' => array( 'Communautesr.actif' => 1 ) ) );
			$pdvs = $this->WebrsaTableausuivipdv93->listePdvs();
			$referents = $this->WebrsaTableausuivipdv93->listeReferentsPdvs();
			$search = array( 'Search' => array( 'annee' => $this->params['annee'] ) );
			$tableaux = array_keys( $this->WebrsaTableausuivipdv93->tableaux );
			$success = true;

			$this->out( "Enregistrement des tableaux de suivi pour l'année {$search['Search']['annee']}" );
			$this->Tableausuivipdv93->begin();

			// Sauvegarde pour le département
			$success = $success && $this->_historiserTableaux(
				$tableaux,
				$search,
				array( 'Search' => array( 'type' => 'cg' ) )
			);

			// Sauvegarde par PDV territorial
			foreach( $communautessrs as $communautesr_id => $label ) {
				$success = $success && $this->_historiserTableaux(
					$tableaux,
					$search,
					array( 'Search' => array( 'type' => 'communaute', 'communautesr_id' => $communautesr_id ) ),
					$label
				);
			}

			// Sauvegarde par PDV
			foreach( $pdvs as $pdv_id => $label ) {
				$success = $success && $this->_historiserTableaux(
					$tableaux,
					$search,
					array( 'Search' => array( 'type' => 'pdv', 'structurereferente_id' => $pdv_id ) ),
					$label
				);
			}

			// Sauvegarde par référent de PDV
			foreach( $referents as $referent_id => $label ) {
				$pdv_id = prefix( $referent_id );
				$label = "{$label} ($pdvs[$pdv_id]])";
				$success = $success && $this->_historiserTableaux(
					$tableaux,
					$search,
					array( 'Search' => array( 'type' => 'referent', 'referent_id' => $referent_id ) ),
					$label
				);
			}

			if( $success && false === $this->params['test'] ) {
				$this->Tableausuivipdv93->commit();
			}
			else {
				$this->Tableausuivipdv93->rollback();
			}

			$msgstr = ( $success ? 'Succès' : 'Erreur' ) . ( true === $this->params['test'] ? ' (test)' : '' );
			$this->out( $msgstr );

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}


		/**
		 * Mise à jour des entrées d'historiques de tableaux de suivi PIE antérieures
		 * à la version 3.1.0 de l'application.
		 */
		public function update() {
			$Dbo = $this->Tableausuivipdv93->getDataSource();
			$success = true;
			$count = 0;

			// Clés "Search" communes aux différents moteurs de recherche
			$common = array(
                'annee',
                'communautesr_id',
                'structurereferente_id',
                'referent_id',
				'type'
			);

			// On prend tous les champs sauf results
			$fields = $this->Tableausuivipdv93->fields();
			array_remove( $fields, 'Tableausuivipdv93.results' );

			$query = array(
				'fields' => $fields,
				'conditions' => array(
					'OR' => array(
						array( 'Tableausuivipdv93.version LIKE' => '2.%' ),
						array( 'Tableausuivipdv93.version LIKE' => '3.0.%' )
					)
				),
				'contain' => false
			);

			$this->Tableausuivipdv93->begin();

			$results = $this->Tableausuivipdv93->find( 'all', $query );
			foreach( $results as $result ) {
				$filters = (array)Hash::get(
					$this->WebrsaTableausuivipdv93->filters,
					$result['Tableausuivipdv93']['name']
				);
				$search = hash_filter_keys( unserialize( $result['Tableausuivipdv93']['search'] ), $filters );
				foreach( $common as $key ) {
					$search['Search'][$key] = $result['Tableausuivipdv93'][$key];
				}

				$search = serialize( $search );
				if( $search !== $result['Tableausuivipdv93']['search'] ) {
					$this->out( sprintf( 'Mise à jour du tableau de suivi d\'id %d', $result['Tableausuivipdv93']['id'] ) );
					$success = $success && $this->Tableausuivipdv93->updateAllUnBound(
						array( 'Tableausuivipdv93.search' => $Dbo->value( $search, 'string') ),
						array( 'Tableausuivipdv93.id' => $result['Tableausuivipdv93']['id'] )
					);
					$count++;
				}
			}

			if( $success && false === $this->params['test'] ) {
				$this->Tableausuivipdv93->commit();
			}
			else {
				$this->Tableausuivipdv93->rollback();
			}

			$msgstr = (
				$success
					? sprintf( 'Succès, mise à jour de %d tableau(x) de suivi enregistré(s)', $count )
					: 'Erreur'
			)
			. ( true === $this->params['test'] ? ' (test)' : '' );
			$this->out( $msgstr );

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}

		/**
		 * Ajout de nouvelles options pour le shell.
		 *
		 * @return ConsoleOptionParser
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			$Parser->addOptions( $this->options );
			$Parser->addSubcommands( $this->subcomands );

			return $Parser;
		}
	}
?>