<?php
	/**
	 * Code source de la classe CsvAbstractImporterShell.
	 *
	 * PHP 5.3
	 *
	 * @package Csv
	 * @subpackage Console.Command.Abstract
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppShell', 'Console/Command' );
	App::uses( 'CsvFileReader', 'Csv.Utility' );

	/**
	 * La classe CsvAbstractImporterShell ...
	 *
	 * @package Csv
	 * @subpackage Console.Command.Abstract
	 */
	abstract class CsvAbstractImporterShell extends AppShell
	{
		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 *
		 * @var string
		 */
		public $description = null;

		/**
		 *
		 * @var array
		 */
		public $options = array(
			'headers' => array(
				'short' => 'H',
				'help' => 'précise si le fichier à importer commence par une colonne d\'en-tête ou s\'il commence directement par des données à intégrér',
				'choices' => array( 'true', 'false' ),
				'default' => 'true'
			),
			'separator' => array(
				'short' => 's',
				'help' => 'le caractère utilisé comme séparateur',
				'default' => ','
			),
			'delimiter' => array(
				'short' => 'd',
				'help' => 'le caractère utilisé comme délimiteur de champ',
				'default' => '"'
			),
		);

		/**
		 *
		 * @var array
		 */
		public $arguments = array(
			'csv' => array(
				'help' => 'Le chemin vers le fichier CSV à importer',
				'required' => true
			)
		);

		/**
		 *
		 * @var array
		 */
		protected $_headers = array();

		/**
		 *
		 * @var array
		 */
		protected $_defaultHeaders = array();

		/**
		 *
		 * @var CsvFileReader
		 */
		protected $_Csv = null;

		/**
		 * Lignes rejetées.
		 *
		 * @var array
		 */
		public $rejects = array();

		/**
		 * Lignes vides.
		 *
		 * @var array
		 */
		public $empty = array();

		/**
		 * Surcharge du constructeur permettant de compléter les attributs
		 * "options" et "arguments".
		 *
		 * @param ConsoleOutput $stdout
		 * @param ConsoleOutput $stderr
		 * @param ConsoleInput $stdin
		 */
		public function __construct( $stdout = null, $stderr = null, $stdin = null ) {
			parent::__construct( $stdout, $stderr, $stdin );

			$parent = get_parent_class( $this );
			$vars = array();

			if( $this->options !== null && $this->options !== false ) {
				$vars[] = 'options';
			}

			if( $this->arguments !== null && $this->arguments !== false ) {
				$vars[] = 'arguments';
			}

			if( !empty( $vars ) ) {
				$this->_mergeVars( $vars, $parent, true );
			}
		}

		/**
		 * Nettoyage et normalisation de la ligne d'en-tête.
		 *
		 * @param array $headers
		 * @return array
		 */
		public function processHeaders( array $headers ) {
			foreach( $headers as $key => $value ) {
				$headers[$key] = trim( $value );
			}

			return $headers;
		}

		/**
		 * Vérification de la ligne d'en-tête.
		 */
		public function checkHeaders() {
			$defaultHeaders = $this->processHeaders( $this->_defaultHeaders );

			$missing = array_diff( $defaultHeaders, $this->_headers );
			if( !empty( $missing ) ) {
				$this->error( sprintf( "En-têtes de colonnes manquants: %s", implode( ',', $missing ) ) );
			}

			$found = array_diff( $this->_headers, $defaultHeaders );
			if( !empty( $found ) ) {
				$msgstr = 'En-têtes de colonnes supplémentaires: %s';
				$this->out( sprintf( __d( 'cake_console', '<success>Warning:</success> %s', sprintf( $msgstr, implode( ',', $found ) ) ) ) );
			}
		}

		/**
		 * Lecture de la ligne d'en-tête.
		 */
		public function parseHeaders() {
			if( $this->params['headers'] ) {
				$this->_headers = $this->processHeaders( $this->_Csv->headers() );
			}
			else {
				$this->_headers = $this->_defaultHeaders;
			}
		}

		/**
		 * Rejet d'une ligne, ajout de la description de l'erreur dans une colonne
		 * supplémentaire, ajout de la ligne dans les lignes rejetées.
		 *
		 * @param array $row
		 * @param Model $Model
		 * @param string $msgstr
		 */
		public function rejectRow( array $row, $Model = null, $msgstr = null ) {
			if( $msgstr !== null ) {
				$row[] = $msgstr;
			}
			else {
				$errors = array();

				foreach( $Model->validationErrors as $field => $values ) {
					foreach( $values as $msgstr ) {
						$path = "{$Model->alias}.{$field}";
						$key = array_search( $path, $this->_correspondances );
						$column = $this->_headers[$key];

						$errors[] = "{$column}: {$msgstr}";
					}
				}

				$row[] = implode( '; ', $errors );
			}

			$this->rejects[] = $row;
		}

		/**
		 * Transforme un array en ligne CSV.
		 *
		 * @param array $input
		 * @return string
		 */
		public function toCsvLine( array $input ) {
			$escape = '\\';

			foreach( $input as $key => $value ) {
				$input[$key] = $this->params['delimiter'].str_replace( $this->params['delimiter'], "{$escape}{$this->params['delimiter']}", $value ).$this->params['delimiter'];
			}

			return implode( $this->params['separator'], $input );
		}

		/**
		 * Traitement du fchier de rejets.
		 */
		public function logRejects() {
			$filename = LOGS.$this->name.'_'.preg_replace( '/\.csv$/i', '_rejects', basename( $this->args[0] ) ).'_'.date( 'Ymd-His' ).'.csv';
			$File = new File( $filename, true );

			$headers = $this->_Csv->headers();
			$headers[] = 'Erreur(s)';
			$content = array(
				$this->toCsvLine( $headers )
			);

			foreach( $this->rejects as $reject ) {
				$content[] = $this->toCsvLine( $reject );
			}

			$File->write( implode( "\n", $content ) );
			$File->close();

			$msgstr = "<info>Le fichier de rejets se trouve dans</info> %s";
			$this->out( sprintf( $msgstr, $this->shortPath( $File->pwd() ) ) );
		}

		/**
		 * Traitement d'une ligne de données du fichier CSV.
		 *
		 * @param array $row
		 * @return boolean
		 */
		abstract public function processRow( array $row );

		/**
		 * Epilogue du traitement du fichier CSV, écriture des rejets.
		 */
		public function epilog() {
			$count = $this->_Csv->count();
			$rejects = count( $this->rejects );
			$empty = count( $this->empty );

			$msgstr = "Fin du traitement du fichier %s: %d ligne(s) à traiter, %d ligne(s) traitées, %d ligne(s) rejetées, %d ligne(s) vides";
			$message = sprintf( $msgstr, $this->args[0], $count, $count - $rejects - $empty, $rejects, $empty );

			$this->out();
			if( empty( $rejects ) ) {
				$this->out( __d( 'cake_console', '<success>Success:</success> %s', $message ) );
				$this->_stop( self::SUCCESS );
			}
			else {
				$this->err( __d( 'cake_console', '<error>Error:</error> %s', $message ) );
				$this->logRejects();
				$this->_stop( self::ERROR );
			}
		}

		/**
		 * Démarrage du shell, lecture du fichier CSV, vérification des paramètres.
		 */
		public function startup() {
			parent::startup();

			$this->params['headers'] = ( $this->params['headers'] === 'true' );

			try {
				$path = Hash::get( $this->args, '0' );
				$params = array(
					'separator' => $this->params['separator'],
					'delimiter' => $this->params['delimiter'],
					'headers' => $this->params['headers']
				);

				$this->_Csv = new CsvFileReader( $path, $params );

				$this->parseHeaders();
				$this->checkHeaders();

				// Fin du traitement si on n'a aucune ligne à traiter
				if( $this->_Csv->count() === 0 ) {
					$this->out( '<info>Aucune ligne à traiter</info>' );
					$this->_stop( self::SUCCESS );
				}
			} catch( RuntimeException $Exception ) {
				$this->error( $Exception->getMessage() );
			}
		}


		/**
		 * Méthode principale, traitement du fichier CSV.
		 */
		public function main() {
			foreach( $this->_Csv as $row ) {
				$this->processRow( $row );
			}

			$this->epilog();
		}

		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();

			if( $this->description !== null ) {
				$Parser->description( $this->description );
			}

			$Parser->addOptions( $this->options );

			$Parser->addArguments( $this->arguments );

			return $Parser;
		}
	}
?>