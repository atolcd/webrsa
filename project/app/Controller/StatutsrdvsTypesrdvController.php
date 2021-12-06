<?php
	/**
	 * Code source de la classe StatutsrdvsTypesrdvController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe StatutsrdvsTypesrdvController s'occupe du paramétrage des passages
	 * en commission des rendez-vous.
	 *
	 * @package app.Controller
	 */
	class StatutsrdvsTypesrdvController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'StatutsrdvsTypesrdv';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'StatutrdvTyperdv' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'StatutsrdvsTypesrdv:edit',
		);

		/**
		 * Liste des passages en commission des rendez-vous.
		 */
		public function index() {
			if( false === $this->StatutrdvTyperdv->Behaviors->attached( 'Occurences' ) ) {
				$this->StatutrdvTyperdv->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->StatutrdvTyperdv->fields(),
					array(
						$this->StatutrdvTyperdv->sqHasLinkedRecords( true ),
						'Statutrdv.libelle',
						'Typerdv.libelle'
					)
				),
				'joins' => array(
					$this->StatutrdvTyperdv->join( 'Statutrdv', array( 'type' => 'INNER' ) ),
					$this->StatutrdvTyperdv->join( 'Typerdv', array( 'type' => 'INNER' ) )
				),
				'order' => array(
					'Statutrdv.libelle',
					'Typerdv.libelle',
					'StatutrdvTyperdv.typecommission'
				)
			);
			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un passage en commission des rendez-vous.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$options = $this->viewVars['options'];
			//on affiche uniquement les statuts actifs qui peuvent provoquer une action
			$options['StatutrdvTyperdv']['statutrdv_id'] = $this->StatutrdvTyperdv->Statutrdv->find(
				 'list',
				 array(
					'conditions' => array(
						'provoquepassagecommission' => '1',
						'actif' => '1',
					)
				)
			);
			$options['StatutrdvTyperdv']['typerdv_id'] = $this->StatutrdvTyperdv->Typerdv->find( 'list' );
			$this->set( compact( 'options' ) );
		}
	}
?>
