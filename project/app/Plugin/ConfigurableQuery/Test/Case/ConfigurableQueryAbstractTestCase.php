<?php
	// TODO: documentation, etc...
	abstract class ConfigurableQueryAbstractTestCase extends CakeTestCase
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

		public static function assertEqualsXhtml( $result, $expected, $message = '' ) {
			return self::assertEquals(
				self::_normalizeXhtml( $expected ),
				self::_normalizeXhtml( $result )
			);
		}
	}
?>
