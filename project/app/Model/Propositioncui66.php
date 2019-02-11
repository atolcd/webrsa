<?php
	/**
	 * Fichier source de la classe Propositioncui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractAppModelLieCui66', 'Model/Abstractclass' );

	/**
	 * La classe Propositioncui66 est la classe contenant les avis techniques du CUI pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Propositioncui66 extends AbstractAppModelLieCui66
	{
		/**
		 * Alias de la table et du model
		 * @var string
		 */
		public $name = 'Propositioncui66';

		/**
		 *
		 * @var array
		 */
		public $validate = array(
			'motif' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'avis', true, array( 'refus', 'avisreserve' ) )
				)
			)
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			'default' => 'CUI/%s/impression.odt',
			'aviselu' => 'CUI/%s/aviselu.odt',
		);

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param array $params <=> array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();

			$optionRefus = $this->enums();
			$optionRefus['Propositioncui66']['motif'] = ClassRegistry::init( 'motifrefuscui66' )->find( 'list' );

			$options = Hash::merge(
				$options,
				$optionRefus
			);

			return $options;
		}
	}
?>