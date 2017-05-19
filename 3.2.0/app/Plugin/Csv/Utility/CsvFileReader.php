<?php
	/**
	 * Code source de la classe CsvFileReader.
	 *
	 * PHP 5.3
	 *
	 * @package Csv
	 * @subpackage Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'File', 'Utility' );

	/**
	 * La classe CsvFileReader ...
	 *
	 * @package Csv
	 * @subpackage Utility
	 */
	class CsvFileReader implements Iterator, Countable
	{
		/**
		 * Le fichier CSV.
		 *
		 * @var File
		 */
		protected $_File = null;

		/**
		 *
		 * @var string
		 */
		protected $_content = null;

		/**
		 *
		 * @var array
		 */
		protected $_lines = array();

		/**
		 *
		 * @var array
		 */
		protected $_records = array();

		/**
		 *
		 * @var string
		 */
		protected $_separator = null;

		/**
		 *
		 * @var string
		 */
		protected $_delimiter = null;

		/**
		 *
		 * @var string
		 */
		protected $_headers = null;

		/**
		 * L'array de paramètres par défaut du constructeur.
		 *
		 * @var array
		 */
		protected $_defaults = array(
			'separator' => ',',
			'delimiter' => '",',
			'encoding' => null,
			'headers' => false,
		);

		/**
		 *
		 * @param string $path
		 * @throws RuntimeException
		 */
		protected function _loadFile( $path ) {
			$this->_File = new File( $path );

			if( !$this->_File->exists() ) {
				$msgstr = 'Le fichier "%s" n\'existe pas';
				throw new RuntimeException( sprintf( $msgstr, $this->_File->pwd() ) );
			}
			else if( !$this->_File->readable() ) {
				$msgstr = 'Le fichier "%s" n\'est pas lisible';
				throw new RuntimeException( sprintf( $msgstr, $this->_File->pwd() ) );
			}

			$this->_content = $this->_File->read();
		}

		/**
		 *
		 */
		protected function _parseContent() {
			$this->_lines = explode( "\n", $this->_toUtf8( $this->_content ) );

			foreach( $this->_lines as $key => $line ) {
				$line = trim( $line );
				$row = str_getcsv( $line, $this->_separator, $this->_delimiter );

				if( $line === '' || empty( $row ) ) {
					$row = array();
				}

				$this->_records[$key] = $row;
			}
		}

		/**
		 *
		 * @todo array de paramètres: separator, delimiter, encoding, ... (+ headers / attributs public ?)
		 *
		 * @param string $path
		 * @param array $params
		 */
		public function __construct( $path, array $params = array() ) {
			$params += $this->_defaults;

			$this->_separator = $params['separator'];
			$this->_delimiter = $params['delimiter'];
			$this->_headers = $params['headers'];

			$this->_loadFile( $path );
			$this->_parseContent();


			$this->rewind();
		}

		/**
		 *
		 * @param string $string
		 * @return string
		 */
		protected function _toUtf8( $string ) {
			mb_detect_order( array( 'UTF-8', 'ISO-8859-1', 'ASCII' ) );
			$encoding = mb_detect_encoding( $string );

			$appEncoding = Configure::read( 'App.encoding' );
			if( $encoding !== $appEncoding ) {
				$string = mb_convert_encoding( $string, $appEncoding, $encoding );
			}

			return $string;
		}

		/**
		 *
		 */
		public function rewind() {
			$this->_position = 0;
		}

		/**
		 *
		 * @return integer
		 */
		protected function _realKey() {
			return ( $this->_position + ( $this->_headers ? 1 : 0 ) );
		}

		/**
		 *
		 * @return array
		 */
		public function current() {
			return $this->_records[$this->_realKey()];
		}

		/**
		 *
		 * @return integer
		 */
		public function key() {
			return $this->_position;
		}

		/**
		 *
		 */
		public function next() {
			++$this->_position;
		}

		/**
		 *
		 * @return boolean
		 */
		public function valid() {
			return isset( $this->_records[$this->_realKey()] );
		}

		/**
		 *
		 * @return integer
		 */
		public function count() {
			return max( count( $this->_records ) - ( $this->_headers ? 1 : 0 ), 0 );
		}

		/**
		 *
		 * @return integer
		 */
		public function headers() {
			if( $this->_headers && count( $this->_records ) > 0 ) {
				return $this->_records[0];
			}

			return array();
		}
	}
?>