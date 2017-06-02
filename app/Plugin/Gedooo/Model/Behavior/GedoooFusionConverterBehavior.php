<?php
	/**
	 * Fichier source de la classe GedoooFusionConverterBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	App::import( 'Behavior', 'Gedooo.GedoooClassic' );

	/**
	 * La classe GedoooFusionConverterBehavior permet de générer un fichier PDF
	 * grâce à la classe GedoooClassicBehavior et au passage dans la méthode
	 * de conversion gedConversion() des classes descendantes.
	 *
	 * Cette classe est utilisée comme classe parente d'autres behaviors.
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	abstract class GedoooFusionConverterBehavior extends GedoooClassicBehavior
	{
		/**
		 * Setup this behavior with the specified configuration settings.
		 *
		 * @param Model $model Model using this behavior
		 * @param array $config Configuration settings for $model
		 * @return void
		 */
		public function setup( Model $model, $config = array() ) {
			Configure::WRITE( 'GEDOOO_WSDL', GEDOOO_WSDL );
		}

		/**
		 *
		 * @param AppModel $model
		 * @param type $datas
		 * @param type $document
		 * @param type $section
		 * @param type $options
		 * @return type
		 */
		public function gedFusion( Model $model, $datas, $document, $section = false, $options = array() ) {
			return parent::ged( $model, $datas, $document, $section, $options );
		}

		/**
		 * @param AppModel $model
		 * @param string $fileName
		 * @param string $format
		 */
		abstract public function gedConversion( Model $model, $fileName, $format );

		/**
		 *
		 * @param AppModel $model
		 * @param array $datas
		 * @param string $document
		 * @param boolean $section
		 * @param array $options
		 * @return string
		 */
		public function ged( Model $model, $datas, $document, $section = false, $options = array() ) {
			Configure::write( 'GEDOOO_WSDL', GEDOOO_WSDL ); // FIXME ?

			$odt = $this->gedFusion( $model, $datas, $document, $section, $options );
			if( empty( $odt ) ) {
				return false;
			}

			$fileName = tempnam( TMP, $document );
			if( $fileName === false ) {
				return false;
			}
			chmod( $fileName, 0775 );

			$success = file_put_contents( $fileName, $odt );
			if( $success === false ) {
				return false;
			}

			$pdf = $this->gedConversion( $model, $fileName, 'pdf' );

			$success = unlink( $fileName );
			if( $success === false || empty( $pdf ) ) {
				return false;
			}

			return $pdf;
		}

		/**
		 * Retourne la liste des clés de configuration.
		 *
		 * @return array
		 */
		public function gedConfigureKeys( Model $model ) {
			return array(
				'Gedooo.wsdl' => 'string',
			);
		}
	}
?>