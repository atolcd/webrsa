<?php
	/**
	 * Code source de la classe Listedecisionssuspensionseps93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Listedecisionssuspensionseps93Controller s'occupe du paramétrage des
	 * motifs des demandes de réorientation à passer en EP.
	 *
	 * @package app.Controller
	 */
	class Listedecisionssuspensionseps93Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Listedecisionssuspensionseps93';

		/**
		 * Formulaire de modification d'un élément.
		 *
		 * @todo final
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$decision = $this->Listedecisionsuspensionsep93->findById($id);
			$code = $decision['Listedecisionsuspensionsep93']['code'];
			$courrier = $decision['Listedecisionsuspensionsep93']['nom_courrier'];
			$this->set(compact('code', 'courrier'));

			if( false === empty( $this->request->data ) ) {
				if(isset($this->request->data['Listedecisionsuspensionsep93'])){
					$actif = (
						$this->request->data['Listedecisionsuspensionsep93']['premier_niveau'] == '1' 
						|| $this->request->data['Listedecisionsuspensionsep93']['deuxieme_niveau'] == '1'
					);
					$this->request->data['Listedecisionsuspensionsep93']['actif'] = $actif;
				}
			}

			parent::edit($id);
		}

	}
?>