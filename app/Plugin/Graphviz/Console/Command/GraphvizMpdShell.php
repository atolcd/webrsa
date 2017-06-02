<?php
	/**
	 * Code source de la classe GraphvizMpdShell.
	 *
	 * PHP 5.3
	 *
	 * @package Graphviz
	 * @subpackage Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'ConnectionManager', 'Model' );

	// lib/Cake/Console/cake Graphviz.GraphvizMpd && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1

	// lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/(^personnes$|eps([0-9]+){0,1}$)/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1
	// lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/^(personnes|dossiers|foyers|prestations|adresses|adressesfoyers)$/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1
	// lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/(^personnes$|pcgs([0-9]+){0,1}$)/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1
	// lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/(^personnes$|apres{0,1}([0-9]+){0,1}$)/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1

	// lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/^(groups|users|users_zonesgeographiques|zonesgeographiques)$/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1

	// lib/Cake/Console/cake Graphviz.GraphvizMpd -t "/^(personnes|referents|personnes_referents|structuresreferentes)$/" && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1

	// lib/Cake/Console/cake Graphviz.GraphvizMpd && dot -K fdp -T png -o ./graphviz_mpd.png ./graphviz_mpd.dot && gwenview ./graphviz_mpd.png > /dev/null 2>&1

	/**
	 * La classe GraphvizMpdShell ...
	 *
	 * @package Graphviz
	 * @subpackage Console.Command
	 */
	class GraphvizMpdShell extends AppShell
	{
		/**
		 * Code de sortie en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Code de sortie en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * Les tables à prendre en compte.
		 *
		 * @var array
		 */
		protected $_tables = array();

		/**
		 * Les modèles à prendre en compte.
		 *
		 * @var array
		 */
		protected $_models = array();

		/**
		 * Les associations à prendre en compte, par modèle.
		 *
		 * @var array
		 */
		protected $_associations = array();

		/**
		 * Les associations déjà traitées.
		 *
		 * @var array
		 */
		protected $_formattedAssociations = array();

		/**
		 * Les paramètres généraux du graphes, utilisés pour générer le fichier
		 * .dot.
		 *
		 * @var array
		 */
		public $digraphParams = array(
			'overlap' => 'false',
			'concentrate' => 'true',
			'splines' => 'polyline',
			'outputorder' => 'nodesfirst',
			'pack' => 'true',
			'packmode' => 'clust',
			'fontname' => 'Bitstream Vera Sans',
			'fontsize' => '8',
			'pack' => 'false',
			'packMode' => 'clust',
			'nodesep' => '0.8',
		);

		/**
		 * Les paramètres des noeuds du graphes, utilisés pour générer le fichier
		 * .dot.
		 *
		 * @var array
		 */
		public $nodeParams = array(
			'fontname' => 'Bitstream Vera Sans',
			'fontsize' => '8',
			'shape' => 'record',
		);

		/**
		 * Les paramètres des relations du graphes, utilisés pour générer le fichier
		 * .dot.
		 *
		 * @var array
		 */
		public $edgeParams = array(
			'fontname' => 'Bitstream Vera Sans',
			'fontsize' => '8',
			'arrowhead' => 'none',
			'color' => 'firebrick',
			'fontcolor' => 'firebrick',
		);

		/**
		 * Réalise une sorte d'implode clés/valeurs suivant le format.
		 *
		 * @param array $array
		 * @param string $format
		 * @return string
		 */
		protected function _graphvizFormatParams( array $array, $format = "\n\t%s = \"%s\"" ) {
			$return = '';
			foreach( $array as $key => $value ) {
				$return .= sprintf( $format, $key, $value );
			}
			return $return;
		}

		/**
		 * Démarrage et configuration du shell.
		 */
		public function startup() {
			parent::startup();

			if( !empty( $this->params['tables'] ) ) {
				if( @preg_match( $this->params['tables'], '' ) === false ) {
					$this->err( "<error>Expression régulière erronée:</error> {$this->params['tables']}" );
					$this->_stop( self::ERROR );
				}
			}

			foreach( array( 'fields' ) as $bool ) {
				if( $this->params[$bool] == 'true' ) {
					$this->params[$bool] = true;
				}
				else if( $this->params[$bool] == 'false' ) {
					$this->params[$bool] = false;
				}

				if( !is_bool( $this->params[$bool] ) ) {
					$this->error( "Le paramètre {$bool} n'est pas correct \"{$this->params[$bool]}\" (valeurs possibles: true et false)" );
				}
			}

			$models = App::objects( 'models');
			sort( $models );

			foreach( $models as $modelName ) {
				if( $modelName !== 'AppModel' ) {
					$Model = ClassRegistry::init( $modelName );

					if( $Model->useTable !== false ) {
						$foo = (
							empty( $this->params['tables'] )
							|| ( !empty( $this->params['tables'] ) && preg_match( $this->params['tables'], $Model->useTable ) )
						);

						if( $foo ) {
							$this->_tables[] = $Model->useTable; // FIXME: traduction tables/modèles
							$this->_models[] = $Model->alias;
						}
					}
				}
			}

			$this->_tables = array_unique( $this->_tables );
			$this->_models = array_unique( $this->_models );

			// Si aucune table à traiter, fin du shell
			if( empty( $this->_tables ) ) {
				$this->out( "<info>Aucune table à traiter</info>" );
				$this->_stop( self::SUCCESS );
			}
		}

		/**
		 *
		 * @param string $modelName
		 * @return string
		 */
		protected function _graphvizFormatTable( $modelName ) {
			$tableName = Inflector::tableize( $modelName );
			$label = $tableName;

			if( !empty( $this->params['fields'] ) ) {
				$schema = ClassRegistry::init( $modelName )->schema();
				$fields = array();
				foreach( $schema as $fieldName => $fieldParams ) {
					$fields[] = "{$fieldName}: {$fieldParams['type']}";
				}
				$label = '{'.$label.'|'.implode( '\l', $fields ).'\l}';
			}

			$return = "\t\"{$tableName}\" [label=\"{$label}\", shape=record];\n";

			return $return;
		}

		/**
		 *
		 * @param string $modelName
		 * @param array $association
		 * @return string
		 */
		public function _graphvizFormatAssociation( $modelName, array $association ) {
			$tableName1 = Inflector::tableize( $modelName );
			$tableName2 = Inflector::tableize( $association['className'] );
			$alias = Inflector::tableize( $association['alias'] );

			$dir = 'forward';
			$arrowhead = 'normal';
			$foreignKey = null;
			$taillabel = null;
			$headlabel = null;

			if( $association['association'] == 'belongsTo' ) {
				$foreignKey = $association['foreignKey'];
				$taillabel = '0,1';
				$headlabel = '0,n';

				// Notre label
				$Model = ClassRegistry::init( $modelName );
				$foreignKeySchema = (array)$Model->schema( $foreignKey );
				if( Hash::get( $foreignKeySchema, 'null' ) === false ) {
					$taillabel = '1,1';
					$headlabel = '1,n';
				}
			}
			else if( $association['association'] == 'hasOne' ) {
				// FIXME: Table collectivites for model Collectivite was not found in datasource default.
				$foreignKey = $association['foreignKey'];
				$taillabel = '0,1';
				$headlabel = '0,n';
				$dir = 'back';

				// Notre label
				$Model = ClassRegistry::init( $association['className'] );
				$foreignKeySchema = (array)$Model->schema( $foreignKey );
				if( Hash::get( $foreignKeySchema, 'null' ) === false ) {
					$taillabel = '1,1';
					$headlabel = '1,n';
				}
			}
			else if( $association['association'] == 'hasMany' ) {
				$foreignKey = $association['foreignKey'];
				$taillabel = '0,1';
				$headlabel = '0,n';
				$dir = 'back';

				// Notre label
				$Model = ClassRegistry::init( $modelName );
				$foreignKeySchema = (array)$Model->schema( $foreignKey );
				if( Hash::get( $foreignKeySchema, 'null' ) === false ) {
					$taillabel = '1,1';
					$headlabel = '1,n';
				}
			}

			// Normalisation du nom de l'association
			if( $dir == 'back' ) {
				$key = "{$tableName2}\" -> \"{$tableName1} ($foreignKey)";
			}
			else {
				$key = "{$tableName1}\" -> \"{$tableName2} ($foreignKey)";
			}

			if( !in_array( $key, $this->_formattedAssociations ) ) {
				$this->_formattedAssociations[] = $key;

				// TODO: si l'alias est le nom de la table...
				$return = "\t\"{$tableName1}\" -> \"{$tableName2}\" [dir=\"{$dir}\", taillabel=\"{$taillabel}\", headlabel=\"{$headlabel}\", label=\"{$foreignKey}\", arrowhead=\"{$arrowhead}\"];\n";
			}
			else {
				$return = '';
			}

			return $return;
		}

		/**
		 *
		 * @return string
		 */
		protected function _graphvizToGraph() {
			$tables = '';
			$associations = '';

			foreach( $this->_associations as $modelName => $associationList ) {
				$this->out( sprintf( 'Écriture de la table %s', Inflector::tableize( $modelName ) ) );
				$tables .= $this->_graphvizFormatTable( $modelName );

				foreach( $associationList as $associationParams ) {
					$associations .= $this->_graphvizFormatAssociation( $modelName, $associationParams );
				}
			}

			$contents = "digraph G { ".$this->_graphvizFormatParams( $this->digraphParams )." \n \tnode [\n".$this->_graphvizFormatParams( $this->nodeParams )."\n\t]\n \n \tedge [\n".$this->_graphvizFormatParams( $this->edgeParams )."\n \t]\n\n";
			$contents .= "{$tables}\n";
			$contents .= "{$associations}\n";
			$contents .= "\n}";
			return $contents;
		}

		/**
		 *
		 * @return string
		 */
		public function toGraph() {
			return $this->_graphvizToGraph();
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			foreach( $this->_tables as $table ) {
				$this->out( sprintf( 'Lecture de la table %s', $table ) );

				$modelName = Inflector::classify( $table );
				$Model = ClassRegistry::init( $modelName );

				// Lecture des liaisons avec les autres modèles
				foreach( $Model->associations() as $association ) {
					if( $association !== 'hasAndBelongsToMany' ) {
						foreach( (array)$Model->{$association} as $associationAlias => $associationParams ) {
							if( in_array( $associationParams['className'], $this->_models ) ) {
								$associationParams['alias'] = $associationAlias;
								$associationParams['association'] = $association;
								$this->_associations[$Model->name][] = $associationParams;
							}
						}
					}
					else {
						foreach( (array)$Model->{$association} as $associationAlias => $associationParams ) {
							if( in_array( $associationParams['with'], $this->_models ) ) {
								$this->_associations[$Model->name][] = array(
									'className' => $associationParams['with'],
									'alias' => $associationParams['with'],
									'foreignKey' => $associationParams['foreignKey'],
									'dependent' => true,
									'conditions' => null,
									'fields' => null,
									'order' => null,
									'limit' => null,
									'offset' => null,
									'exclusive' => null,
									'finderQuery' => null,
									'counterQuery' => null,
									'association' => 'hasMany'
								);
							}
							if( in_array( $associationParams['className'], $this->_models ) ) {
								$this->_associations[$associationParams['className']][] = array(
									'className' => $associationParams['with'],
									'alias' => $associationParams['with'],
									'foreignKey' => $associationParams['associationForeignKey'],
									'dependent' => true,
									'conditions' => null,
									'fields' => null,
									'order' => null,
									'limit' => null,
									'offset' => null,
									'exclusive' => null,
									'finderQuery' => null,
									'counterQuery' => null,
									'association' => 'hasMany'
								);
							}
						}
					}
				}
			}

			file_put_contents( 'graphviz_mpd.dot', $this->toGraph() );
		}

		/**
		 * Ajout des options et paramètres au shell.
		 *
		 * @return ConsoleOptionParser
		 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			$Parser->description( "Ce shell ..." );

			$options = array(
				'connection' => array(
					'short' => 'c',
					'help' => 'Le schéma à prendre en compte',
					'default' => 'default'
				),
				'tables' => array(
					'short' => 't',
					'help' => 'Permet de préciser, au moyen d\'une expression régulière, la liste des tables à prendre en compte.',
					'default' => null
				),
				'fields' => array(
					'short' => 'f',
					'help' => 'Permet de spécifier si on veut la liste des champs ainsi que leur type',
					'choices' => array( 'true', 'false' ),
					'default' => 'false'
				),
			);
			$Parser->addOptions( $options );

			return $Parser;
		}
	}
?>