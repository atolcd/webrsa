<?php
	/**
	 * Fichier source de la classe Rupturecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractAppModelLieCui66', 'Model/Abstractclass' );

	/**
	 * La classe Rupturecui66 est la classe contenant les avis techniques du CUI pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Rupturecui66 extends AbstractAppModelLieCui66
	{
		/**
		 * Alias de la table et du model
		 * @var string
		 */
		public $name = 'Rupturecui66';
		
		/**
		 * Récupère les donnés par defaut dans le cas d'un ajout, ou récupère les données stocké en base dans le cas d'une modification
		 * 
		 * @param integer $cui66_id
		 * @param integer $id
		 * @return array
		 */
		public function prepareAddEditFormData( $cui66_id, $id = null ) {
			$result = parent::prepareAddEditFormData($cui66_id, $id);
			
			// Ajout
			if( empty( $id ) ) {
				$result['Rupturecui66']['dateenregistrement'] = date_format(new DateTime(), 'Y-m-d');
			}

			return $result;
		}
				
		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @return array
		 */
		public function options() {			
			$options = $this->enums();
			$options['Rupturecui66']['motif'] = ClassRegistry::init( 'Motifrupturecui66' )->find( 'list' );
			$options['Rupturecui66']['motif_actif'] = ClassRegistry::init( 'Motifrupturecui66' )->find( 'list', array( 'conditions' => array( 'actif' => true ) ) );
			
			return $options;
		}
	}
?>