<?php
	Configure::write( 'Routing.prefixes', array( 'admin' ) );

	// TODO: documentation, etc...
	App::uses( 'CsvHelper', 'View/Helper' );

	class CsvTestHelper extends CsvHelper
	{
		/**
		 * Surcharge de la méthode CsvHelper::renderHeaders() afin de ne pas
		 * envoyer le fichier en attachement.
		 *
		 * @param string $filename
		 */
		public function renderHeaders($filename = null) {
			if (is_string($filename)) {
				$this->setFilename($filename);
			}

			if ($this->filename === null) {
				$this->filename = 'Data.csv';
			}
		}
	}

	// TODO: documentation, etc...
	abstract class DefaultAbstractTestCase extends CakeTestCase
	{
		protected static function _normalizeXhtml( $xhtml ) {
			$xhtml = preg_replace( "/([[:space:]]|\n)+/m", ' ', $xhtml );
			$xhtml = str_replace( '> <', '><', $xhtml );
			return trim( $xhtml );
		}

		public static function assertEquals( $expected, $result, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false ) {
			if( empty( $message ) ) {
				$message = var_export( $result, true );
			}

			return parent::assertEquals(
				$expected,
				$result,
				$message,
				$delta,
				$maxDepth,
				$canonicalize,
				$ignoreCase
			);
		}

		public static function assertEqualsXhtml( $expected, $result, $message = '' ) {
			return self::assertEquals(
				self::_normalizeXhtml( $expected ),
				self::_normalizeXhtml( $result )
			);
		}
	}
?>
