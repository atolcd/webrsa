<?php
	/**
	 * Fichier source de la classe Suspensioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractAppModelLieCui66', 'Model/Abstractclass' );

	/**
	 * La classe Suspensioncui66 est la classe contenant les avis techniques du CUI pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Suspensioncui66 extends AbstractAppModelLieCui66
	{
		/**
		 * Alias de la table et du model
		 * @var string
		 */
		public $name = 'Suspensioncui66';
		
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
				$result['Suspensioncui66']['duree'] = 'journee';
			}

			return $result;
		}
				
		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param array $params <=> array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			
			$optionSuspension = $this->enums();
			$optionSuspension['Suspensioncui66']['motif'] = ClassRegistry::init( 'Motifsuspensioncui66' )->find( 'list' );
			$optionSuspension['Suspensioncui66']['motif_actif'] = ClassRegistry::init( 'Motifsuspensioncui66' )->find( 'list', array( 'conditions' => array( 'actif' => true ) ) );
			

			$options = Hash::merge(
				$options,
				$optionSuspension
			);
			
			return $options;
		}
	}
?>