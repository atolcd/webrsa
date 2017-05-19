<?php
	/**
	 * Fichier source de la classe GedoooUnoconvBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	App::uses( 'GedoooFusionConverterBehavior', 'Gedooo.Model/Behavior' );

	/**
	 * La classe GedoooUnoconvBehavior fournit une méthode de conversion de
	 * document ODT au format PDF en utilisant le binaire unoconv.
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	class GedoooUnoconvBehavior extends GedoooFusionConverterBehavior
	{
		/**
		 *
		 * @param string $fileName
		 * @param string $format
		 * @return string
		 */
		public function gedConversion( Model $model, $fileName, $format ) {
			// lecture fichier exécutable de unoconv
			$convertorExec = Configure::read( 'Gedooo.unoconv_bin' );
			if( empty( $convertorExec ) ) {
				return false;
			}
			// exécution
			$fileName = escapeshellarg( $fileName );
			$cmd = "LANG=fr_FR.UTF-8; $convertorExec -f {$format} --stdout {$fileName}";
			$result = shell_exec( $cmd );

			// guess that if there is less than this characters probably an error
			if( strlen( $result ) < 10 ) {
				return false;
			}
			else {
				return ($result);
			}
		}

		/**
		 * Retourne la liste des clés de configuration.
		 *
		 * @return array
		 */
		public function gedConfigureKeys( Model $model ) {
			return array_merge(
				parent::gedConfigureKeys( $model ),
				array(
					'Gedooo.unoconv_bin' => 'string',
				)
			);
		}

		/**
		 * @return array
		 */
		public function gedTests( Model $model ) {
			App::uses( 'Check', 'Appchecks.Model' );
			$Check = ClassRegistry::init( 'Appchecks.Check' );

			$results = parent::gedTests( $model );

			return array_merge(
				$results,
				$Check->binaries( (array)Configure::read( 'Gedooo.unoconv_bin' ) )
			);
		}
	}
?>