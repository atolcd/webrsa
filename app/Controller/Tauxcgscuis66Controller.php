<?php
	/**
	 * Code source de la classe Tauxcgscuis66Controller.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Tauxcgscuis66Controller s'occupe du paramétrage des taux de prise
	 * en charge du CUI pour le CG 66.
	 *
	 * @package app.Controller
	 */
	class Tauxcgscuis66Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tauxcgscuis66';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Tauxcgcui66' );

		/**
		 * Surcharge de la liste des taux de prise en charge pour envoyer les
		 * options depuis la méthode options du modèle.
		 */
		public function index() {
			$this->WebrsaParametrages->index();
			$this->set( 'options', $this->Tauxcgcui66->options() );
		}

		/**
		 * Surcharge du formulaire de modification d'un taux de prise en charge
		 * pour ajouter les valeurs par défaut et pour envoyer les options depuis
		 * la méthode options du modèle.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			if( true === empty( $this->request->data ) ) {
				$this->request->data = array(
					'Tauxcgcui66' => array(
						'tauxfixeregion' => '0',
						'priseenchargeeffectif' => '0',
						'tauxcg' => '0'
					)
				);
			}

			$this->set( 'options', $this->Tauxcgcui66->options() );
		}
	}
?>
