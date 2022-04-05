<?php
	/**
	 * Code source de la classe StructuresreferentesTypesorientsZonesgeographiquesController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe StructuresreferentesTypesorientsZonesgeographiquesController s'occupe du paramétrage des passages
	 * en commission des rendez-vous.
	 *
	 * @package app.Controller
	 */
	class StructuresreferentesTypesorientsZonesgeographiquesController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'StructuresreferentesTypesorientsZonesgeographiques';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'StructurereferenteTypeorientZonegeographique', 'Typeorient', 'Zonegeographique', 'Structurereferente' );

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = ['add', 'delete'];

		/**
		 * Liste des structures réferentes par zone geographique et type d'orientation
		 */
		public function index() {

			$typesorients = $this->Typeorient->listTypeEnfant();
			$villes = $this->Zonegeographique->find('list', ['fields' => 'libelle', 'conditions' => ['codeinsee ilike' => '93%']]);
			$structuresreferentes = $this->StructurereferenteTypeorientZonegeographique->tableauIndex($villes, $typesorients);

			$this->set( compact( 'typesorients', 'villes', 'structuresreferentes' ) );
		}

		/**
		 * Formulaire de modification d'une structure réferente par zone geographique et type d'orientation
		 * @param integer $id
		 */
		public function edit($id = null) {
			if( false === empty( $this->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( '/StructuresreferentesTypesorientsZonesgeographiques/index' );
				}
				//on enregistre
				$zonegeographique_id = $id;
				$data = [];
				foreach($this->request->data['StructurereferenteTypeorientZonegeographique'] as $typeorient_id => $structurereferente_id){
					//on regarde si ligne de jointure déjà présente dans la table
					$lien = $this->StructurereferenteTypeorientZonegeographique->findByZonegeographiqueIdAndTypeorientId($zonegeographique_id, $typeorient_id);
					//on regarde si il y a une valeur à enregistrer
					if($structurereferente_id != ''){
						if($lien != []){
							//on regarde si c'est la même valeur
							if($lien['Structurereferente']['id'] != $structurereferente_id)
							{
								//on update
								$data[] = [
									'id' => $lien['StructurereferenteTypeorientZonegeographique']['id'],
									'structurereferente_id' => $structurereferente_id,
								];
							}
						} else {
							// on ajoute
							$data[] = [
								'structurereferente_id' => $structurereferente_id,
								'typeorient_id' => $typeorient_id,
								'zonegeographique_id' => $zonegeographique_id,
							];
						}
					} else {
						if($lien != []){
							//on supprime
							$this->StructurereferenteTypeorientZonegeographique->delete($lien['StructurereferenteTypeorientZonegeographique']['id']);
						}
					}

				}
				if($data != []){
					$success = $this->StructurereferenteTypeorientZonegeographique->saveMany($data);
					if($success){
						$this->Flash->success( __( 'Save->success' ) );
						$this->redirect( '/StructuresreferentesTypesorientsZonesgeographiques/index' );
					} else {
						$this->Flash->error( __( 'Save->error' ) );
					}
				} else {
					$this->redirect( '/StructuresreferentesTypesorientsZonesgeographiques/index' );
				}
			}

			$listeStructuresreferentes = $this->Structurereferente->find('list');
			$typesorients = $this->Typeorient->listTypeEnfant();
			$nomVille = $this->Zonegeographique->findById($id)['Zonegeographique']['libelle'];

			//on récupère les infos enregistrées sur la ville
			$structuresreferentes = $this->StructurereferenteTypeorientZonegeographique->tableauIndexVille($id, $typesorients);
			$this->set( compact( 'typesorients', 'structuresreferentes', 'listeStructuresreferentes', 'nomVille') );


		}
	}
?>
